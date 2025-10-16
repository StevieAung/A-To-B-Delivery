<?php
/**
 * A To B Delivery – Admin Login Page
 * ---------------------------------------------
 * Table: admins
 * Columns: first_name, last_name, email, password_hash, role
 * ---------------------------------------------
 */

session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../Database/db.php';
include __DIR__ . '/middleware/csrf.php';

$alert = "";

// ✅ Redirect logged-in admin to dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit;
}

// ✅ Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT admin_id, first_name, last_name, role, password_hash FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows === 1) {
        $admin = $res->fetch_assoc();

        if (password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_name'] = $admin['first_name'] . ' ' . $admin['last_name'];
            $_SESSION['admin_role'] = $admin['role'];

            header("Location: dashboard.php");
            exit;
        } else {
            $alert = "<div class='alert alert-danger mt-3 p-2 text-center'>❌ Incorrect password.</div>";
        }
    } else {
        $alert = "<div class='alert alert-danger mt-3 p-2 text-center'>⚠️ No admin found with that email.</div>";
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login | A To B Delivery</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Arial', sans-serif;
      min-height: 100vh;
    }
    .login-container {
      max-width: 900px;
      margin: 5vh auto;
      box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
      border-radius: 1rem;
      overflow: hidden;
      background: #fff;
    }
    .login-image {
      background: linear-gradient(135deg,#e9f2ff,#ffffff);
    }
    .login-image img {
      max-width: 80%;
    }
    .btn-primary {
      background-color: #0d6efd;
      border: none;
    }
    .btn-primary:hover {
      background-color: #0b5ed7;
    }
    .form-label, .fw-semibold { color: #212529; }
  </style>
</head>

<body>

<div class="container d-flex justify-content-center align-items-center min-vh-100">
  <div class="row login-container w-100">
    
    <!-- Left Column: Image -->
    <div class="col-md-6 d-none d-md-flex login-image justify-content-center align-items-center">
      <img src="../Assets/images/admin_pic.png" alt="Admin Login Illustration" class="img-fluid">
    </div>

    <!-- Right Column: Login Form -->
    <div class="col-md-6 p-5 d-flex flex-column justify-content-center">
      <h3 class="mb-3">
        <span class="fw-semibold text-primary">Admin</span> Login
      </h3>

      <form method="POST" action="admin_login.php" autocomplete="off">
        <?php csrf_input(); ?>
        <div class="mb-3">
          <label class="form-label fw-semibold">Email Address:</label>
          <input type="email" name="email" class="form-control" placeholder="Enter your admin email" required />
        </div>

        <div class="mb-3">
          <label for="password" class="form-label fw-semibold">Password:</label>
          <div class="input-group">
            <input type="password" name="password" id="password" class="form-control" required minlength="8" placeholder="Enter your password">
            <span class="input-group-text" onclick="togglePassword('password', this)" style="cursor:pointer;">
              <i class="bi bi-eye" id="eye-password"></i>
            </span>
          </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2">
          <i class="bi bi-box-arrow-in-right me-1"></i> Login
        </button>
      </form>

      <!-- Register link -->
      <p class="text-center mt-3 mb-0">
        Don’t have an account? 
        <a href="manage_admins.php" class="text-decoration-none text-primary fw-semibold">Ask Super Admin to create one</a>.
      </p>

      <!-- Alert Message -->
      <?= $alert ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePassword(fieldId, spanElement) {
  const field = document.getElementById(fieldId);
  const icon = spanElement.querySelector('i');
  if (field.type === "password") {
    field.type = "text";
    icon.classList.replace("bi-eye", "bi-eye-slash");
  } else {
    field.type = "password";
    icon.classList.replace("bi-eye-slash", "bi-eye");
  }
}
</script>
</body>
</html>
