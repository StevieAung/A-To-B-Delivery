<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../Database/db.php';

$alert = "";

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Upload directory
$uploadDir = __DIR__ . "/uploads/drivers/";

// Create folder if it doesn't exist
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0777, true)) {
        die("Failed to create upload directory: $uploadDir");
    }
}

// Ensure folder is writable
if (!is_writable($uploadDir)) {
    if (!chmod($uploadDir, 0777)) {
        die("Upload directory is not writable and permissions could not be set: $uploadDir");
    }
}

// File upload function
function uploadFile($fileKey, $uploadDir, &$alert){
    if (!empty($_FILES[$fileKey]['name']) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
        $fileName = basename($_FILES[$fileKey]['name']);
        $fileTmp = $_FILES[$fileKey]['tmp_name'];
        $fileType = $_FILES[$fileKey]['type'];
        $fileSize = $_FILES[$fileKey]['size'];

        $allowedTypes = ['image/jpeg','image/png','image/jpg'];
        $maxSize = 2*1024*1024; // 2MB

        if (!in_array($fileType, $allowedTypes)) {
            $alert = "<div class='alert alert-danger mt-3'>Invalid file type for $fileKey. Only JPG/PNG allowed.</div>";
            return false;
        }

        if ($fileSize > $maxSize) {
            $alert = "<div class='alert alert-danger mt-3'>$fileKey is too large. Max 2MB.</div>";
            return false;
        }

        $safeFileName = preg_replace("/[^a-zA-Z0-9\._-]/", "", $fileName);
        $newFileName = time() . "_" . $fileKey . "_" . $safeFileName;
        $destination = $uploadDir . $newFileName;

        if (move_uploaded_file($fileTmp, $destination)) {
            return $newFileName;
        } else {
            $alert = "<div class='alert alert-danger mt-3'>Error uploading $fileKey. Check folder permissions.</div>";
            return false;
        }
    }
    return null;
}

// Handle form submission
if (isset($_POST['save_profile'])) {
    $vehicle_type = $_POST['vehicle_type'] ?? '';
    $vehicle_number = trim($_POST['vehicle_number']);
    $license_number = trim($_POST['license_number']);
    $experience_years = (int) ($_POST['experience_years'] ?? 0);

    // Validate vehicle type
    $validTypes = ['Bike','Motorbike','Car'];
    if (!in_array($vehicle_type, $validTypes)) {
        $alert = '<div class="alert alert-danger mt-3">Invalid vehicle type selected.</div>';
    }

    if (empty($alert)) {
        $profilePhoto = uploadFile('profile_photo', $uploadDir, $alert);
        $licensePhoto = uploadFile('license_photo', $uploadDir, $alert);
    }

    if (empty($alert) && $profilePhoto !== false && $licensePhoto !== false) {
        $stmt = $conn->prepare("INSERT INTO driver_profiles 
            (user_id, vehicle_type, vehicle_number, license_number, experience_years, profile_photo, license_photo)
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssiss", $user_id, $vehicle_type, $vehicle_number, $license_number, $experience_years, $profilePhoto, $licensePhoto);

        if ($stmt->execute()) {
            $alert = "<div class='alert alert-success mt-3 text-center'>
                        <strong>Profile Setup Complete!</strong><br>
                        Redirecting to Dashboard...
                      </div>
                      <script>setTimeout(function(){ window.location.href='driver_dashboard.php'; }, 1000);</script>";
        } else {
            if ($stmt->errno == 1062) { 
                if (strpos($stmt->error,'vehicle_number')!==false) $alert='<div class="alert alert-danger mt-3">This vehicle number is already registered.</div>';
                elseif (strpos($stmt->error,'license_number')!==false) $alert='<div class="alert alert-danger mt-3">This license number is already registered.</div>';
                else $alert='<div class="alert alert-danger mt-3">Duplicate entry detected. Please check your details.</div>';
            } else {
                $alert='<div class="alert alert-danger mt-3">Error: Could not save profile.</div>';
            }
        }
        $stmt->close();
    }
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Driver Setup | A To B Delivery</title>
<?php include '../includes/head_tags.php'; ?>
<style>
*{font-family:"Roboto Mono", monospace;}
body{background:#d1e7dd; min-height:100vh; display:flex; justify-content:center; align-items:center;}
.form-container{background:white; padding:2rem; border-radius:1rem; box-shadow:0 0.5rem 1rem rgba(0,0,0,0.15); width:100%; max-width:700px; display:flex; gap:1rem;}
.form-image{flex:1; display:flex; justify-content:center; align-items:center;}
.form-image img{max-width:100%;}
.form-fields{flex:1;}
</style>
</head>
<body>
<div class="form-container">
    <div class="form-image d-none d-md-flex">
        <img src="../Assets/images/driver.png" alt="Driver Setup">
    </div>
    <div class="form-fields">
        <h3 class="mb-3"><span class="fw-semibold">Setup</span> <span class="text-success fw-bold">Driver Profile</span></h3>
        <?= $alert ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="fw-semibold">Profile Photo:</label>
                <input type="file" name="profile_photo" class="form-control" accept="image/*" required>
            </div>
            <div class="mb-3">
                <label class="fw-semibold">License Photo:</label>
                <input type="file" name="license_photo" class="form-control" accept="image/*" required>
            </div>
            <div class="mb-3">
                <label class="fw-semibold">Vehicle Type:</label>
                <select name="vehicle_type" class="form-select" required>
                    <option value="">-- Select Vehicle Type --</option>
                    <option value="Bike">Bike</option>
                    <option value="Motorbike">Motorbike</option>
                    <option value="Car">Car</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="fw-semibold">Vehicle Number:</label>
                <input type="text" name="vehicle_number" class="form-control" placeholder="e.g., AB-1234" required>
            </div>
            <div class="mb-3">
                <label class="fw-semibold">License Number:</label>
                <input type="text" name="license_number" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="fw-semibold">Years of Experience:</label>
                <input type="number" name="experience_years" class="form-control" min="0" max="50" required>
            </div>
            <button type="submit" name="save_profile" class="btn btn-success w-100">Save Profile</button>
        </form>
    </div>
</div>
</body>
</html>
