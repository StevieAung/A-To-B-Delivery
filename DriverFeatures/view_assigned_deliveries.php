<?php
session_start();
include '../Database/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role']!=='driver'){
    header("Location: ../login.php");
    exit();
}

$driver_id = $_SESSION['user_id'];

// Fetch all deliveries assigned to this driver
$stmt = $conn->prepare("
    SELECT dr.request_id, dr.pickup_location, dr.delivery_location, dr.item_description, dr.weight, dr.is_fragile, dr.delivery_mode, dr.status, dr.preferred_time
    FROM delivery_requests dr
    WHERE dr.driver_id = ?
    ORDER BY dr.preferred_time DESC
");
$stmt->bind_param("i", $driver_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch status history for all assigned deliveries
$history_stmt = $conn->prepare("
    SELECT request_id, status, changed_at
    FROM delivery_status_history
    WHERE driver_id = ?
    ORDER BY changed_at ASC
");
$history_stmt->bind_param("i", $driver_id);
$history_stmt->execute();
$history_result = $history_stmt->get_result();

// Organize history by request_id
$history_data = [];
while($row = $history_result->fetch_assoc()){
    $history_data[$row['request_id']][] = $row;
}

$stmt->close();
$history_stmt->close();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assigned Deliveries | Driver Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: "Roboto Mono", monospace; background-color: #d1f7d1; }
        .status-badge { text-transform: capitalize; }
        .timeline { list-style: none; padding-left: 0; }
        .timeline li { margin-bottom: 0.75rem; padding-left: 1.5rem; position: relative; }
        .timeline li::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0.25rem;
            width: 10px;
            height: 10px;
            background-color: #28a745;
            border-radius: 50%;
        }
    </style>
</head>
<body>
<div class="container my-5">
    <h2 class="text-success mb-4">Your Assigned Deliveries</h2>

    <?php if($result->num_rows > 0): ?>
        <?php while($delivery = $result->fetch_assoc()): ?>
            <div class="card mb-4 shadow">
                <div class="card-header bg-success text-white">
                    Delivery #<?= $delivery['request_id'] ?> - Status: 
                    <span class="badge bg-light text-success status-badge"><?= $delivery['status'] ?></span>
                </div>
                <div class="card-body">
                    <p><strong>Pickup:</strong> <?= $delivery['pickup_location'] ?></p>
                    <p><strong>Drop:</strong> <?= $delivery['delivery_location'] ?></p>
                    <p><strong>Item:</strong> <?= $delivery['item_description'] ?></p>
                    <p><strong>Weight:</strong> <?= $delivery['weight'] ?> kg</p>
                    <p><strong>Fragile:</strong> <?= $delivery['is_fragile'] ? 'Yes':'No' ?></p>
                    <p><strong>Mode:</strong> <?= ucfirst($delivery['delivery_mode']) ?></p>
                    <p><strong>Preferred Time:</strong> <?= date("d M Y H:i", strtotime($delivery['preferred_time'])) ?></p>

                    <?php if(isset($history_data[$delivery['request_id']])): ?>
                        <h6 class="mt-3">Status Timeline:</h6>
                        <ul class="timeline">
                            <?php foreach($history_data[$delivery['request_id']] as $status): ?>
                                <li>
                                    <strong><?= ucfirst($status['status']) ?></strong> - 
                                    <small><?= date("d M Y H:i", strtotime($status['changed_at'])) ?></small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <div class="mt-3">
                        <?php if($delivery['status'] === 'pending'): ?>
                            <a href="accept_delivery.php?id=<?= $delivery['request_id'] ?>" class="btn btn-success btn-sm">Accept</a>
                        <?php elseif(in_array($delivery['status'], ['accepted','in_transit'])): ?>
                            <a href="update_delivery_status.php?id=<?= $delivery['request_id'] ?>" class="btn btn-primary btn-sm">Update Status</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-info">No deliveries assigned yet.</div>
    <?php endif; ?>
</div>
</body>
</html>
