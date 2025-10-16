<?php
/**
 * submit_feedback.php
 * --------------------------------------------------------
 * Fixed version – handles JSON output, errors, and DB inserts properly
 */

session_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../Database/db.php';

// Validate request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
    exit;
}

$request_id = intval($_POST['request_id'] ?? 0);
$rating     = intval($_POST['rating'] ?? 0);
$comments   = trim($_POST['comments'] ?? '');
$sender_id  = $_SESSION['user_id'] ?? 0;

// Validate fields
if (!$request_id || !$rating || !$comments || !$sender_id) {
    echo json_encode(["success" => false, "message" => "Missing or invalid data"]);
    exit;
}

// Find driver_id from this request
$driverQ = $conn->prepare("SELECT driver_id FROM delivery_requests WHERE request_id = ?");
$driverQ->bind_param("i", $request_id);
$driverQ->execute();
$driverRow = $driverQ->get_result()->fetch_assoc();
$driver_id = $driverRow['driver_id'] ?? null;
$driverQ->close();

if (!$driver_id) {
    echo json_encode(["success" => false, "message" => "No driver assigned yet"]);
    exit;
}

// Prevent duplicate feedback
$check = $conn->prepare("SELECT feedback_id FROM feedback WHERE request_id = ?");
$check->bind_param("i", $request_id);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Feedback already submitted"]);
    $check->close();
    exit;
}
$check->close();

// Insert feedback
$stmt = $conn->prepare("
    INSERT INTO feedback (request_id, driver_id, sender_id, rating, comments)
    VALUES (?, ?, ?, ?, ?)
");
$stmt->bind_param("iiiis", $request_id, $driver_id, $sender_id, $rating, $comments);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Feedback submitted successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Database error: " . $conn->error]);
}

$stmt->close();
$conn->close();
?>
