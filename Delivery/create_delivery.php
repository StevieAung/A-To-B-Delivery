<?php
/**
 * create_delivery.php
 * --------------------------------------------------------
 * A To B Delivery – Create Delivery Page (Enhanced Version)
 *
 * Purpose:
 *  • Allow "sender" users to create new delivery requests with preferred times and payment method.
 *  • Integrate interactive map for pickup/drop selection with clear buttons.
 *  • Automatically set pickup to user's current location.
 *  • Display a detailed route summary (distance, time, price).
 *
 * Author: Sai Htet Aung Hlaing
 * Updated: 2025-10-10
 * --------------------------------------------------------
 */

session_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../Database/db.php';

// Ensure only logged-in senders can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sender') {
    // Corrected Path: Go up one directory to find login.php
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$csrf_error = '';
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
$alert = "";
// =====================
//   FORM SUBMISSION
// =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF validation
    if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
        $alert = '<div class="alert alert-danger">Security check failed. Please refresh and try again.</div>';
    } else {
    // --- Get data from form ---
    $pickup_location   = trim($_POST['pickup_location']);
    $delivery_location = trim($_POST['drop_location']);
    $item_description  = trim($_POST['item_description']);
    $weight            = trim($_POST['weight']);
    $is_fragile_input  = isset($_POST['fragile']) ? 'Yes' : 'No';
    $estimated_price   = $_POST['estimated_price'] ?? 0.00;
    $payment_method    = $_POST['payment_method']; // Get payment method
    $pickup_lat        = isset($_POST['pickup_lat']) ? trim($_POST['pickup_lat']) : null;
    $pickup_lng        = isset($_POST['pickup_lng']) ? trim($_POST['pickup_lng']) : null;
    $drop_lat          = isset($_POST['drop_lat']) ? trim($_POST['drop_lat']) : null;
    $drop_lng          = isset($_POST['drop_lng']) ? trim($_POST['drop_lng']) : null;
    
    // --- Get and combine date/time ---
    $preferred_date = $_POST['preferred_date'];
    $preferred_time = $_POST['preferred_time'];
    $preferred_pickup_datetime = (!empty($preferred_date) && !empty($preferred_time)) ? "$preferred_date $preferred_time:00" : null;

    // --- Validation ---
    if (empty($pickup_location) || empty($delivery_location)) {
        $alert = '<div class="alert alert-danger">Pickup and Drop locations are required.</div>';
    } else {
        // --- Prepare SQL statement matching current schema (no lat/lng columns) ---
        $stmt = $conn->prepare("INSERT INTO delivery_requests
            (sender_id, pickup_location, drop_location, item_description, weight, is_fragile, estimated_price, preferred_pickup_datetime, payment_method)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Ensure numeric for price; weight is stored as VARCHAR in schema
        $estimated_price_f = is_numeric($estimated_price) ? (float)$estimated_price : 0.0;

        $stmt->bind_param(
            "isssisdss",
            $user_id,
            $pickup_location,
            $delivery_location,
            $item_description,
            $weight,
            $is_fragile_input,
            $estimated_price_f,
            $preferred_pickup_datetime,
            $payment_method
        );

        if ($stmt->execute()) {
            $new_id = $stmt->insert_id;
            $stmt->close();
            header("Location: track_delivery.php?id=" . $new_id);
            exit();
        } else {
            $alert = '<div class="alert alert-danger">Database error: ' . htmlspecialchars($stmt->error) . '</div>';
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
<title>Create Delivery | A To B Delivery</title>
<?php include '../includes/head_tags.php'; ?>
<style>
    body { background-color: var(--bs-success-bg-subtle); }
    .form-container { background: #fff; border-radius: 1rem; box-shadow: 0 10px 30px rgba(0,0,0,0.1); padding: 2.5rem; margin: 2rem auto; }
    #map { height: 450px; width: 100%; border-radius: 0.75rem; border: 1px solid #dee2e6; }
    .map-actions { display:flex; gap:.5rem; margin-bottom:.5rem; flex-wrap: wrap; }
    .map-actions .btn { padding:.25rem .5rem; font-size:.85rem; }
    label { font-weight: 600; color: #198754; }
    .location-box { padding: 0.75rem 1rem; border: 1px solid #ced4da; border-radius: 0.5rem; background-color: #f8f9fa; font-size: 0.9rem; min-height: 58px; display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; }
    .location-box .location-text { flex-grow: 1; word-break: break-word; overflow-wrap: break-word; min-width: 0; }
    .location-box .clear-btn { display: none; flex-shrink: 0; } 
    .route-summary { background-color: #e9fbe9; border: 1px solid #a3e6a3; border-radius: 0.75rem; padding: 1rem; }
    .btn-close {
    color: #ff0000ff;
    border-color: #ff0000ff; 
}
</style>
</head>
<body class="bg-success-subtle">
<div class="container">
    <div class="form-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="../home.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Home</a>
            <h2 class="text-center text-success fw-bold mb-0">Create New Delivery</h2>
            <a href="view_deliveries.php" class="btn btn-outline-success">My Deliveries <i class="bi bi-list-ul"></i></a>
        </div>
        <?= $alert ?>
        <form method="POST">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="mb-3">
                        <label class="form-label">Pickup Location</label>
                        <div class="location-box" id="pickup_location_box">
                            <span class="location-text">Detecting current location...</span>
                            <button type="button" class="btn-close clear-btn" aria-label="Clear" onclick="clearMarker('pickup')"></button>
                        </div>
                        <input type="hidden" name="pickup_location" id="pickup_location">
                        <input type="hidden" name="pickup_lat" id="pickup_lat">
                        <input type="hidden" name="pickup_lng" id="pickup_lng">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Drop Location</label>
                        <div class="location-box" id="drop_location_box">
                            <span class="location-text">Click on the map to set a drop-off location...</span>
                            <button type="button" class="btn-close clear-btn" aria-label="Clear" onclick="clearMarker('drop')"></button>
                        </div>
                        <input type="hidden" name="drop_location" id="drop_location">
                        <input type="hidden" name="drop_lat" id="drop_lat">
                        <input type="hidden" name="drop_lng" id="drop_lng">
                    </div>
                    <div class="map-actions">
                        <button type="button" id="btnFitBoth" class="btn btn-outline-success btn-sm"><i class="bi bi-bounding-box"></i> Fit Both</button>
                        <button type="button" id="btnCenterPickup" class="btn btn-outline-primary btn-sm"><i class="bi bi-geo-alt"></i> Pickup</button>
                        <button type="button" id="btnCenterDrop" class="btn btn-outline-secondary btn-sm"><i class="bi bi-geo"></i> Drop</button>
                    </div>
                    <div id="map"></div>
                    <div id="mapInfo" class="text-muted small mt-2"></div>
                </div>
                <div class="col-lg-5">
                    <div class="mb-3">
                        <label>Item Description</label>
                        <textarea class="form-control" name="item_description" rows="2" required placeholder="e.g., Documents, a small box"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label>Weight (kg)</label><input type="number" step="0.1" class="form-control" name="weight" required placeholder="e.g., 1.5"></div>
                        <div class="col-md-6 mb-3 d-flex align-items-end"><div class="form-check"><input type="checkbox" class="form-check-input" name="fragile" id="fragileCheck"><label class="form-check-label" for="fragileCheck">Fragile Item</label></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label>Preferred Date</label><input type="date" class="form-control" name="preferred_date" min="<?= date('Y-m-d') ?>"></div>
                        <div class="col-md-6 mb-3"><label>Preferred Time</label><input type="time" class="form-control" name="preferred_time"></div>
                    </div>
                    <div class="mb-3">
                        <label for="payment_method">Payment Method</label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="Cash" selected>Cash</option><option value="Card">Card</option><option value="Wallet">Wallet</option>
                        </select>
                    </div>
                    <div id="route_info" class="route-summary" style="display:none;">
                        <h5 class="text-success text-center mb-3">Route Summary</h5>
                        <div class="d-flex justify-content-between"><span>Distance:</span> <strong id="dist_val">0 km</strong></div>
                        <div class="d-flex justify-content-between"><span>Est. Time:</span> <strong id="time_val">0 mins</strong></div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between fs-5"><strong>Est. Price:</strong> <strong id="price_val">0 Ks</strong></div>
                    </div>
                    <input type="hidden" name="estimated_price" id="estimated_price" value="0">
                    <button type="submit" class="btn btn-success w-100 py-2 fw-semibold mt-3"><i class="bi bi-send-fill me-2"></i>Create Delivery</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- Leaflet Routing Machine (CSS + JS) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
<script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>

<script>
  // =============================
  //   A To B Delivery – Map Logic
  // =============================
  let map = L.map('map');
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
  // Initial center (Mandalay)
  map.setView([21.9573, 96.0836], 13);

  let pickupMarker = null, dropMarker = null, routingControl = null;
  const infoEl = document.getElementById('mapInfo');

  // ---------- Reverse Geocoding ----------
  async function reverseGeocode(type, lat, lng) {
    try {
      let res = await fetch(`../api/geocode_proxy.php?lat=${lat}&lon=${lng}`);
      let data = await res.json();
      if (data.display_name) {
        document.querySelector(`#${type}_location_box .location-text`).innerText = data.display_name;
        document.getElementById(`${type}_location`).value = data.display_name;
        document.querySelector(`#${type}_location_box .clear-btn`).style.display = 'block';
      }
    } catch (err) {
      console.warn('Reverse geocode failed', err);
    }
  }

  // ---------- Set Marker ----------
  function setMarker(type, lat, lng) {
    let marker = L.marker([lat, lng], { draggable: true }).addTo(map);
    if (type === 'pickup') {
      if (pickupMarker) map.removeLayer(pickupMarker);
      pickupMarker = marker.bindPopup('Pickup Location').openPopup();
    } else {
      if (dropMarker) map.removeLayer(dropMarker);
      dropMarker = marker.bindPopup('Drop-off Location').openPopup();
    }

    reverseGeocode(type, lat, lng);
    const latField = document.getElementById(type + '_lat');
    const lngField = document.getElementById(type + '_lng');
    if (latField && lngField) {
      latField.value = lat;
      lngField.value = lng;
    }

    marker.on('moveend', e => {
      const { lat, lng } = e.target.getLatLng();
      reverseGeocode(type, lat, lng);
      if (latField && lngField) {
        latField.value = lat;
        lngField.value = lng;
      }
      createRoute();
    });

    createRoute();
  }

  // ---------- Clear Marker ----------
  function clearMarker(type) {
    let marker = (type === 'pickup') ? pickupMarker : dropMarker;
    if (marker) map.removeLayer(marker);
    if (type === 'pickup') pickupMarker = null; else dropMarker = null;

    document.querySelector(`#${type}_location_box .location-text`).innerText =
      `Click on the map to set a ${type} location...`;
    document.getElementById(`${type}_location`).value = '';
    document.querySelector(`#${type}_location_box .clear-btn`).style.display = 'none';

    if (routingControl) map.removeControl(routingControl);
    routingControl = null;
    document.getElementById('route_info').style.display = 'none';
    fitToMarkers();
  }

  // ---------- Route Creation ----------
  function createRoute() {
    if (pickupMarker && dropMarker) {
      if (routingControl) map.removeControl(routingControl);
      routingControl = L.Routing.control({
        waypoints: [pickupMarker.getLatLng(), dropMarker.getLatLng()],
        routeWhileDragging: false,
        lineOptions: { styles: [{ color: 'green', opacity: 0.8, weight: 6 }] },
        createMarker: () => null
      }).on('routesfound', e => {
        let summary = e.routes[0].summary;
        let distance = summary.totalDistance / 1000;
        let time = Math.round(summary.totalTime / 60);
        let price = distance * 1000;

        document.getElementById('route_info').style.display = 'block';
        document.getElementById('dist_val').innerText = `${distance.toFixed(2)} km`;
        document.getElementById('time_val').innerText = `${time} mins`;
        document.getElementById('price_val').innerText =
          `${price.toLocaleString('en-US', { maximumFractionDigits: 0 })} Ks`;
        document.getElementById('estimated_price').value = price.toFixed(2);
        if (infoEl)
          infoEl.textContent = `Route: ${distance.toFixed(2)} km • ETA: ${time} mins`;

        const bounds = L.latLngBounds([
          pickupMarker.getLatLng(),
          dropMarker.getLatLng()
        ]);
        map.fitBounds(bounds.pad(0.2));
      }).addTo(map);
    } else {
      fitToMarkers();
    }
  }

  // ---------- Fit map view ----------
  function fitToMarkers() {
    const points = [];
    if (pickupMarker) points.push(pickupMarker.getLatLng());
    if (dropMarker) points.push(dropMarker.getLatLng());

    if (points.length === 0) {
      map.setView([21.9573, 96.0836], 13);
      return;
    }
    if (points.length === 1) {
      map.setView(points[0], 14);
      return;
    }
    map.fitBounds(L.latLngBounds(points).pad(0.2));
  }

  // ---------- Detect user location ----------
  window.addEventListener('load', () => {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        pos => {
          console.log('Geolocation success:', pos.coords);
          setMarker('pickup', pos.coords.latitude, pos.coords.longitude);
          map.setView([pos.coords.latitude, pos.coords.longitude], 14);
        },
        err => {
          console.warn('Geolocation error:', err);
          let msg = 'Could not get location. Using default Mandalay center.';
          if (err.code === 1) msg = 'Permission denied. Please allow location access.';
          else if (err.code === 2) msg = 'Location unavailable. Please check Wi-Fi or GPS.';
          else if (err.code === 3) msg = 'Location request timed out.';
          document.querySelector('#pickup_location_box .location-text').innerText = msg;

          // Fallback Mandalay
          const fallbackLat = 21.9573, fallbackLng = 96.0836;
          setMarker('pickup', fallbackLat, fallbackLng);
          map.setView([fallbackLat, fallbackLng], 13);
        },
        { enableHighAccuracy: true, timeout: 10000 }
      );
    } else {
      document.querySelector('#pickup_location_box .location-text').innerText =
        'Geolocation not supported by this browser.';
      setMarker('pickup', 21.9573, 96.0836);
    }
  });

  // ---------- Map Click Events ----------
  map.on('click', e => {
    if (!pickupMarker) setMarker('pickup', e.latlng.lat, e.latlng.lng);
    else if (!dropMarker) setMarker('drop', e.latlng.lat, e.latlng.lng);
  });

  // ---------- UI Controls ----------
  document.getElementById('btnFitBoth').addEventListener('click', fitToMarkers);
  document.getElementById('btnCenterPickup').addEventListener('click', () => {
    if (pickupMarker) map.setView(pickupMarker.getLatLng(), 14);
  });
  document.getElementById('btnCenterDrop').addEventListener('click', () => {
    if (dropMarker) map.setView(dropMarker.getLatLng(), 14);
  });

  // ---------- Fix rendering layout ----------
  setTimeout(() => { map.invalidateSize(); fitToMarkers(); }, 400);
</script>

</body>
</html>
