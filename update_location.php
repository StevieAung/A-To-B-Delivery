<?php
session_start();
header('Content-Type: application/json');
include './Database/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'driver') {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['lat'], $_POST['lon'])) {
    $lat = floatval($_POST['lat']);
    $lon = floatval($_POST['lon']);

    // Get driver_id from driver_profiles
    $stmt = $conn->prepare("SELECT driver_id FROM driver_profiles WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($driver_id);
    $stmt->fetch();
    $stmt->close();

    if ($driver_id) {
        // Insert or update driver location
        $stmt = $conn->prepare("INSERT INTO driver_locations (driver_id, latitude, longitude) 
                                VALUES (?, ?, ?) 
                                ON DUPLICATE KEY UPDATE latitude=VALUES(latitude), longitude=VALUES(longitude), updated_at=CURRENT_TIMESTAMP");
        $stmt->bind_param("idd", $driver_id, $lat, $lon);
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "data" => ["lat"=>$lat, "lon"=>$lon]]);
        } else {
            echo json_encode(["success" => false, "message" => "DB error"]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "No driver profile"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}
$conn->close();
