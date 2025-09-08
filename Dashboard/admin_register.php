<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Include database connection
session_start();
include './Database/db.php';

if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'super_admin') {
    die("Access denied. Only super admins can register new admins.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO admins (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $first_name, $last_name, $email, $password, $role);

    if ($stmt->execute()) {
        echo "Admin registered successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <title>Admin Register</title>
    <?php include 'head_tags.php'; ?>
</head>
<body class="d-flex flex-column min-vh-100 bg-primary-subtle">
    <div class="container form-container d-flex justify-content-center align-items-center mt-5">
        <div class="row shadow-lg bg-white rounded-4 overflow-hidden w-100" style="max-width: 900px;">
            
            <!-- Left Column: Image -->
            <div class="col-md-6 form-image d-none d-md-block align-content-center">
                <img
                    src="./Assets/adminPic.jpeg"
                    alt="Logo Image"
                    class="img-fluid"
                /> 
            </div>

            <!-- Right Column: Form -->
            <div class="col-md-6 pt-3 px-4">
                <h3 class="mb-3"><span class="fw-semibold text-primary">Admin</span> <span class="">Register</span></h3>
                <form method="POST" action="admin_register.php">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="fw-semibold">First Name:</label>
                        <input type="text" name="first_name" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-semibold">Last Name:</label>
                        <input type="text" name="last_name" class="form-control" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="fw-semibold">Email Address:</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Create Password:</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control" required minlength="8">
                        <span class="input-group-text" onclick="togglePassword('password', this)" style="cursor:pointer;">
                            <i class="bi bi-eye" id="eye-password"></i>
                        </span>
                    </div>
                </div>
                <div class="mb-3">
                    <label>Role</label>
                    <select name="role" class="form-control" required>
                        <option value="admin">Admin</option>
                        <option value="super_admin">Super Admin</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary w-100">Register Admin</button>
                <p class="text-center mt-3">Already have an account? <a href="login.php">Login</a>.</p>
            </form>

            <?php if (!empty($alert)) echo $alert; ?>
            
            </div>
        </div>
    </div>

    

<script
    src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
    crossorigin="anonymous"
></script>
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
    integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
    crossorigin="anonymous"
></script>
<script>
    function togglePassword(fieldId, spanElement) {
        const field = document.getElementById(fieldId);
        const icon = spanElement.querySelector('i');

        if (field.type === "password") {
            field.type = "text";
            icon.classList.remove("bi-eye");
            icon.classList.add("bi-eye-slash");
        } else {
            field.type = "password";
            icon.classList.remove("bi-eye-slash");
            icon.classList.add("bi-eye");
        }
    }
</script>
</body>
</html>
