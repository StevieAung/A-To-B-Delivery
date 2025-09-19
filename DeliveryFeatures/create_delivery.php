<?php
session_start();
include '../Database/db.php';
$alert = "";

// Redirect if not logged in or not sender
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sender') {
    header("Location: ../login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pickup_location   = trim($_POST['pickup_location']);
    $delivery_location = trim($_POST['drop_location']);
    $item_description  = trim($_POST['item_description']);
    $weight            = trim($_POST['weight']);
    $is_fragile        = isset($_POST['fragile']) ? 1 : 0;
    $preferred_time    = !empty($_POST['preferred_time']) ? $_POST['preferred_time'] : null;
    $delivery_mode     = trim($_POST['delivery_mode']);
    $pickup_lat        = $_POST['pickup_lat'] ?? null;
    $pickup_long       = $_POST['pickup_lng'] ?? null;
    $delivery_lat      = $_POST['drop_lat'] ?? null;
    $delivery_long      = $_POST['drop_lng'] ?? null;
    // Validate weight
    if (!is_numeric($weight) || $weight <= 0) {
        $alert = '<div class="alert alert-danger">Invalid weight. Please enter a positive number.</div>';
    } elseif (empty($pickup_lat) || empty($pickup_long) || empty($delivery_lat) || empty($delivery_long)) {
        $alert = '<div class="alert alert-danger">Pickup and Drop locations are required.</div>';
    } else {
        $stmt = $conn->prepare("INSERT INTO delivery_requests
            (user_id, pickup_location, delivery_location, item_description, weight, is_fragile, preferred_time, delivery_mode, pickup_lat, pickup_long, delivery_lat, delivery_long)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Check if prepare() failed
        if ($stmt === false) {
            $alert = '<div class="alert alert-danger">Prepare failed: ' . htmlspecialchars($conn->error) . '</div>';
        } else {
            $stmt->bind_param("isssdissdddd", $user_id, $pickup_location, $delivery_location, $item_description, $weight, $is_fragile, $preferred_time, $delivery_mode, $pickup_lat, $pickup_long, $delivery_lat, $delivery_long);

            if ($stmt->execute()) {
                $stmt->close();
                header("Location: view_deliveries.php");
                exit();
            } else {
                $alert = '<div class="alert alert-danger">Execute failed: ' . htmlspecialchars($stmt->error) . '</div>';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Delivery</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
<style>
body{min-height:100vh; display:flex; justify-content:center; align-items:center; background:#f8f9fa;}
.form-container{background:#fff; padding:2rem; border-radius:1rem; box-shadow:0 0.5rem 1rem rgba(0,0,0,0.15); width:100%; max-width:1200px; position: relative;}
#map{height:400px; width:100%; border-radius:.5rem; margin-bottom:1rem;}
.location-box {
    padding: 0.5rem;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    background-color: #f8f9fa;
    margin-bottom: 0.5rem;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.location-box.selected {
    background-color: #e9ecef;
}
.clear-button {
    background: none;
    border: none;
    cursor: pointer;
    color: #dc3545;
}
.back-button {
    position: absolute;
    top: 1rem;
    left: 1rem;
    padding: 0.5rem 1rem;
    border-radius: 0.25rem;
    cursor: pointer;
    z-index: 1000;
    border: 2px solid #198754ff;
    color: #198754;
    background-color: transparent;
}
.back-button:hover {
    background-color: #d1e7dd;
}
.view-deliveries-button {
    position: absolute;
    top: 1rem;
    right: 1rem;
    padding: 0.5rem 1rem;
    border-radius: 0.25rem;
    cursor: pointer;
    z-index: 1000;
    border: 2px solid #198754;
    color: #198754;
    background-color: transparent;
}
.view-deliveries-button:hover {
    background-color: #d1e7dd;
}
.location-inputs {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}
.location-inputs > div {
    flex: 1;
}
</style>
</head>
<body class="d-flex flex-column min-vh-100 bg-success-subtle">
<form method="POST">
    <div class="form-container">
    <button class="back-button" onclick="window.location.href='../home.php'">
        <i class="fas fa-arrow-left"></i> Back to Home
    </button>
    <button class="view-deliveries-button" onclick="window.location.href='view_deliveries.php'">
        <i class="fas fa-list"></i> View Deliveries
    </button>
    <h2 class="text-center text-success mb-4 fw-semibold">Create Delivery</h2>
    <?= $alert ?>
    <div class="row">
        <div class="col-md-8">
            <div class="location-inputs">
                <div class="mb-3">
                    <label>Pickup Location</label>
                    <div class="location-box" id="pickup_location_box">
                        <span>Click to set pickup location</span>
                        <button type="button" class="clear-button" onclick="clearMarker('pickup')"><i class="fas fa-times"></i></button>
                    </div>
                    <input type="hidden" name="pickup_location" id="pickup_location">
                    <input type="hidden" name="pickup_lat" id="pickup_lat">
                    <input type="hidden" name="pickup_lng" id="pickup_lng">
                </div>
                <div class="mb-3">
                    <label>Drop Location</label>
                    <div class="location-box" id="drop_location_box">
                        <span>Click to set drop location</span>
                        <button type="button" class="clear-button" onclick="clearMarker('drop')"><i class="fas fa-times"></i></button>
                    </div>
                    <input type="hidden" name="drop_location" id="drop_location">
                    <input type="hidden" name="drop_lat" id="drop_lat">
                    <input type="hidden" name="drop_lng" id="drop_lng">
                </div>
            </div>
            <div id="map"></div>
        </div>
        <div class="col-md-4">
            <form method="POST">
                <div class="mb-3">
                    <label>Item Description</label>
                    <textarea class="form-control" name="item_description" required></textarea>
                </div>
                <div class="mb-3">
                    <label>Weight (kg)</label>
                    <input type="number" step="0.01" class="form-control" name="weight" required>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" name="fragile">
                    <label class="form-check-label">Fragile</label>
                </div>
                <div class="mb-3">
                    <label>Preferred Delivery Time</label>
                    <input type="datetime-local" class="form-control" name="preferred_time">
                </div>
                <div class="mb-3">
                    <label>Delivery Mode</label>
                    <select class="form-select" name="delivery_mode" required>
                        <option value="bike">Bike</option>
                        <option value="motorbike">Motorbike</option>
                        <option value="car">Car</option>
                    </select>
                </div>
                <div class="alert alert-info" id="route_info" style="display:none;"></div>
                <button type="submit" class="btn btn-success w-100">Create Delivery</button>
            </form>
        </div>
    </div>
    </div>
</form>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
    <script>
    let map = L.map('map').setView([21.9573, 96.0836], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'&copy; OpenStreetMap contributors'}).addTo(map);

    let pickupMarker = null;
    let dropMarker = null;
    let routingControl = null;
    let selectedLocation = null; // Track which location is being set

    // Reverse geocode via proxy
    async function reverseGeocode(type, lat, lng) {
        try {
            let res = await fetch(`../geocode_proxy.php?lat=${lat}&lon=${lng}`);
            let data = await res.json();
            if (data.display_name) {
                if (type === 'pickup') {
                    document.getElementById('pickup_location_box').querySelector('span').innerText = data.display_name;
                    document.getElementById('pickup_location').value = data.display_name;
                }
                else {
                    document.getElementById('drop_location_box').querySelector('span').innerText = data.display_name;
                    document.getElementById('drop_location').value =  data.display_name;
                }
            }
        } catch (err) {
            console.warn('Reverse geocode failed', err);
        }
    }

    // Set marker function
    function setMarker(type, lat, lng) {
        if (type === 'pickup') {
            if (pickupMarker) map.removeLayer(pickupMarker);
            pickupMarker = L.marker([lat, lng], { draggable: true }).addTo(map).bindPopup('Pickup').openPopup();
            document.getElementById('pickup_lat').value = lat;
            document.getElementById('pickup_lng').value = lng;
            reverseGeocode('pickup', lat, lng);

            pickupMarker.on('moveend', e => {
                let pos = e.target.getLatLng();
                document.getElementById('pickup_lat').value = pos.lat;
                document.getElementById('pickup_lng').value = pos.lng;
                reverseGeocode('pickup', pos.lat, pos.lng);
                createRoutingControl();
            });
        } else {
            if (dropMarker) map.removeLayer(dropMarker);
            dropMarker = L.marker([lat, lng], { draggable: true }).addTo(map).bindPopup('Drop').openPopup();
            document.getElementById('drop_lat').value = lat;
            document.getElementById('drop_lng').value = lng;
            reverseGeocode('drop', lat, lng);

            dropMarker.on('moveend', e => {
                let pos = e.target.getLatLng();
                document.getElementById('drop_lat').value = pos.lat;
                document.getElementById('drop_lng').value = pos.lng;
                reverseGeocode('drop', pos.lat, pos.lng);
                createRoutingControl();
            });
        }
        createRoutingControl();
    }

    function createRoutingControl() {
        if (pickupMarker && dropMarker) {
            let pickupLatLng = pickupMarker.getLatLng();
            let dropLatLng = dropMarker.getLatLng();

            // Check if latitude and longitude are valid numbers
            if (isNaN(pickupLatLng.lat) || isNaN(pickupLatLng.lng) || isNaN(dropLatLng.lat) || isNaN(dropLatLng.lng)) {
                console.error("Invalid latitude or longitude values.");
                return; // Exit the function if values are invalid
            }

            if (routingControl != null) {
                map.removeControl(routingControl);
            }

            routingControl = L.Routing.control({
                waypoints: [
                    L.latLng(pickupLatLng.lat, pickupLatLng.lng),
                    L.latLng(dropLatLng.lat, dropLatLng.lng)
                ],
                routeWhileDragging: false,
                showAlternatives: false,
                lineOptions: {
                    styles: [{color: 'green', opacity: 0.7, weight: 5}]
                }
            }).addTo(map);

            routingControl.on('routesfound', function (e) {
                let routes = e.routes;
                let summary = routes[0].summary;
                let dist = summary.totalDistance / 1000; // km
                let price = dist * 1000;
                let estimatedTime = (dist / 20) * 60; // Assuming 20 km/h average speed, convert to minutes
                document.getElementById('route_info').style.display = 'block';
                document.getElementById('route_info').innerText = `Distance: ${dist.toFixed(2)} km | Estimated Price: ${price.toFixed(0)} Kyats | Estimated Time: ${estimatedTime.toFixed(0)} mins`;
            });
        }
    }

    // Clear markers
    function clearMarker(type) {
        if (type === 'pickup') {
            if (pickupMarker) {
                map.removeLayer(pickupMarker);
                pickupMarker = null;
            }
            document.getElementById('pickup_location_box').querySelector('span').innerText = 'Click to set pickup location';
            document.getElementById('pickup_location').value = '';
            document.getElementById('pickup_lat').value = '';
            document.getElementById('pickup_lng').value = '';
        } else {
            if (dropMarker) {
                map.removeLayer(dropMarker);
                dropMarker = null;
            }
            document.getElementById('drop_location_box').querySelector('span').innerText = 'Click to set drop location';
            document.getElementById('drop_location').value = '';
            document.getElementById('drop_lat').value = '';
            document.getElementById('drop_lng').value = '';
        }
        if (routingControl) {
            map.removeControl(routingControl);
            routingControl = null;
        }
        document.getElementById('route_info').style.display = 'none';
    }

    // Auto pickup location with user geolocation
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(pos => {
            setMarker('pickup', pos.coords.latitude, pos.coords.longitude);
            map.setView([pos.coords.latitude, pos.coords.longitude], 13);
        }, err => {
            console.warn('Geolocation failed, defaulting to Mandalay');
            setMarker('pickup', 21.9573, 96.0836);
        });
    } else {
        setMarker('pickup', 21.9573, 96.0836);
    }

    // Click map to set drop marker
    map.on('click', e => {
        if (selectedLocation === 'pickup') {
            setMarker('pickup', e.latlng.lat, e.latlng.lng);
            selectedLocation = null;
        } else if (selectedLocation === 'drop') {
            setMarker('drop', e.latlng.lat, e.latlng.lng);
            selectedLocation = null;
        } else {
            if (!pickupMarker) {
                selectedLocation = 'pickup';
                setMarker('pickup', e.latlng.lat, e.latlng.lng);
            } else if (!dropMarker) {
                selectedLocation = 'drop';
                setMarker('drop', e.latlng.lat, e.latlng.lng);
            }
        }
    });

    // Pickup and drop location box click event
    document.getElementById('pickup_location_box').addEventListener('click', () => {
        selectedLocation = 'pickup';
        document.getElementById('pickup_location_box').classList.add('selected');
        document.getElementById('drop_location_box').classList.remove('selected');
    });

    document.getElementById('drop_location_box').addEventListener('click', () => {
        selectedLocation = 'drop';
        document.getElementById('drop_location_box').classList.add('selected');
        document.getElementById('pickup_location_box').classList.remove('selected');
    });
    </script>
</body>
</html>