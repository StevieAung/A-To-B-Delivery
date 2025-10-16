<?php
/**
 * update_tracking.php
 * --------------------------------------------------------
 * Updates or inserts driver’s current location.
 * Called every few seconds from driver dashboard.
 * --------------------------------------------------------
 */

session_start();
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../Database/db.php';

$response = ['success' => false, 'message' => 'Authentication failed.'];

// 1️⃣ Validate driver session
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'driver') {
    echo json_encode($response);
    exit;
}

$driver_id = (int) $_SESSION['user_id'];
$lat = $_POST['lat'] ?? null;
$lon = $_POST['lon'] ?? null;

// 2️⃣ Validate coordinates
if (!is_numeric($lat) || !is_numeric($lon)) {
    echo json_encode(['success' => false, 'message' => 'Invalid or missing coordinates.']);
    exit;
}

// 3️⃣ Find active delivery
$stmt = $conn->prepare("
    SELECT request_id, delivery_status
    FROM delivery_requests
    WHERE driver_id = ?
      AND delivery_status IN ('accepted','picked_up','in_transit')
    ORDER BY updated_at DESC
    LIMIT 1
");
$stmt->bind_param("i", $driver_id);
$stmt->execute();
$active = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$active) {
    echo json_encode(['success' => false, 'message' => 'No active delivery to track.']);
    exit;
}

$request_id = $active['request_id'];
$current_status = $active['delivery_status'];

// 4️⃣ UPSERT tracking record
try {
    $check = $conn->prepare("SELECT track_id FROM delivery_tracking WHERE request_id = ?");
    $check->bind_param("i", $request_id);
    $check->execute();
    $exists = $check->get_result()->fetch_assoc();
    $check->close();

    if ($exists) {
        $sql = "UPDATE delivery_tracking 
                SET current_lat=?, current_long=?, current_status=?, updated_at=NOW() 
                WHERE request_id=? AND driver_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssii", $lat, $lon, $current_status, $request_id, $driver_id);
        $ok = $stmt->execute();
        $stmt->close();
    } else {
        $sql = "INSERT INTO delivery_tracking 
                (request_id, driver_id, current_lat, current_long, current_status)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisss", $request_id, $driver_id, $lat, $lon, $current_status);
        $ok = $stmt->execute();
        $stmt->close();
    }

    if ($ok) {
        echo json_encode([
            'success' => true,
            'message' => 'Location updated.',
            'request_id' => $request_id,
            'driver_id' => $driver_id,
            'lat' => $lat,
            'lng' => $lon,
            'status' => $current_status
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database update failed.']);
    }
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: '.$e->getMessage()]);
}
$conn->close();
?>
