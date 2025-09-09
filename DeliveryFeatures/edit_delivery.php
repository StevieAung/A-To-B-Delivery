<?php
session_start();
include '../Database/db.php';
$alert = "";

// Check if user is logged in and is a sender
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'sender') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get delivery ID from query
if (!isset($_GET['id'])) {
    header("Location: view_deliveries.php");
    exit();
}
$request_id = intval($_GET['id']);

// Fetch current delivery details
$stmt = $conn->prepare("SELECT * FROM delivery_requests WHERE request_id=? AND user_id=?");
$stmt->bind_param("ii", $request_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$delivery = $result->fetch_assoc();
$stmt->close();

if (!$delivery) {
    header("Location: view_deliveries.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pickup_location   = trim($_POST['pickup_location']);
    $delivery_location = trim($_POST['delivery_location']);
    $item_description  = trim($_POST['item_description']);
    $weight            = trim($_POST['weight']);
    $is_fragile        = isset($_POST['fragile']) ? 1 : 0;
    $preferred_time    = !empty($_POST['preferred_time']) ? $_POST['preferred_time'] : null;
    $delivery_mode     = trim($_POST['delivery_mode']);

    $stmt = $conn->prepare("UPDATE delivery_requests SET 
        pickup_location=?, delivery_location=?, item_description=?, weight=?, is_fragile=?, preferred_time=?, delivery_mode=? 
        WHERE request_id=? AND user_id=?");
    $stmt->bind_param("sssdissii", $pickup_location, $delivery_location, $item_description, $weight, $is_fragile, $preferred_time, $delivery_mode, $request_id, $user_id);

    if ($stmt->execute()) {
        $stmt->close();
        echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Redirecting...</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
        <style>
            body{background-color:#d1e7dd;display:flex;justify-content:center;align-items:center;height:100vh;}
            #spinner-overlay{display:flex;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(255,255,255,0.85);z-index:9999;justify-content:center;align-items:center;flex-direction:column;}
            #spinner-overlay .spinner-border{width:4rem;height:4rem;color:#198754;}
        </style></head><body>
        <div id='spinner-overlay' class='d-flex'><div class='spinner-border'></div><div class='mt-3 text-success'>Updating...</div></div>
        <script>setTimeout(()=>{window.location.href='view_deliveries.php';},2000);</script>
        </body></html>";
        exit();
    } else {
        $alert = '<div class="alert alert-danger">Error: '.htmlspecialchars($stmt->error).'</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Delivery</title>
<?php include '../includes/head_tags.php'; ?>
<style>
body { min-height:100vh; display:flex; justify-content:center; align-items:center; background-color:#d1e7dd; }
.form-container { background:white; padding:2rem; border-radius:1rem; box-shadow:0 0.5rem 1rem rgba(0,0,0,0.15); width:100%; max-width:500px; }
</style>
</head>
<body>
<div class="form-container">
<h2 class="text-center text-success mb-4">Edit Delivery</h2>
<?= $alert ?>
<form method="POST">
<div class="mb-3"><label class="form-label">Pickup Location</label><input type="text" class="form-control" name="pickup_location" value="<?= htmlspecialchars($delivery['pickup_location']) ?>" required></div>
<div class="mb-3"><label class="form-label">Delivery Location</label><input type="text" class="form-control" name="delivery_location" value="<?= htmlspecialchars($delivery['delivery_location']) ?>" required></div>
<div class="mb-3"><label class="form-label">Item Description</label><textarea class="form-control" name="item_description" rows="3" required><?= htmlspecialchars($delivery['item_description']) ?></textarea></div>
<div class="mb-3"><label class="form-label">Weight (kg)</label><input type="number" class="form-control" name="weight" step="0.01" value="<?= $delivery['weight'] ?>" required></div>
<div class="form-check mb-3">
<input class="form-check-input" type="checkbox" name="fragile" <?= $delivery['is_fragile'] ? 'checked' : '' ?>>
<label class="form-check-label">Fragile Item</label>
</div>
<div class="mb-3"><label class="form-label">Preferred Delivery Time</label><input type="datetime-local" class="form-control" name="preferred_time" value="<?= !empty($delivery['preferred_time']) ? date('Y-m-d\TH:i', strtotime($delivery['preferred_time'])) : '' ?>"></div>
<div class="mb-3"><label class="form-label">Delivery Mode</label>
<select class="form-select" name="delivery_mode" required>
<option value="bike" <?= $delivery['delivery_mode']=='bike'?'selected':'' ?>>Bike</option>
<option value="motorbike" <?= $delivery['delivery_mode']=='motorbike'?'selected':'' ?>>Motorbike</option>
<option value="car" <?= $delivery['delivery_mode']=='car'?'selected':'' ?>>Car</option>
</select></div>
<button type="submit" class="btn btn-success w-100">Update Delivery</button>
</form>
</div>
</body>
</html>
