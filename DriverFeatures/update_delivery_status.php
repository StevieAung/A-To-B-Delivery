<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include __DIR__ . '/../Database/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$driver_id = $_SESSION['user_id'];
$alert = "";

// Validate delivery_id
if (!isset($_POST['delivery_id']) || !isset($_POST['status'])) {
    die("Invalid request.");
}

$delivery_id = (int) $_POST['delivery_id'];
$new_status = $_POST['status'];

// Allowed statuses
$allowed = ['accepted','in_transit','delivered','cancelled'];
if (!in_array($new_status, $allowed)) {
    die("Invalid status value.");
}

// Update delivery status
$stmt = $conn->prepare("UPDATE delivery_requests SET status = ? WHERE request_id = ? AND driver_id = ?");
$stmt->bind_param("sii", $new_status, $delivery_id, $driver_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    $alert = "<div class='alert alert-success mt-3 text-center'>
                <strong>Status Updated to ".ucwords(str_replace('_',' ',$new_status))."!</strong>
              </div>
              <script>
                setTimeout(function(){ window.location.href='driver_dashboard.php'; }, 2000);
              </script>";
} else {
    $alert = "<div class='alert alert-danger mt-3 text-center'>
                Failed to update status. Please try again.
              </div>
              <script>
                setTimeout(function(){ window.location.href='driver_dashboard.php'; }, 2000);
              </script>";
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Delivery Status | A To B Delivery</title>
    <?php include __DIR__ . '/../includes/head_tags.php'; ?>
</head>
<body class="d-flex flex-column min-vh-100 bg-success-subtle">
<div class="container mt-5">
    <?= $alert ?>
</div>
</body>
</html>
