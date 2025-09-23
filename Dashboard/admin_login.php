<?php
// Include database connection
session_start();
include '../Database/db.php';
$alert = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_name'] = $admin['first_name'];
            $_SESSION['admin_role'] = $admin['role'];

            header("Location: dashboard.php");
            exit;
        } else {
            $alert = "<div class='alert alert-danger mt-3 p-2 text-center'>Invalid.</div>";
        }
    } else {
        $alert = "<div class='alert alert-danger mt-3 p-2 text-center'>No admin found with that email.</div>";
    }
}
?>
<!doctype html>
<html lang="en">
    <head>
        <title>Admin Login</title>
        <?php include '../includes/head_tags.php'; ?>
    </head>

<body class="d-flex flex-column min-vh-100 bg-primary-subtle">

     <div class="container d-flex justify-content-center align-items-center mt-5">
        <div class="row shadow-lg bg-white rounded-4 overflow-hidden w-100" style="max-width: 900px;">
            
            <!-- Left Column: Image -->
            <div class="col-md-6 d-none d-md-block form-image align-content-center">
                <img src="../Assets/images/admin_pic.png" alt="Login Image" class="img-fluid p-4" />
            </div>

            <!-- Right Column: Login Form -->
            <div class="col-md-6 p-5">
                <h3 class="mb-3"><span class="fw-semibold text-primary">Admin</span> <span class="">Login</span></h3>
                <form method="POST" action="admin_login.php">
                    <div class="mb-3">
                        <label class="fw-semibold">Email Address:</label>
                        <input type="email" name="email" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Enter Password:</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password" class="form-control" required minlength="8">
                            <span class="input-group-text" onclick="togglePassword('password', this)" style="cursor:pointer;">
                                <i class="bi bi-eye" id="eye-password"></i>
                            </span>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                    <p class="text-center mt-3">Don’t have an account? <a href="admin_register.php">Register</a>.</p>
                </form>
                <!-- Alert Message -->
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
