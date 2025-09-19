<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include './Database/db.php';

$alert = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, first_name, role, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $first_name, $role, $hashed_password);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            // Set session variables
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_firstname'] = $first_name;
            $_SESSION['role'] = $role;

            // Determine redirect page
            $redirect = ($role === 'driver') ? './DriverFeatures/driver_dashboard.php' : 'home.php';

            $alert = "<div class='alert alert-success mt-3 text-center d-flex flex-column align-items-center'>
                        <strong>Login Successful!</strong>
                        <div class='spinner-border text-success mt-2' role='status'><span class='visually-hidden'>Loading...</span></div>
                        <small class='mt-2'>Redirecting...</small>
                      </div>
                      <script>setTimeout(function(){ window.location.href='$redirect'; }, 1000);</script>";
        } else {
            $alert = "<div class='alert alert-danger mt-3 text-center'>Invalid password.</div>";
        }
    } else {
        $alert = "<div class='alert alert-danger mt-3 text-center'>No account found with this email.</div>";
    }

    $stmt->close();
    $conn->close();
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | A To B Delivery</title>
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
            <h3 class="mb-3"><span class="fw-semibold">Login</span> <span class="text-success fw-bold">A To B Delivery</span></h3>
            <form method="POST" action="login.php">
                <div class="mb-3">
                    <label class="fw-semibold">Email Address:</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="fw-semibold">Password:</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control" required>
                        <span class="input-group-text" onclick="togglePassword('password', this)" style="cursor:pointer;">
                            <i class="bi bi-eye"></i>
                        </span>
                    </div>
                </div>
                <button type="submit" class="btn btn-success w-100">Login</button>
                <p class="text-center mt-3">Don't have an account? <a href="register.php">Register</a>.</p>
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
