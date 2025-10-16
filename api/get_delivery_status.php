<?php
// get_delivery_status.php - Returns delivery status and coordinates for tracking
header('Content-Type: application/json');
require_once '../Database/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
  echo json_encode(['success' => false, 'message' => 'Invalid id']);
  exit;
}

$sql = "SELECT dr.request_id, dr.pickup_location, dr.drop_location, dr.delivery_status,
               dt.current_lat, dt.current_long,
               dr.driver_id
        FROM delivery_requests dr
        LEFT JOIN delivery_tracking dt ON dr.request_id = dt.request_id
        WHERE dr.request_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$res) {
  echo json_encode(['success' => false, 'message' => 'Not found']);
  exit;
}

echo json_encode([
  'success' => true,
  'data' => [
    'request_id' => $res['request_id'],
    'pickup_location' => $res['pickup_location'],
    'drop_location' => $res['drop_location'],
    'delivery_status' => $res['delivery_status'],
    'driver_id' => $res['driver_id'],
    'current_lat' => $res['current_lat'],
    'current_long' => $res['current_long']
  ]
]);
?>


