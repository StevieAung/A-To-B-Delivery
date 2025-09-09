<?php
session_start();
include '../Database/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'sender') {
    header("Location: ../login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    header("Location: view_deliveries.php");
    exit();
}
$request_id = intval($_GET['id']);

$stmt = $conn->prepare("DELETE FROM delivery_requests WHERE request_id=? AND user_id=?");
$stmt->bind_param("ii", $request_id, $user_id);

if($stmt->execute()){
    $stmt->close();
    // Spinner redirect
    echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Deleting...</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
    body{background-color:#d1e7dd;display:flex;justify-content:center;align-items:center;height:100vh;}
    #spinner-overlay{display:flex;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(255,255,255,0.85);z-index:9999;justify-content:center;align-items:center;flex-direction:column;}
    #spinner-overlay .spinner-border{width:4rem;height:4rem;color:#198754;}
    </style></head><body>
    <div id='spinner-overlay' class='d-flex'><div class='spinner-border'></div><div class='mt-3 text-success'>Redirecting...</div></div>
    <script>setTimeout(()=>{window.location.href='view_deliveries.php';},2000);</script>
    </body></html>";
    exit();
} else {
    echo "Error deleting delivery: ".htmlspecialchars($stmt->error);
}
?>
