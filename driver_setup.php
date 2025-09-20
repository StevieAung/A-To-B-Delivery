<?php
session_start();
include './Database/db.php';

$alert = "";
$successRedirect = false;

// Redirect if not logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'driver') {
    header("Location: register.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vehicle_type = $_POST['vehicle_type'];
    $vehicle_number = trim($_POST['vehicle_number']);
    $license_number = trim($_POST['license_number']);
    $experience_years = $_POST['experience_years'];

    // Upload folder
    $uploadDir = "uploads/drivers/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Allowed MIME types and max size (2MB)
    $allowedTypes = ['image/jpeg', 'image/png'];
    $maxSize = 2 * 1024 * 1024; // 2MB

    // Profile photo
    $profile_photo = null;
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === 0) {
        $fileType = mime_content_type($_FILES['profile_photo']['tmp_name']);
        $fileSize = $_FILES['profile_photo']['size'];
        if (!in_array($fileType, $allowedTypes)) {
            $alert = '<div class="alert alert-danger">Invalid profile photo format. Only JPG, PNG allowed.</div>';
        } elseif ($fileSize > $maxSize) {
            $alert = '<div class="alert alert-danger">Profile photo exceeds 2MB size limit.</div>';
        } else {
            $ext = pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
            $profile_photo = time() . "_profile." . $ext;
            move_uploaded_file($_FILES['profile_photo']['tmp_name'], $uploadDir . $profile_photo);
        }
    }

    // License photo
    $license_photo = null;
    if (isset($_FILES['license_photo']) && $_FILES['license_photo']['error'] === 0) {
        $fileType = mime_content_type($_FILES['license_photo']['tmp_name']);
        $fileSize = $_FILES['license_photo']['size'];
        if (!in_array($fileType, $allowedTypes)) {
            $alert = '<div class="alert alert-danger">Invalid license photo format. Only JPG, PNG allowed.</div>';
        } elseif ($fileSize > $maxSize) {
            $alert = '<div class="alert alert-danger">License photo exceeds 2MB size limit.</div>';
        } else {
            $ext = pathinfo($_FILES['license_photo']['name'], PATHINFO_EXTENSION);
            $license_photo = time() . "_license." . $ext;
            move_uploaded_file($_FILES['license_photo']['tmp_name'], $uploadDir . $license_photo);
        }
    }

    // Only insert if no alert
    if (empty($alert)) {
        $stmt = $conn->prepare("INSERT INTO driver_profiles 
            (user_id, vehicle_type, vehicle_number, license_number, experience_years, profile_photo, license_photo) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $user_id, $vehicle_type, $vehicle_number, $license_number, $experience_years, $profile_photo, $license_photo);

        if ($stmt->execute()) {
            $successRedirect = true;
            $alert = '
            <div class="alert alert-success text-center d-flex flex-column align-items-center justify-content-center">
                <div class="spinner-border text-success mb-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <strong>Success! Redirecting to dashboard...</strong>
            </div>';
        } else {
            $alert = '<div class="alert alert-danger">Error: ' . $stmt->error . '</div>';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Driver Setup | A To B Delivery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; font-family: "Roboto Mono", monospace; }
        .setup-container { border-radius: 12px; background: #fff; }
        .preview-img { margin-top: 10px; max-height: 150px; border-radius: 8px; border: 1px solid #ddd; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100 bg-success-subtle">
<div class="container my-5">
    <div class="row shadow-lg setup-container mx-auto" style="max-width: 900px;">
        <!-- Left Column: Image -->
        <div class="col-md-5 d-flex align-items-center justify-content-center  p-3">
            <img src="Assets/images/driver.png" class="img-fluid rounded" alt="Driver Image">
        </div>

        <!-- Right Column: Form -->
        <div class="col-md-7 p-4">
            <h3 class="text-center mb-4 text-success fw-semibold">Setup your Driver Profile</h3>
            <?php if ($alert) echo $alert; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Profile Photo</label>
                    <input type="file" name="profile_photo" class="form-control" accept="image/*" onchange="previewImage(this, 'profilePreview')" required>
                    <img id="profilePreview" class="preview-img d-none" alt="Profile Preview">
                </div>

                <div class="mb-3">
                    <label class="form-label">License Photo</label>
                    <input type="file" name="license_photo" class="form-control" accept="image/*" onchange="previewImage(this, 'licensePreview')" required>
                    <img id="licensePreview" class="preview-img d-none" alt="License Preview">
                </div>

                <div class="mb-3">
                    <label class="form-label">Vehicle Type</label>
                    <select class="form-select" name="vehicle_type" required>
                        <option value="">Select Vehicle</option>
                        <option value="Bike">Bike</option>
                        <option value="Motorbike">Motorbike</option>
                        <option value="Car">Car</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Vehicle Number</label>
                    <input type="text" name="vehicle_number" class="form-control" placeholder="e.g. 7J-1234" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">License Number</label>
                    <input type="text" name="license_number" class="form-control" placeholder="e.g. MM-DRV-2025-XYZ" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Experience</label>
                    <select class="form-select" name="experience_years" required>
                        <option value="">Select Experience</option>
                        <option value="None">None</option>
                        <option value="Less than 1 year">Less than 1 year</option>
                        <option value="Above 1 year">Above 1 year</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success w-100">Submit</button>
            </form>
        </div>
    </div>
</div>

<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove("d-none");
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = "";
        preview.classList.add("d-none");
    }
}

// Redirect after success
<?php if ($successRedirect): ?>
setTimeout(function() {
    window.location.href = 'DriverFeatures/driver_dashboard.php';
}, 1000);
<?php endif; ?>
</script>
</body>
</html>
