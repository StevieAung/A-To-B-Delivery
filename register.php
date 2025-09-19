<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include './Database/db.php';

$alert = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = preg_replace('/\D/', '', trim($_POST['phone']));
    $role = trim($_POST['user_type']); // sender or driver
    $password_raw = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if ($password_raw !== $confirm_password) {
        $alert = "<div class='alert alert-danger mt-3 text-center'>Passwords do not match.</div>";
    } elseif (strlen($password_raw) < 8 || !preg_match('/[A-Z]/', $password_raw) || !preg_match('/[a-z]/', $password_raw) 
        || !preg_match_all('/\d/', $password_raw, $nums) || count($nums[0]) < 2 
        || !preg_match_all('/[^a-zA-Z0-9$#]/', $password_raw, $specials) || count($specials[0]) < 2) {
        $alert = "<div class='alert alert-danger mt-3 text-center'>Password does not meet requirements.</div>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $alert = "<div class='alert alert-danger mt-3 text-center'>Invalid email format.</div>";
    } else {
        // Check if email exists
        $check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $alert = "<div class='alert alert-danger mt-3 text-center'>Email is already registered.</div>";
        } else {
            // Insert new user
            $password = password_hash($password_raw, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, phone, role) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $first_name, $last_name, $email, $password, $phone, $role);

            if ($stmt->execute()) {
                // Determine redirect
                $_SESSION['user_id'] = $conn->insert_id; // get last inserted user ID
                $_SESSION['user_firstname'] = $first_name;
                $_SESSION['role'] = $role;

                // Redirect based on role
                if ($role === 'driver') {
                    header("Location: ./DriverFeatures/driver_setup.php");
                    exit();
                } else {
                header("Location: login.php");
                exit();
            }
            } else {
                $alert = "<div class='alert alert-danger mt-3 text-center'>Error: " . $stmt->error . "</div>";
            }
            $stmt->close();
        }
        $check->close();
    }
    $conn->close();
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | A To B Delivery</title>
    <?php include './includes/head_tags.php'; ?>
    <style>
        * { font-family: "Roboto Mono", monospace; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100 bg-success-subtle">
<div class="container form-container d-flex justify-content-center align-items-center mt-5">
    <div class="row shadow-lg bg-white rounded-4 overflow-hidden w-100" style="max-width: 900px;">
        <div class="col-md-6 form-image d-none d-md-block align-content-center">
            <img src="./Assets/images/logo.png" alt="Logo" class="img-fluid" />
        </div>
        <div class="col-md-6 pt-3 px-4">
            <h3 class="mb-3"><span class="fw-semibold">Join</span> <span class="text-success fw-bold">A To B Delivery</span></h3>
            <form method="POST" action="register.php">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="fw-semibold">First Name:</label>
                        <input type="text" name="first_name" class="form-control" placeholder="..." required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-semibold">Last Name:</label>
                        <input type="text" name="last_name" class="form-control" placeholder="..." required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="fw-semibold">Email Address:</label>
                    <input type="email" name="email" class="form-control fst-italic" placeholder="example@mail.com" required>
                </div>
                <div class="mb-3">
                    <label class="fw-semibold">Phone Number:</label>
                    <input type="tel" name="phone" class="form-control fst-italic" required pattern="[0-9]{7,15}" title="Enter 7-15 digits" placeholder="09xxxxxxxxx" required>
                </div>
                <div class="mb-3">
                    <label class="fw-semibold">Create Password:</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control fst-italic" required minlength="8" placeholder="Must be 8 length(E.g. Aa1234!!)">
                        <span class="input-group-text" onclick="togglePassword('password', this)" style="cursor:pointer;">
                            <i class="bi bi-eye"></i>
                        </span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="fw-semibold">Confirm Password:</label>
                    <div class="input-group">
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control fst-italic" required minlength="8" placeholder="Re-enter your password">
                        <span class="input-group-text" onclick="togglePassword('confirm_password', this)" style="cursor:pointer;">
                            <i class="bi bi-eye"></i>
                        </span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="fw-semibold">User Type:</label>
                    <select name="user_type" class="form-select" required>
                        <option value="sender">Sender</option>
                        <option value="driver">Driver</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success w-100">Register</button>
                <p class="text-center mt-3">Already have an account? <a href="login.php">Login</a>.</p>
            </form>
            <?php if (!empty($alert)) echo $alert; ?>
        </div>
    </div>
</div>
<script>
function togglePassword(fieldId, spanElement){
    const field = document.getElementById(fieldId);
    const icon = spanElement.querySelector('i');
    if(field.type==="password"){ field.type="text"; icon.classList.replace("bi-eye","bi-eye-slash"); } 
    else { field.type="password"; icon.classList.replace("bi-eye-slash","bi-eye"); }
}
</script>
</body>
</html>
