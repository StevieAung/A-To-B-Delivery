<?php
/**
 * get_driver_location.php
 * --------------------------------------------------------
 * Returns the driver’s most recent coordinates.
 * --------------------------------------------------------
 */

header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../Database/db.php';

if (!isset($_GET['request_id']) || !is_numeric($_GET['request_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid or missing request_id']);
    exit;
}

$request_id = (int) $_GET['request_id'];

try {
    $stmt = $conn->prepare("
        SELECT dt.current_lat, dt.current_long, dt.updated_at,
               u.first_name, u.last_name
        FROM delivery_tracking dt
        LEFT JOIN users u ON dt.driver_id = u.user_id
        WHERE dt.request_id = ?
        ORDER BY dt.updated_at DESC
        LIMIT 1
    ");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($lat, $lng, $updated, $fname, $lname);
        $stmt->fetch();
        echo json_encode([
            'success' => true,
            'current_lat' => $lat,
            'current_lng' => $lng,
            'driver_name' => trim("$fname $lname"),
            'last_updated' => $updated
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No tracking data found']);
    }

    $stmt->close();
    $conn->close();
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: '.$e->getMessage()]);
}
?>
