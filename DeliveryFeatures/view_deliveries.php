<?php
session_start();
include '../Database/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sender') {
    header("Location: ../login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// Fetch user's deliveries
$stmt = $conn->prepare("SELECT * FROM delivery_requests WHERE user_id=? ORDER BY created_at DESC");
$stmt->bind_param("i",$user_id);
$stmt->execute();
$result = $stmt->get_result();
$deliveries = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Deliveries</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color:#d1e7dd; min-height:100vh; }
.table-container { max-width:900px; margin:3rem auto; background:white; padding:2rem; border-radius:1rem; box-shadow:0 0.5rem 1rem rgba(0,0,0,0.15); }
</style>
</head>
<body>
<div class="table-container">
<h2 class="text-center text-success mb-4">My Deliveries</h2>
<table class="table table-hover">
<thead class="table-success">
<tr>
<th>ID</th><th>Pickup</th><th>Delivery</th><th>Item</th><th>Weight</th><th>Fragile</th><th>Mode</th><th>Status</th><th>Actions</th>
</tr>
</thead>
<tbody>
<?php foreach($deliveries as $d): ?>
<tr>
<td><?= $d['request_id'] ?></td>
<td><?= htmlspecialchars($d['pickup_location']) ?></td>
<td><?= htmlspecialchars($d['delivery_location']) ?></td>
<td><?= htmlspecialchars($d['item_description']) ?></td>
<td><?= $d['weight'] ?></td>
<td><?= $d['is_fragile'] ? 'Yes' : 'No' ?></td>
<td><?= ucfirst($d['delivery_mode']) ?></td>
<td><?= ucfirst($d['status']) ?></td>
<td>
<a href="edit_delivery.php?id=<?= $d['request_id'] ?>" class="btn btn-sm btn-success mb-1">Edit</a>
<a href="delete_delivery.php?id=<?= $d['request_id'] ?>" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Are you sure?')">Delete</a>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<a href="create_delivery.php" class="btn btn-success mt-3">Create New Delivery</a>
</div>
</body>
</html>
