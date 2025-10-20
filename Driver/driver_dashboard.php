<?php
/**
 * driver_dashboard.php
 * --------------------------------------------------------
 * A To B Delivery – Driver Dashboard
 *
 * Purpose:
 *  • Display driver profile, live location, and active delivery info
 *  • Serve as home page for driver module
 *  • Match A To B Delivery design (bg-success-subtle)
 *
 * Author: Sai Htet Aung Hlaing
 * Updated: 2025-10-09
 * --------------------------------------------------------
 */

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Corrected Path: Go up one directory to find the Database folder
include '../Database/db.php';

// Restrict to driver role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'driver') {
    // Corrected Path: Go up one directory to find login.php
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$first_name = $_SESSION['user_firstname'] ?? 'Driver';

// Fetch driver profile
$stmt = $conn->prepare("SELECT d.vehicle_type, d.vehicle_number, d.license_number, d.profile_photo
                         FROM driver_profiles d
                         WHERE d.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$driver = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch current active delivery (if any)
// DEBUG FIX: Corrected column names to match the a2b_delivery.sql schema.
// - `assigned_driver_id` changed to `driver_id`
// - `status` changed to `delivery_status`
// - `delivery_location` changed to `drop_location`
// - Status values changed to lowercase ('accepted', 'picked_up') to match ENUM.
$delivery_query = $conn->prepare("
    SELECT request_id, pickup_location, drop_location, delivery_status 
    FROM delivery_requests 
    WHERE driver_id = ? 
      AND LOWER(delivery_status) IN ('accepted','picked_up','in_progress')
    ORDER BY created_at DESC LIMIT 1
");
$delivery_query->bind_param("i", $user_id);
$delivery_query->execute();
$active_delivery = $delivery_query->get_result()->fetch_assoc();

$delivery_query->close();

$conn->close();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Driver Dashboard | A To B Delivery</title>
    <?php include '../includes/head_tags.php'; // Corrected Path ?>
    <style>
        #map { height: 380px; width: 100%; border-radius: 10px; }
        .map-actions { display:flex; gap:.5rem; margin-bottom:.5rem; flex-wrap: wrap; }
        .map-actions .btn { padding:.25rem .5rem; font-size:.85rem; }
    </style>
</head>
<body class="bg-success-subtle d-flex flex-column min-vh-100">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand text-success fw-bold" href="driver_dashboard.php">A To B Delivery</a>
        <div class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav">
                <li class="nav-item me-3">
                    <span class="nav-link">Hello, <strong><?php echo htmlspecialchars($first_name); ?></strong></span>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-danger" href="../logout.php">Logout</a> <!-- Corrected Path -->
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- MAIN CONTENT -->
<div class="container mt-5 flex-grow-1">
    <div class="row g-4">
        <!-- LEFT COLUMN -->
        <div class="col-md-5">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">Driver Profile</div>
                <div class="card-body text-center">
                    <!-- Corrected Path for image check and src -->
                    <?php if ($driver && !empty($driver['profile_photo']) && file_exists("../uploads/drivers/".$driver['profile_photo'])): ?>
                        <img src="../uploads/drivers/<?php echo htmlspecialchars($driver['profile_photo']); ?>" 
                             alt="Profile" class="rounded-circle mb-3" width="110" height="110" style="object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width:110px;height:110px;">
                            <i class="bi bi-person-fill" style="font-size:3rem;"></i>
                        </div>
                    <?php endif; ?>
                    <?php if ($driver): ?>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Vehicle Type:</strong> <?= htmlspecialchars($driver['vehicle_type']) ?></li>
                            <li class="list-group-item"><strong>Plate No.:</strong> <?= htmlspecialchars($driver['vehicle_number']) ?></li>
                            <li class="list-group-item"><strong>License:</strong> <?= htmlspecialchars($driver['license_number']) ?></li>
                        </ul>
                    <?php else: ?>
                        <div class="alert alert-warning">Profile not completed. <a href="driver_setup.php">Set up now</a>.</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">Quick Navigation</div>
                <div class="card-body d-grid gap-2">
                    <a href="available_deliveries.php" class="btn btn-outline-success"><i class="bi bi-box-seam me-2"></i>Available Deliveries</a>
                    <a href="my_deliveries.php" class="btn btn-outline-primary"><i class="bi bi-truck me-2"></i>My Deliveries</a>
                    <a href="earnings.php" class="btn btn-outline-warning"><i class="bi bi-cash-coin me-2"></i>Earnings</a>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div class="col-md-7">
            <!-- Active Delivery -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">Active Delivery</div>
                <div class="card-body">
                    <?php if ($active_delivery): ?>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Pickup:</strong> <?= htmlspecialchars($active_delivery['pickup_location']) ?></li>
                            <!-- DEBUG FIX: Changed 'delivery_location' to 'drop_location' -->
                            <li class="list-group-item"><strong>Drop:</strong> <?= htmlspecialchars($active_delivery['drop_location']) ?></li>
                            <li class="list-group-item"><strong>Status:</strong> 
                                <!-- DEBUG FIX: Changed 'status' to 'delivery_status' -->
                                <span class="badge bg-info text-dark"><?= htmlspecialchars(ucfirst($active_delivery['delivery_status'])) ?></span>
                            </li>
                        </ul>
                        <a href="my_deliveries.php" class="btn btn-success mt-3">View Details</a>
                    <?php else: ?>
                        <div class="alert alert-info text-center">No active delivery yet.</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Real-Time Location -->
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">Your Real-Time Location</div>
                <div class="card-body">
                    <div class="map-actions">
                        <button type="button" id="btnCenterMe" class="btn btn-outline-primary btn-sm"><i class="bi bi-crosshair"></i> Center</button>
                        <button type="button" id="btnZoomIn" class="btn btn-outline-success btn-sm"><i class="bi bi-zoom-in"></i></button>
                        <button type="button" id="btnZoomOut" class="btn btn-outline-success btn-sm"><i class="bi bi-zoom-out"></i></button>
                    </div>
                    <div id="map"></div>
                    <div id="location-status" class="mt-2 text-center text-muted">Awaiting location data...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FOOTER -->
<footer class="bg-white shadow-sm text-center py-3 mt-auto">
    <span class="text-muted">&copy; <?= date('Y'); ?> A To B Delivery</span>
</footer>

<script>
document.addEventListener("DOMContentLoaded", () => {
  // =============================
  // A To B Delivery – Live Tracking (Enhanced)
  // =============================
  const map = L.map("map").setView([16.8409, 96.1735], 13);
  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png").addTo(map);

  let marker = null;
  const statusDiv = document.getElementById("location-status");
  let lastSent = 0; // Timestamp for throttling API calls

  // ===== Helper: Show colored Bootstrap alerts =====
  function showAlert(type, message) {
    const color = { success: "success", warning: "warning", error: "danger" }[type] || "secondary";
    statusDiv.innerHTML = `
      <div class="alert alert-${color} py-2 mb-0 text-center fw-semibold small">
        ${message}
      </div>`;
  }

  // ===== Helper: Smooth marker movement =====
  function moveMarker(lat, lon) {
    const latlng = L.latLng(lat, lon);
    if (marker) {
      marker.setLatLng(latlng);
    } else {
      marker = L.marker(latlng).addTo(map).bindPopup("📍 You are here").openPopup();
    }
    map.setView(latlng, map.getZoom() < 13 ? 14 : map.getZoom(), { animate: true });
  }

  // ===== Function: Update driver location on server =====
  async function updateLocation(lat, lon) {
    try {
      const now = Date.now();
      if (now - lastSent < 3000) return; // limit updates to once every 3 seconds
      lastSent = now;

      const res = await fetch("update_tracking.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `lat=${encodeURIComponent(lat)}&lon=${encodeURIComponent(lon)}`
      });
      const data = await res.json();

      if (data.success) {
        showAlert("success", `✅ Updated: ${lat.toFixed(4)}, ${lon.toFixed(4)}`);
      } else {
        showAlert("warning", data.message || "⚙️ Waiting for active delivery assignment…");
      }
    } catch (err) {
      showAlert("error", "🚫 Error updating location. Check connection.");
    }
  }

  // ===== Geolocation Watcher =====
  if ("geolocation" in navigator) {
    showAlert("warning", "📡 Initializing location tracking...");
    navigator.geolocation.watchPosition(
      pos => {
        const { latitude, longitude, accuracy } = pos.coords;
        moveMarker(latitude, longitude);
        updateLocation(latitude, longitude);

        // Show live accuracy feedback
        if (accuracy <= 50) {
          showAlert("success", `📍 Accurate to ${Math.round(accuracy)} m`);
        } else if (accuracy <= 500) {
          showAlert("warning", `⚠️ Approx. location (±${Math.round(accuracy)} m)`);
        } else {
          showAlert("error", `❌ Low accuracy (±${Math.round(accuracy)} m). Move outdoors.`);
        }
      },
      err => {
        const errMsg = {
          1: "🚫 Permission denied. Please allow location access.",
          2: "📴 Location unavailable. Check GPS or Wi-Fi.",
          3: "⏳ Location request timed out."
        }[err.code] || "Unknown geolocation error.";
        showAlert("error", errMsg);
      },
      { enableHighAccuracy: true, maximumAge: 0, timeout: 20000 }
    );
  } else {
    showAlert("error", "❌ Geolocation not supported on this device.");
  }

  // ===== UI Controls =====
  document.getElementById("btnCenterMe")?.addEventListener("click", () => {
    if (marker) map.setView(marker.getLatLng(), 15, { animate: true });
  });
  document.getElementById("btnZoomIn")?.addEventListener("click", () => map.zoomIn());
  document.getElementById("btnZoomOut")?.addEventListener("click", () => map.zoomOut());

  // ===== Map Resize Fix =====
  setTimeout(() => map.invalidateSize(), 400);
});
</script>
</body>
</html>
