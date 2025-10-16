<?php
// includes/navbar.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$current_page = basename($_SERVER['PHP_SELF']); // detect current page

// Dynamically detect base path (one level up if inside subfolder)
$base_path = (strpos($_SERVER['PHP_SELF'], '/Delivery/') !== false ||
              strpos($_SERVER['PHP_SELF'], '/Driver/') !== false)
              ? '../'
              : './';

// Ensure CSRF token exists
if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
?>
<!-- =======================================
     A TO B DELIVERY NAVBAR (Bootstrap 5.3)
     ======================================= -->
<nav class="navbar navbar-expand-lg fixed-top bg-white shadow-sm py-2 transition-all" id="mainNavbar">
  <div class="container-fluid px-4">

    <!-- Brand -->
    <a class="navbar-brand fw-bolder d-flex align-items-center" href="<?= $base_path ?>home.php">
      <i class="bi bi-bicycle text-success me-2 fs-3"></i>
      <h4 class="text-success fw-bold mb-0">A TO B DELIVERY</h4>
    </a>

    <!-- Navbar toggler for mobile -->
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <i class="bi bi-list fs-2 text-success"></i>
    </button>

    <!-- Nav links -->
    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
      <ul class="navbar-nav fw-semibold">

        <li class="nav-item px-2">
          <a class="nav-link <?= $current_page === 'home.php' ? 'active text-success fw-bold' : 'text-dark'; ?>"
             href="<?= $base_path ?>home.php">Home</a>
        </li>

        <li class="nav-item px-2">
          <a class="nav-link <?= $current_page === 'services.php' ? 'active text-success fw-bold' : 'text-dark'; ?>"
             href="<?= $base_path ?>services.php">Services</a>
        </li>

        <li class="nav-item px-2">
          <a class="nav-link <?= $current_page === 'track_delivery.php' ? 'active text-success fw-bold' : 'text-dark'; ?>"
             href="<?= $base_path ?>Delivery/track_delivery.php">My Delivery</a>
        </li>

        <li class="nav-item px-2">
          <a class="nav-link <?= $current_page === 'pricing.php' ? 'active text-success fw-bold' : 'text-dark'; ?>"
             href="<?= $base_path ?>pricing.php">Pricing</a>
        </li>
        <li class="nav-item px-2">
          <a class="nav-link <?= $current_page === 'feedbacks.php' ? 'active text-success fw-bold' : 'text-dark'; ?>"
             href="<?= $base_path ?>feedbacks.php">Feedbacks</a>
        </li>
        <li class="nav-item px-2">
          <a class="nav-link <?= $current_page === 'about.php' ? 'active text-success fw-bold' : 'text-dark'; ?>"
             href="<?= $base_path ?>about.php">About Us</a>
        </li>

        <li class="nav-item px-2">
          <a class="nav-link <?= $current_page === 'contact.php' ? 'active text-success fw-bold' : 'text-dark'; ?>"
             href="<?= $base_path ?>contact.php">Contact</a>
        </li>
      </ul>
    </div>

    <!-- User / Auth Controls -->
    <div class="d-flex align-items-center">
      <div class="bg-success text-white rounded-circle d-inline-flex justify-content-center align-items-center shadow-sm"
           style="width: 36px; height: 36px;">
        <i class="bi bi-person fs-6"></i>
      </div>

      <span class="fw-semibold px-2">
        <?= htmlspecialchars($_SESSION['user_firstname'] ?? "Guest"); ?>
      </span>

      <?php if (isset($_SESSION['user_id'])): ?>
        <form method="POST" action="<?= $base_path ?>logout.php" class="d-inline">
          <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
          <button type="submit" class="btn btn-outline-success fw-semibold ms-2 px-3">Logout</button>
        </form>
      <?php else: ?>
        <a href="<?= $base_path ?>login.php" class="btn btn-success fw-bold ms-2 px-3">Login</a>
      <?php endif; ?>
    </div>

  </div>
</nav>

<!-- =======================
     NAVBAR STYLING
     ======================= -->
<style>
  .navbar, .nav-link, .btn { transition: all 0.3s ease; }

  .navbar .nav-link.active { border-bottom: 3px solid var(--bs-success); }

  .navbar .nav-link:hover {
    color: var(--bs-success) !important;
    transform: translateY(-1px);
  }

  #mainNavbar.scrolled {
    background: rgba(255, 255, 255, 0.92);
    backdrop-filter: blur(6px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  .navbar-brand:hover h4 {
    color: var(--bs-success);
    text-shadow: 0 0 5px rgba(25,135,84,0.4);
  }

  .btn-success:hover { filter: brightness(1.1); }
  .btn-outline-success:hover {
    background-color: var(--bs-success);
    color: #fff;
  }
</style>

<!-- =======================
     SCROLL EFFECT SCRIPT
     ======================= -->
<script>
  window.addEventListener("scroll", () => {
    const navbar = document.getElementById("mainNavbar");
    if (window.scrollY > 40) navbar.classList.add("scrolled");
    else navbar.classList.remove("scrolled");
  });
</script>
