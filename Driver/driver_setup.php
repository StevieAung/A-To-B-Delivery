<?php
/**
 * driver_setup.php
 * --------------------------------------------------------
 * A To B Delivery – Driver Setup Page (Enhanced Version)
 *
 * Purpose:
 *  - Allows drivers to complete their profile setup.
 *  - Upload necessary documents and vehicle information.
 *
 * Author: Sai Htet Aung Hlaing
 * Updated: 2025-10-06
 * --------------------------------------------------------
 */

session_start();
include '../Database/db.php';

// Ensure only logged-in drivers can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'driver') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$alert = "";

// =====================
//   FORM SUBMISSION
// =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vehicle_type = $_POST['vehicle_type'];
    $vehicle_number = trim($_POST['vehicle_number']);
    $license_number = trim($_POST['license_number']);
    $experience_years = $_POST['experience_years'];

    $targetDir = "../uploads/drivers/";
    if (!file_exists($targetDir)) { mkdir($targetDir, 0777, true); }

    $allowed = ['image/jpeg','image/png','image/webp'];
    $maxSize = 2 * 1024 * 1024; // 2MB

    function store_image($file, $dir, $allowed, $maxSize) {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) return [null, 'Upload error'];
        $tmp = $file['tmp_name'];
        if (!file_exists($tmp)) return [null, 'Temp file missing'];
        $mime = mime_content_type($tmp);
        if (!in_array($mime, $allowed)) return [null, 'Invalid file type'];
        if ($file['size'] > $maxSize) return [null, 'File too large'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $name = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $dest = rtrim($dir,'/') . '/' . $name;
        if (!move_uploaded_file($tmp, $dest)) return [null, 'Failed to save file'];
        return [$name, null];
    }

    list($profile_photo, $err1) = store_image($_FILES['profile_photo'], $targetDir, $allowed, $maxSize);
    list($license_photo, $err2) = store_image($_FILES['license_photo'], $targetDir, $allowed, $maxSize);
    if ($err1 || $err2) {
        $alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">Image upload failed: ' . htmlspecialchars($err1 ?: $err2) . '</div>';
    } else {

        // Insert data
        $stmt = $conn->prepare("INSERT INTO driver_profiles 
            (user_id, vehicle_type, vehicle_number, license_number, experience_years, profile_photo, license_photo)
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $user_id, $vehicle_type, $vehicle_number, $license_number, $experience_years, $profile_photo, $license_photo);

        if ($stmt->execute()) {
            $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Success!</strong> Driver profile created successfully. Redirecting...
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>';
            // Redirect after a short delay
            header("Refresh: 3; url=../Driver/driver_dashboard.php");
        } else {
            $alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> Something went wrong. Please try again.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Setup | A To B Delivery</title>
    <?php include '../includes/head_tags.php'; ?>
    <style>
        body, html {
            height: 100%;
            margin: 0;
            background-color: var(--bs-success-bg-subtle);            
            font-family: "Roboto Mono", monospace;
        }
        .form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh; /* Use min-height to ensure it fills the screen */
            padding: 2rem; /* Add padding around the card for spacing */
        }
        .card {
            border-radius: 1.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: none;
            overflow: hidden; /* Important for rounded corners on children */
        }
        .form-image-container {
            padding: 0; /* Remove padding to make image flush with edges */
        }
        .form-image {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Ensures the image covers the area without distortion */
        }
        .form-column {
            padding: 3rem; /* Increased padding for better spacing inside the form */
        }
        .btn-custom {
            background-color: #00a884;
            color: white;
            border-radius: 30px;
            transition: 0.3s;
        }
        .btn-custom:hover {
            background-color: #007f63;
        }
        /* Image preview styling */
        .img-preview {
            width: 150px;
            height: 150px;
            border-radius: 12px;
            object-fit: cover;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container form-container">
        <div class="card w-100" style="max-width: 900px;">
            <div class="row g-0">
                <!-- Left Column: Image -->
                <div class="col-md-5 d-none d-md-block form-image-container">
                    <img src="../Assets/images/driver.png" alt="Driver Setup Image" class="form-image">
                </div>
                <!-- Right Column: Form -->
                <div class="col-md-7 form-column">
                    <h3 class="mb-4 text-success fw-semibold text-center">
                        <i class="bi bi-bicycle"></i> Set up Your Driver Profile
                    </h3>
                    <?= $alert ?>

                    <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        <!-- Vehicle Type -->
                        <div class="mb-3">
                            <label class="form-label">Vehicle Type</label>
                            <select class="form-select" name="vehicle_type" required>
                                <option value="" disabled selected>Select your vehicle</option>
                                <option value="Bike">Bike</option>
                                <option value="Motorbike">Motorbike</option>
                                <option value="Car">Car</option>
                            </select>
                            <div class="invalid-feedback">Please select your vehicle type.</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Vehicle Number</label>
                                <input type="text" class="form-control" name="vehicle_number" placeholder="e.g. YGN-4567" required>
                                <div class="invalid-feedback">Vehicle number is required.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">License Number</label>
                                <input type="text" class="form-control" name="license_number" placeholder="e.g. DL-12345" required>
                                <div class="invalid-feedback">License number is required.</div>
                            </div>
                        </div>

                        <!-- Experience -->
                        <div class="mb-3">
                            <label class="form-label">Driving Experience</label>
                            <select class="form-select" name="experience_years">
                                <option value="None">None</option>
                                <option value="Less than 1 year">Less than 1 year</option>
                                <option value="Above 1 year">Above 1 year</option>
                            </select>
                        </div>

                        <div class="row">
                            <!-- Profile Photo -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Profile Photo</label>
                                <input class="form-control" type="file" name="profile_photo" accept="image/*" onchange="previewImage(event, 'profilePreview')" required>
                                <div class="invalid-feedback">Profile photo is required.</div>
                                <div class="mt-2 text-center">
                                    <img id="profilePreview" class="img-preview" src="" alt="Profile Preview">
                                </div>
                            </div>

                            <!-- License Photo -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">License Photo</label>
                                <input class="form-control" type="file" name="license_photo" accept="image/*" onchange="previewImage(event, 'licensePreview')" required>
                                <div class="invalid-feedback">License photo is required.</div>
                                <div class="mt-2 text-center">
                                    <img id="licensePreview" class="img-preview" src="" alt="License Preview">
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid mt-3">
                            <button type="submit" class="btn btn-success btn-lg w-100">Complete Setup</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
    // Bootstrap validation
    (function () {
        'use strict';
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();

    // Image preview function
    function previewImage(event, previewId) {
        const output = document.getElementById(previewId);
        output.src = URL.createObjectURL(event.target.files[0]);
        output.onload = function() {
            URL.revokeObjectURL(output.src); // Clean up memory
        }
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
