<?php
/**
 * home.php (Bilingual + Themed)
 * --------------------------------------------------------
 * A To B Delivery – Home Page
 *
 * Purpose:
 *  • Present main introduction, delivery features & process
 *  • Match the bilingual Aesthetic of Services page
 *
 * Author: Sai Htet Aung Hlaing
 * Updated: 2025-10-06
 * --------------------------------------------------------
 */

session_start();
include './Database/db.php';

// Fetch user first name
if (isset($_SESSION['user_id']) && !isset($_SESSION['user_firstname'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT first_name FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($first_name);
    if ($stmt->fetch()) $_SESSION['user_firstname'] = $first_name;
    $stmt->close();
}

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
body { 
  background-color: var(--bs-success-bg-subtle);
  padding-top: 100px; /* ⬆️ Increased to push all content below navbar */
}

/* Myanmar text style */
.mm-text {
  font-family: "Pyidaungsu", sans-serif;
  font-size: 0.95rem;
  color: #555;
  line-height: 1.6;
}

/* Hero Section */
.hero {
  padding-top: 4rem;  /* reduced slightly; body padding handles main offset */
  padding-bottom: 4rem;
  text-align: center;
}
.hero .card {
  border: none;
  border-radius: 20px;
  box-shadow: 0 12px 30px rgba(0,0,0,0.12);
  background-color: #fff;
  width: 90%;
  max-width: 1200px;
  margin: auto;
}
.hero .card-body {
  padding: 3rem 2rem;
}

/* Sender Quick Access */
.sender-section {
  background-color: var(--bs-success-bg-subtle);
  padding: 4rem 0; /* ⬆️ Increased top & bottom padding */
}

/* Fade Animation */
.fade-in { opacity: 0; transform: translateY(25px); transition: all 0.8s ease; }
.fade-in.visible { opacity: 1; transform: translateY(0); }

/* Card hover effect */
.card-link .card {
  border: none;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.card-link:hover .card {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

/* Remove underline globally */
a, a:hover, a:focus { text-decoration: none !important; }

/* Section Titles */
h2.section-title {
  text-align: center;
  font-weight: 700;
  color: #198754;
  margin-top: 2rem; /* ⬆️ Added extra breathing room above titles */
  margin-bottom: 2rem;
}

/* --- How It Works Card Hover Effects --- */
.how-card {
  border: none;
  border-radius: 12px;
  background: #fff;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.how-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 28px rgba(0, 0, 0, 0.18);
}

/* Add breathing room before footer */
section.container:last-of-type {
  margin-bottom: 5rem; /* slightly more footer space */
}
</style>

</head>

<body class="d-flex flex-column min-vh-100">
<?php include './includes/navbar.php'; ?>

<!-- Alert Message -->
<?php if($alert): ?>
<div class="container mt-4">
  <?= $alert ?>
</div>
<?php endif; ?>

<!-- =======================
     HERO SECTION
     ======================= -->
<section class="hero fade-in">
  <div class="card">
    <div class="card-body">
      <img src="./Assets/images/logo.png" alt="logo" class="img-fluid mb-4" style="max-width: 150px;">
      <h1 class="fw-bold text-success mb-3">Fast & Reliable Delivery</h1>
      <p class="lead text-muted mb-1">Your trusted pickup-to-drop solution — anytime, anywhere.</p>
      <p class="mm-text">အချိန်မရွေး နေရာမရွေး ယုံကြည်စိတ်ချရသော ပစ္စည်းပို့ဝန်ဆောင်မှု။</p>

      <?php if(isset($_SESSION['user_id'])): ?>
        <?php if($_SESSION['role'] === 'sender'): ?>
          <a href="./Delivery/create_delivery.php" class="btn btn-success btn-lg mt-3 me-2">
            <i class="bi bi-send me-1"></i> Send a Package
          </a>
        <?php elseif($_SESSION['role'] === 'driver'): ?>
          <a href="./Driver/driver_dashboard.php" class="btn btn-outline-success btn-lg mt-3 me-2">
            <i class="bi bi-speedometer me-1"></i> Go to Dashboard
          </a>
        <?php endif; ?>
      <?php else: ?>
        <a href="register.php" class="btn btn-success btn-lg mt-3 me-2">Send a Package</a>
        <a href="register.php" class="btn btn-outline-success btn-lg mt-3">Become a Driver</a>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- =======================
     QUICK ACCESS (Sender Only)
     ======================= -->
<?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'sender'): ?>
<section class="sender-section text-center fade-in">
  <div class="container">
    <h2 class="fw-bold text-success mb-4">Quick Access for You <br>
      <span class="mm-text">သင့်အတွက် အလွယ်တကူ အသုံးပြုနိုင်ရန်</span>
    </h2>
    <div class="row g-4 justify-content-center">

      <div class="col-md-5">
        <a href="./Delivery/view_deliveries.php" class="card-link">
          <div class="card h-100 p-4 bg-white">
            <div class="card-body">
              <i class="bi bi-truck text-success display-5 mb-3"></i>
              <h4 class="fw-semibold">View Your Deliveries</h4>
              <p class="text-muted mb-1">Check your active and past delivery requests.</p>
              <p class="mm-text">သင်ပေးပို့ခဲ့သော ပစ္စည်းများနှင့် လက်ရှိပို့ဆောင်မှုများကို စစ်ဆေးနိုင်ပါသည်။</p>
            </div>
          </div>
        </a>
      </div>

      <div class="col-md-5">
        <a href="./Delivery/track_delivery.php" class="card-link">
          <div class="card h-100 p-4 bg-white">
            <div class="card-body">
              <i class="bi bi-geo-alt text-success display-5 mb-3"></i>
              <h4 class="fw-semibold">Track Your Delivery</h4>
              <p class="text-muted mb-1">Enter your tracking ID to view real-time status.</p>
              <p class="mm-text">ပစ္စည်း၏ လမ်းကြောင်းနှင့် တည်နေရာကို အချိန်နှင့်တစ်ပြေးညီ စောင့်ကြည့်နိုင်ပါသည်။</p>
            </div>
          </div>
        </a>
      </div>

    </div>
  </div>
</section>
<?php endif; ?>

<!-- =======================
     HOW IT WORKS (with hover)
     ======================= -->
<section class="container fade-in">
  <h2 class="section-title">How It Works <br><span class="mm-text">လုပ်ဆောင်ပုံ</span></h2>
  <div class="row g-4">

    <!-- Step 1 -->
    <div class="col-md-4">
      <div class="card how-card text-center h-100">
        <div class="card-body py-4">
          <i class="bi bi-person-plus display-4 text-success mb-3"></i>
          <h5 class="fw-semibold">1. Sign Up</h5>
          <p class="text-muted">Create your account as a sender or driver.</p>
          <p class="mm-text">ပစ္စည်းပေးပို့သူ သို့မဟုတ် ပို့ဆောင်သူအဖြစ် အကောင့်ဖွင့်ပါ။</p>
        </div>
      </div>
    </div>

    <!-- Step 2 -->
    <div class="col-md-4">
      <div class="card how-card text-center h-100">
        <div class="card-body py-4">
          <i class="bi bi-box-seam display-4 text-success mb-3"></i>
          <h5 class="fw-semibold">2. Book Delivery</h5>
          <p class="text-muted">Enter your package details and pickup/drop points.</p>
          <p class="mm-text">ပစ္စည်းအချက်အလက်နှင့် Pickup / Drop လိပ်စာကို ထည့်ပါ။</p>
        </div>
      </div>
    </div>

    <!-- Step 3 -->
    <div class="col-md-4">
      <div class="card how-card text-center h-100">
        <div class="card-body py-4">
          <i class="bi bi-bicycle display-4 text-success mb-3"></i>
          <h5 class="fw-semibold">3. Get It Delivered</h5>
          <p class="text-muted">Track your package and receive it safely.</p>
          <p class="mm-text">ပစ္စည်းအောင်မြင်စွာ ရောက်ရှိရန်အထိ စောင့်ကြည့်ပါ။</p>
        </div>
      </div>
    </div>
    
  </div>
</section>

<!-- =======================
     FOOTER
     ======================= -->
<?php include './includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const fadeEls=document.querySelectorAll('.fade-in');
const show=()=>{const t=window.innerHeight*0.9;
fadeEls.forEach(e=>{if(e.getBoundingClientRect().top<t)e.classList.add('visible');});};
window.addEventListener('scroll',show);window.addEventListener('load',show);
</script>
</body>
</html>
