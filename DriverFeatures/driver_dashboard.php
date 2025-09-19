<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include './Database/db.php';

// Redirect if not logged in or not a driver
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'driver') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$first_name = $_SESSION['user_firstname'] ?? 'Driver';

// Fetch driver profile
$stmt = $conn->prepare("SELECT vehicle_type, vehicle_number, license_number, experience_years, profile_photo FROM driver_profiles WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$driver = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Driver Dashboard | A To B Delivery</title>
    <?php include './includes/head_tags.php'; ?>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        * { font-family: "Roboto Mono", monospace; }
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100 bg-success-subtle">

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand text-success fw-bold" href="#">A To B Delivery</a>
        <div class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav">
                <li class="nav-item me-3">
                    <span class="nav-link">Hello, <strong><?php echo htmlspecialchars($first_name); ?></strong></span>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-danger" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5 flex-grow-1">
    <div class="row justify-content-center">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <h4>Driver Profile</h4>
                </div>
                <div class="card-body">
                    <?php if($driver): ?>
                        <div class="text-center mb-3">
                            <?php if($driver['profile_photo'] && file_exists("uploads/drivers/".$driver['profile_photo'])): ?>
                                <img src="<?php echo "uploads/drivers/".$driver['profile_photo']; ?>" alt="Profile Photo" class="rounded-circle" width="120">
                            <?php else: ?>
                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white" style="width:120px;height:120px;">
                                    <i class="bi bi-person-fill" style="font-size: 4rem;"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Vehicle Type:</strong> <?php echo htmlspecialchars($driver['vehicle_type']); ?></li>
                            <li class="list-group-item"><strong>Vehicle Number:</strong> <?php echo htmlspecialchars($driver['vehicle_number']); ?></li>
                            <li class="list-group-item"><strong>License Number:</strong> <?php echo htmlspecialchars($driver['license_number']); ?></li>
                            <li class="list-group-item"><strong>Years of Experience:</strong> <?php echo htmlspecialchars($driver['experience_years']); ?></li>
                        </ul>
                    <?php else: ?>
                        <div class="alert alert-warning text-center">No driver profile found. Please complete your setup <a href="driver_setup.php">here</a>.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <h4>Your Real-Time Location</h4>
                </div>
                <div class="card-body">
                    <div id="map"></div>
                    <div id="location-status" class="mt-2 text-center text-muted">Awaiting location data...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="mt-auto py-3 bg-white shadow-sm text-center">
    <div class="container">
        <span class="text-muted">&copy; <?php echo date('Y'); ?> A To B Delivery</span>
    </div>
</footer>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var map = L.map('map').setView([16.8409, 96.1735], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var driverMarker = null;
        var statusDiv = document.getElementById('location-status');

        if ("geolocation" in navigator) {
            statusDiv.textContent = "Getting your location...";

            // Watch position for continuous updates to the map
            navigator.geolocation.watchPosition(function(position) {
                var lat = position.coords.latitude;
                var lon = position.coords.longitude;
                var accuracy = position.coords.accuracy;

                var newLatLng = new L.LatLng(lat, lon);

                if (driverMarker) {
                    driverMarker.setLatLng(newLatLng);
                } else {
                    driverMarker = L.marker(newLatLng).addTo(map)
                        .bindPopup("You are here").openPopup();
                }
                map.setView(newLatLng, 15);
                statusDiv.textContent = "Location updated. Accuracy: " + Math.round(accuracy) + " meters.";
            }, function(error) {
                console.error("Geolocation Error:", error);
                // Handle different error codes with more specific messages
                if (error.code === error.TIMEOUT) {
                    statusDiv.textContent = "Location request timed out. Trying again with a longer timeout.";
                } else if (error.code === error.PERMISSION_DENIED) {
                    statusDiv.textContent = "Location access denied. Please enable location services.";
                } else {
                    statusDiv.textContent = "An error occurred while getting your location.";
                }
            }, {
                enableHighAccuracy: true,
                maximumAge: 60000, // Use a cached position if it's no older than 1 minute
                timeout: 15000 // Increase timeout to 15 seconds
            });
        } else {
            statusDiv.textContent = "Geolocation is not supported by your browser.";
        }
        
        // Function to send location to server
       function updateDriverLocation(lat, lon) {
    fetch('update_location.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'lat=' + encodeURIComponent(lat) + '&lon=' + encodeURIComponent(lon)
    })
    .then(response => response.json()) // Parse the JSON response
    .then(data => {
        console.log('Server Response:', data);
        if (data.success) {
            console.log("Successfully updated location for driver:", data.data);
        } else {
            console.error("Failed to update location:", data.message);
        }
    })
    .catch(error => console.error('Error sending location:', error));
}


        // Set an interval to call the function every 10 seconds
        setInterval(function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var lat = position.coords.latitude;
                    var lon = position.coords.longitude;
                    updateDriverLocation(lat, lon);
                });
            }
        }, 10000);
    });
</script>
</body>
</html>