<?php
session_start();
include './Database/db.php';

// Fetch first name if not already in session
if (isset($_SESSION['user_id']) && !isset($_SESSION['user_firstname'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT first_name FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($first_name);
    if ($stmt->fetch()) {
        $_SESSION['user_firstname'] = $first_name;
    }
    $stmt->close();
}

// Alert message for redirection (optional)
$alert = $_SESSION['alert'] ?? '';
unset($_SESSION['alert']);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home - A To B Delivery</title>
    <?php include './includes/head_tags.php'; ?>
    <style>
        * { font-family: "Roboto Mono", monospace; }
        .card-link .card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .card-link:hover .card { transform: translateY(-5px); box-shadow: 0 1rem 2rem rgba(0,0,0,0.2); background-color: #e9fbe9; }
        .card-link { cursor: pointer; text-decoration: none; color: inherit; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100 bg-success-subtle">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-white bg-white shadow-sm">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <a class="navbar-brand ps-3 fw-bolder" href="home.php"><h3 class="text-success fw-bold">A TO B DELIVERY</h3></a>
        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link active text-success px-3 fw-bold" href="home.php">Home</a></li>
                <li class="nav-item"><a class="nav-link px-3 fw-bold text-success" href="DeliveryFeatures/view_deliveries.php">Delivery</a></li>
                <li class="nav-item"><a class="nav-link px-3 fw-bold" href="#">Pricing</a></li>
                <li class="nav-item"><a class="nav-link px-3 fw-bold" href="#">About Us</a></li>
                <li class="nav-item"><a class="nav-link px-3 fw-bold" href="#">Contact</a></li>
            </ul>
        </div>
        <div class="d-flex align-items-center">
            <span class="bg-success text-white rounded-circle d-inline-flex justify-content-center align-items-center" 
                style="width: 35px; height: 35px;">
                <i class="bi bi-person"></i>
            </span>
            <span class="fw-semibold px-3">
                <?php echo $_SESSION['user_firstname'] ?? "Guest"; ?>
            </span>
            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="POST" action="logout.php" class="d-inline">
                    <button type="submit" class="btn btn-success fw-semibold ms-2">Logout</button>
                </form>
            <?php else: ?>
                <a href="login.php" class="btn btn-success fw-bold ms-2">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Display Alert -->
<?php if($alert): ?>
    <div class="container mt-3">
        <?php echo $alert; ?>
    </div>
<?php endif; ?>

<!-- Hero Section -->
<section class="hero text-center my-5">
    <div class="container bg-white p-5 rounded-3 shadow">
        <div class="col">
            <img src="./Assets/images/logo.png" alt="logo" class="img-fluid mb-4" style="max-width: 200px;">
        </div>
        <div class="col">
            <h1 class="display-5 fw-bold"><span class="text-success">Fast</span> & <span class="text-success">Reliable</span> A To B Delivery</h1>
            <p class="lead mt-3">Send your packages anywhere, anytime with trusted drivers.</p>
            <?php if(isset($_SESSION['user_id'])): ?>
                <?php if($_SESSION['role'] === 'sender'): ?>
                    <a href="./DeliveryFeatures/create_delivery.php" class="btn btn-success btn-lg mt-3 me-2">Send a Package</a>
                <?php endif; ?>
                <?php if($_SESSION['role'] === 'driver'): ?>
                    <a href="./DriverFeatures/driver_dashboard.php" class="btn btn-outline-success btn-lg mt-3 me-2">Go to Dashboard</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="register.php" class="btn btn-success btn-lg mt-3 me-2">Send a Package</a>
                <a href="register.php" class="btn btn-outline-success btn-lg mt-3">Become a Driver</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="container my-5 steps">
    <h2 class="text-center fw-bold mb-4">How It Works</h2>
    <div class="row g-4">
        <div class="col-md-4">
            <a href="register.php" class="card-link text-decoration-none">
                <div class="card text-center h-100 shadow">
                    <div class="card-body">
                        <i class="bi bi-person-plus display-4 text-success mb-3"></i>
                        <h5 class="card-title">1. Sign Up</h5>
                        <p class="card-text">Create your account as a sender or driver to get started.</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="DeliveryFeatures/create_delivery.php" class="card-link text-decoration-none">
                <div class="card text-center h-100 shadow">
                    <div class="card-body">
                        <i class="bi bi-box-seam display-4 text-success mb-3"></i>
                        <h5 class="card-title">2. Book Delivery</h5>
                        <p class="card-text">Enter package details and choose your pickup & drop-off.</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="DeliveryFeatures/track_delivery.php" class="card-link text-decoration-none">
                <div class="card text-center h-100 shadow">
                    <div class="card-body">
                        <i class="bi bi-bicycle display-4 text-success mb-3"></i>
                        <h5 class="card-title">3. Get it Delivered</h5>
                        <p class="card-text">Track your package and get it delivered safely and quickly.</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer bg-success text-light pt-5 mt-auto">
    <div class="container text-center">
        &copy; <?php echo date('Y'); ?> A To B Delivery | All Rights Reserved
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
