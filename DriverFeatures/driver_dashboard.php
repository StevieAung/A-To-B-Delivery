<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../Database/db.php';

// Redirect if not logged in or not a driver
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'driver') {
    header("Location: ../login.php");
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
    <?php include '../includes/head_tags.php'; ?>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>#map{height:400px;width:100%;}</style>
</head>
<body class="bg-success-subtle d-flex flex-column min-vh-100">

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
    <div class="row">
        <!-- Profile -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">Driver Profile</div>
                <div class="card-body">
                    <?php if($driver): ?>
                        <div class="text-center mb-3">
                            <?php if($driver['profile_photo'] && file_exists("uploads/drivers/".$driver['profile_photo'])): ?>
                                <img src="uploads/drivers/<?php echo $driver['profile_photo']; ?>" class="rounded-circle" width="120">
                            <?php else: ?>
                                <div class="bg-secondary rounded-circle text-white d-flex align-items-center justify-content-center" style="width:120px;height:120px;">
                                    <i class="bi bi-person-fill" style="font-size: 4rem;"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Vehicle Type:</strong> <?= htmlspecialchars($driver['vehicle_type']) ?></li>
                            <li class="list-group-item"><strong>Vehicle Number:</strong> <?= htmlspecialchars($driver['vehicle_number']) ?></li>
                            <li class="list-group-item"><strong>License Number:</strong> <?= htmlspecialchars($driver['license_number']) ?></li>
                            <li class="list-group-item"><strong>Experience:</strong> <?= htmlspecialchars($driver['experience_years']) ?></li>
                        </ul>
                    <?php else: ?>
                        <div class="alert alert-warning">No profile found. Complete setup <a href="driver_setup.php">here</a>.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- Location -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">Your Real-Time Location</div>
                <div class="card-body">
                    <div id="map"></div>
                    <div id="location-status" class="mt-2 text-center text-muted">Awaiting location data...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="bg-white shadow-sm text-center py-3 mt-auto">
    <span class="text-muted">&copy; <?= date('Y'); ?> A To B Delivery</span>
</footer>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    var map = L.map('map').setView([16.8409, 96.1735], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    var driverMarker = null;
    var statusDiv = document.getElementById('location-status');

    if ("geolocation" in navigator) {
        navigator.geolocation.watchPosition(pos => {
            var lat = pos.coords.latitude, lon = pos.coords.longitude;
            var newLatLng = new L.LatLng(lat, lon);
            if (driverMarker) driverMarker.setLatLng(newLatLng);
            else driverMarker = L.marker(newLatLng).addTo(map).bindPopup("You are here").openPopup();
            map.setView(newLatLng, 15);
            statusDiv.textContent = "Location updated. Accuracy: " + Math.round(pos.coords.accuracy) + "m";
            updateDriverLocation(lat, lon);
        }, err => {
            statusDiv.textContent = "Error getting location: " + err.message;
        }, {enableHighAccuracy:true, maximumAge:60000, timeout:15000});
    } else {
        statusDiv.textContent = "Geolocation not supported.";
    }

    function updateDriverLocation(lat, lon) {
        fetch('update_location.php', {
            method:'POST',
            headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:'lat='+encodeURIComponent(lat)+'&lon='+encodeURIComponent(lon)
        }).then(r=>r.json()).then(data=>{
            console.log("Server:", data);
        }).catch(e=>console.error("Error:", e));
    }
});
</script>
</body>
</html>
