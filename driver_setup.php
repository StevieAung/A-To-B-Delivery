<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include './Database/db.php';

$alert = "";

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle form submission
if (isset($_POST['save_profile'])) {
    $vehicle_type = mysqli_real_escape_string($conn, $_POST['vehicle_type']);
    $vehicle_number = mysqli_real_escape_string($conn, $_POST['vehicle_number']);
    $license_number = mysqli_real_escape_string($conn, $_POST['license_number']);
    $experience_years = (int) $_POST['experience_years'];

    // Validate vehicle type
    $validTypes = ['Bike', 'Motorbike', 'Car'];
    if (!in_array($vehicle_type, $validTypes)) {
        $alert = '<div class="alert alert-danger mt-3">Invalid vehicle type selected.</div>';
    }

    // Upload directory
    $uploadDir = "uploads/drivers/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $profilePhoto = NULL;
    $licensePhoto = NULL;
    $allowedTypes = ['image/jpeg','image/png','image/jpg'];
    $maxSize = 2 * 1024 * 1024;

    function uploadFile($fileKey, $prefix, $uploadDir, $allowedTypes, $maxSize, &$alert) {
        if (!empty($_FILES[$fileKey]['name'])) {
            $fileType = $_FILES[$fileKey]['type'];
            $fileSize = $_FILES[$fileKey]['size'];
            if (!in_array($fileType, $allowedTypes)) {
                $alert = '<div class="alert alert-danger mt-3">Invalid file type for '. ucfirst($fileKey) .'. Only JPG/PNG allowed.</div>';
                return false;
            }
            if ($fileSize > $maxSize) {
                $alert = '<div class="alert alert-danger mt-3">'. ucfirst($fileKey) .' is too large. Max 2MB.</div>';
                return false;
            }
            $newFileName = time().'_'.$prefix.'_'.basename($_FILES[$fileKey]['name']);
            if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $uploadDir.$newFileName)) return $newFileName;
            else { $alert = '<div class="alert alert-danger mt-3">Error uploading '. ucfirst($fileKey) .'.</div>'; return false; }
        }
        return NULL;
    }

    if(empty($alert)){
        $profilePhoto = uploadFile("profile_photo","profile",$uploadDir,$allowedTypes,$maxSize,$alert);
        $licensePhoto = uploadFile("license_photo","license",$uploadDir,$allowedTypes,$maxSize,$alert);
    }

    if(empty($alert) && $profilePhoto !== false && $licensePhoto !== false){
        $stmt = $conn->prepare("INSERT INTO driver_profiles (user_id, vehicle_type, vehicle_number, license_number, experience_years, profile_photo, license_photo) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssiss", $user_id, $vehicle_type, $vehicle_number, $license_number, $experience_years, $profilePhoto, $licensePhoto);

        if($stmt->execute()){
            $alert = "<div class='alert alert-success mt-3 text-center d-flex flex-column align-items-center'>
                        <strong>Profile Setup Complete!</strong>
                        <div class='spinner-border text-success mt-2' role='status'><span class='visually-hidden'>Loading...</span></div>
                        <small class='mt-2'>Redirecting to Dashboard...</small>
                      </div>
                      <script>setTimeout(function(){ window.location.href='driver_dashboard.php'; }, 2000);</script>";
        } else {
            if($stmt->errno == 1062){ 
                if(strpos($stmt->error,'vehicle_number')!==false) $alert='<div class="alert alert-danger mt-3">This vehicle number is already registered.</div>';
                elseif(strpos($stmt->error,'license_number')!==false) $alert='<div class="alert alert-danger mt-3">This license number is already registered.</div>';
                else $alert='<div class="alert alert-danger mt-3">Duplicate entry detected. Please check your details.</div>';
            } else $alert='<div class="alert alert-danger mt-3">Error: Could not save profile.</div>';
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Driver Setup | A To B Delivery</title>
    <?php include './includes/head_tags.php'; ?>
    <style>
        * { font-family: "Roboto Mono", monospace; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100 bg-success-subtle">

<div class="container form-container d-flex justify-content-center align-items-center mt-5">
    <div class="row shadow-lg bg-white p-3 rounded-4 overflow-hidden w-100" style="max-width: 900px;">
        <div class="col-md-6 form-image d-none d-md-flex align-items-center justify-content-center">
            <img src="./Assets/images/driver.png" alt="Driver Setup" class="img-fluid" />
        </div>
        <div class="col-md-6 pt-3 px-4">
            <h3 class="mb-3"><span class="fw-semibold">Setup</span> <span class="text-success fw-bold">Driver Profile</span></h3>
            <form method="POST" action="driver_setup.php" enctype="multipart/form-data">
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
            <?php if (!empty($alert)) echo $alert; ?>
        </div>
    </div>
</div>
</body>
</html>
