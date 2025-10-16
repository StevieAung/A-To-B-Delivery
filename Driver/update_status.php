<?php
/**
 * update_status.php
 * --------------------------------------------------------
 * A To B Delivery – Driver Delivery Status Updater
 * Automatically inserts a payment record when delivery is completed.
 * --------------------------------------------------------
 */

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../Database/db.php';

// Restrict access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'driver') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$driver_id = $_SESSION['user_id'];
$request_id = $_POST['request_id'] ?? null;
$new_status = $_POST['status'] ?? '';

if (!$request_id || !in_array($new_status, ['accepted','picked_up','in_transit','delivered','cancelled'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

// Update delivery status
$stmt = $conn->prepare("UPDATE delivery_requests SET delivery_status = ? WHERE request_id = ? AND driver_id = ?");
$stmt->bind_param("sii", $new_status, $request_id, $driver_id);
$updated = $stmt->execute();
$stmt->close();

if (!$updated) {
    echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
    exit();
}

// ===== When Delivered: Record Payment & Update Tracking =====
if ($new_status === 'delivered') {
    // Update delivery_tracking current_status
    $t = $conn->prepare("UPDATE delivery_tracking SET current_status = 'delivered', updated_at = NOW() WHERE request_id = ? AND driver_id = ?");
    $t->bind_param("ii", $request_id, $driver_id);
    $t->execute();
    $t->close();

    // Fetch delivery data
    $q = $conn->prepare("SELECT sender_id, estimated_price, payment_method FROM delivery_requests WHERE request_id = ?");
    $q->bind_param("i", $request_id);
    $q->execute();
    $delivery = $q->get_result()->fetch_assoc();
    $q->close();

    if ($delivery) {
        $sender_id = $delivery['sender_id'];
        $amount = $delivery['estimated_price'];
        $method = $delivery['payment_method'];

        // Check if payment already exists
        $check = $conn->prepare("SELECT payment_id FROM payments WHERE request_id = ?");
        $check->bind_param("i", $request_id);
        $check->execute();
        $exists = $check->get_result()->num_rows > 0;
        $check->close();

        if (!$exists) {
            // Insert payment
            $p = $conn->prepare("
                INSERT INTO payments (request_id, sender_id, driver_id, amount, method, payment_status)
                VALUES (?, ?, ?, ?, ?, 'Completed')
            ");
            $p->bind_param("iiids", $request_id, $sender_id, $driver_id, $amount, $method);
            $p->execute();
            $p->close();
        }
    }
}

echo json_encode(['success' => true, 'message' => 'Delivery status updated successfully.']);
$conn->close();
?>
