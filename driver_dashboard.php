<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include './Database/db.php';

// Redirect if not logged in or not a driver
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'driver') {
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
    <style>
        * { font-family: "Roboto Mono", monospace; }
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

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h4>Driver Profile</h4>
                </div>
                <div class="card-body">
                    <?php if($driver): ?>
                        <div class="text-center mb-3">
                            <?php if($driver['profile_photo'] && file_exists("uploads/drivers/".$driver['profile_photo'])): ?>
                                <img src="<?php echo "uploads/drivers/".$driver['profile_photo']; ?>" alt="Profile Photo" class="rounded-circle" width="120">
                            <?php else: ?>
                                <div class="bg-secondary rounded-circle" style="width:120px;height:120px;"></div>
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
    </div>
</div>

<footer class="mt-auto py-3 bg-white shadow-sm text-center">
    <div class="container">
        <span class="text-muted">&copy; <?php echo date('Y'); ?> A To B Delivery</span>
    </div>
</footer>

</body>
</html>
