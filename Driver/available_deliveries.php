<?php
/**
 * available_deliveries.php
 * --------------------------------------------------------
 * A To B Delivery – Driver Available Deliveries Page
 * UI updated to match driver_dashboard.php
 * --------------------------------------------------------
 */

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../Database/db.php';

// Restrict to driver role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'driver') {
    header("Location: ../login.php");
    exit();
}

$driver_id = $_SESSION['user_id'];
$csrf_error = '';
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
$first_name = $_SESSION['user_firstname'] ?? 'Driver';
$alert = "";

// Handle "Accept Delivery" action (Logic is unchanged)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accept_delivery'])) {
    // CSRF validate
    if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
        $alert = '<div class="alert alert-danger">Security validation failed. Please refresh and try again.</div>';
    } else {
    $request_id = $_POST['request_id'];

    $stmt = $conn->prepare("UPDATE delivery_requests SET driver_id = ?, delivery_status = 'accepted' WHERE request_id = ? AND delivery_status = 'pending'");
    $stmt->bind_param("ii", $driver_id, $request_id);
    
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $alert = '<div class="alert alert-success">Delivery accepted! You can view it in "My Deliveries".</div>';
    } else {
        $alert = '<div class="alert alert-warning">Could not accept. The delivery may have been taken by another driver.</div>';
    }
    $stmt->close();
    }
}

// Fetch available deliveries (Logic is unchanged)
$result = $conn->query("SELECT * FROM delivery_requests WHERE delivery_status = 'pending' ORDER BY created_at DESC");
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Deliveries | A To B Delivery</title>
    <?php include '../includes/head_tags.php'; ?>
</head>
<body class="bg-success-subtle d-flex flex-column min-vh-100">

<!-- NAVBAR (Matches driver_dashboard.php) -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand text-success fw-bold" href="driver_dashboard.php">A To B Delivery</a>
        <div class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav">
                <li class="nav-item me-3">
                    <span class="nav-link">Hello, <strong><?php echo htmlspecialchars($first_name); ?></strong></span>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-danger" href="../logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- MAIN CONTENT (Matches driver_dashboard.php layout) -->
<div class="container mt-5 flex-grow-1">
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <span><i class="bi bi-box-seam me-2"></i>Available Deliveries</span>
            <a href="driver_dashboard.php" class="btn btn-light btn-sm">Back to Dashboard</a>
        </div>
        <div class="card-body">
            <?= $alert ?>
            <div class="row">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 shadow-sm border-success">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title text-success">Request #<?= $row['request_id'] ?></h5>
                                    <p class="card-text">
                                        <strong>From:</strong> <?= htmlspecialchars($row['pickup_location']) ?><br>
                                        <strong>To:</strong> <?= htmlspecialchars($row['drop_location']) ?><br>
                                        <strong>Price:</strong> <?= number_format($row['estimated_price'], 2) ?> Ks
                                    </p>
                                    <form method="POST" class="mt-auto">
                                        <input type="hidden" name="request_id" value="<?= $row['request_id'] ?>">
                                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                                        <button type="submit" name="accept_delivery" class="btn btn-success w-100">Accept</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center">No available deliveries at the moment. Check back soon!</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- FOOTER (Matches driver_dashboard.php) -->
<footer class="bg-white shadow-sm text-center py-3 mt-auto">
    <span class="text-muted">&copy; <?= date('Y'); ?> A To B Delivery</span>
</footer>

</body>
</html>
<?php $conn->close(); ?>
