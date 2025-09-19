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

// Check if delivery_id is provided
if (!isset($_GET['delivery_id'])) {
    die("Invalid request. No delivery specified.");
}

$delivery_id = (int) $_GET['delivery_id'];

// Accept delivery
$stmt = $conn->prepare("UPDATE delivery_requests SET driver_id = ?, status = 'accepted' WHERE request_id = ? AND status = 'pending'");
$stmt->bind_param("ii", $driver_id, $delivery_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    $alert = "<div class='alert alert-success mt-3 text-center'>
                <strong>Delivery Accepted Successfully!</strong>
              </div>
              <script>
                setTimeout(function(){ window.location.href='driver_dashboard.php'; }, 2000);
              </script>";
} else {
    $alert = "<div class='alert alert-danger mt-3 text-center'>
                Unable to accept this delivery. It may already be taken.
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
    <title>Accept Delivery | A To B Delivery</title>
    <?php include __DIR__ . '/../includes/head_tags.php'; ?>
</head>
<body class="d-flex flex-column min-vh-100 bg-success-subtle">
<div class="container mt-5">
    <?= $alert ?>
</div>
</body>
</html>
