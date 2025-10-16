<?php
/**
 * view_deliveries.php
 * --------------------------------------------------------
 * A To B Delivery – Sender Delivery History Page
 * --------------------------------------------------------
 */

session_start();
include '../Database/db.php';

// Ensure only sender can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sender') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$first_name = $_SESSION['user_firstname'] ?? 'Sender';

// Fetch deliveries of this sender
$stmt = $conn->prepare("SELECT * FROM delivery_requests WHERE sender_id=? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$deliveries = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

$new_id = $_GET['new_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Deliveries | A To B Delivery</title>
<?php include '../includes/head_tags.php'; ?>
<style>
body { 
    font-family: var(--font-main);
    background-color: var(--bs-success-bg-subtle);
}

.container { 
    max-width: 1100px; 
    margin-top: 3rem; 
}

.card {
    border-radius: 1rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    padding: 2rem;
    background-color: #fff;
}

.table th, .table td { 
    vertical-align: middle; 
    font-size: 0.9rem;
    text-align: center;
}

.table th {
    background-color: #198754;
    color: white;
}

.table-hover tbody tr:hover {
    background-color: #e9f7ef;
}

.badge {
    font-size: 0.85rem;
}

.card-body {
    padding: 2rem;
}

.text-success {
    color: #198754;
}

.mt-3 {
    margin-top: 2rem;
}

.alert-info {
    font-size: 1.1rem;
    background-color: #cce5ff;
    border-color: #b8daff;
}

.btn-outline-success {
    border-color: #198754;
    color: #198754;
}

.btn-outline-success:hover {
    background-color: #198754;
    color: white;
}

footer {
    margin-top: auto;
}
</style>
</head>
<body class="bg-success-subtle">
<?php include '../includes/navbar.php'; ?>
<div class="container">
    <div class="card p-4">
        <h4 class="text-success fw-bold mb-3 text-center">Delivery History</h4>

        <?php if (count($deliveries) > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-success">
                    <tr>
                        <th>ID</th>
                        <th>Pickup</th>
                        <th>Drop</th>
                        <th>Item</th>
                        <th>Weight</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($deliveries as $d): ?>
                    <?php
                        $status = $d['delivery_status'];
                        $badge = match($status) {
                            'pending' => 'secondary',
                            'accepted' => 'info',
                            'picked_up' => 'primary',
                            'in_transit' => 'warning',
                            'delivered' => 'success',
                            'cancelled' => 'danger',
                            default => 'dark'
                        };
                    ?>
                    <tr class="<?= ($new_id && $new_id == $d['request_id']) ? 'table-success border-success border-2' : '' ?>">
                        <td><?= $d['request_id'] ?></td>
                        <td><?= htmlspecialchars($d['pickup_location']) ?></td>
                        <td><?= htmlspecialchars($d['drop_location']) ?></td>
                        <td><?= htmlspecialchars($d['item_description']) ?></td>
                        <td><?= htmlspecialchars($d['weight'] ?? '-') ?></td>
                        <td><span class="badge bg-<?= $badge ?>"><?= ucfirst($status) ?></span></td>
                        <td><?= date('M d, Y', strtotime($d['created_at'])) ?></td>
                        <td><a href="track_delivery.php?request_id=<?= $d['request_id'] ?>" class="btn btn-sm btn-outline-success">Track</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="alert alert-info text-center">You have no deliveries yet.</div>
        <?php endif; ?>

        <a href="create_delivery.php" class="btn btn-success mt-3">
            <i class="bi bi-plus-circle me-2"></i> Create New Delivery
        </a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
