<?php
/**
 * feedbacks.php (Enhanced + Fixed Paths + Themed)
 * --------------------------------------------------------
 * A To B Delivery – View All Feedbacks (Bilingual + Card Style)
 *
 * Author: Sai Htet Aung Hlaing
 * Updated: 2025-10-10
 * --------------------------------------------------------
 */

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include './Database/db.php'; // ✅ Fixed correct path

$feedbacks = [];

if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("
        SELECT 
            f.feedback_id, f.rating, f.comments, f.created_at,
            dr.request_id, dr.pickup_location, dr.drop_location,
            d.first_name AS driver_fname, d.last_name AS driver_lname,
            s.first_name AS sender_fname, s.last_name AS sender_lname
        FROM feedback f
        JOIN delivery_requests dr ON f.request_id = dr.request_id
        LEFT JOIN users d ON f.driver_id = d.user_id
        LEFT JOIN users s ON f.sender_id = s.user_id
        ORDER BY f.created_at DESC
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $feedbacks[] = $row;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Feedbacks | A To B Delivery</title>
<link rel="icon" type="image/png" href="./Assets/images/logo.png">
<?php include './includes/head_tags.php'; ?> 
<style>
body {
  background-color: var(--bs-success-bg-subtle);
  padding-top: 100px; /* ⬆️ Increased to give space below navbar */
  font-family: "Roboto Mono", monospace;
}

/* Myanmar text */
.mm-text {
  font-family: "Pyidaungsu", sans-serif;
  font-size: 0.95rem;
  color: #555;
  line-height: 1.6;
}

/* Hero Section */
.hero {
  padding-top: 4rem; /* ⬆️ Increased for breathing room */
  padding-bottom: 4rem; /* added bottom padding for balance */
  text-align: center;
}

.hero .card {
  border: none;
  border-radius: 20px;
  background: #fff;
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
  padding: 3rem 2rem;
  margin: auto;
  max-width: 1100px;
}

/* Centering Logo */
.hero .card .d-flex {
  display: flex;
  justify-content: center; /* Horizontally center the logo */
}

.hero .card img {
  max-width: 130px; /* Ensure the logo doesn't exceed this width */
}

/* Feedback cards */
.feedback-card {
  background: #fff;
  border: none;
  border-radius: 1rem;
  box-shadow: 0 6px 18px rgba(0,0,0,0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  padding: 2rem; /* ⬆️ Slightly increased padding for cleaner look */
  margin-top: 1rem;
  margin-bottom: 1.5rem;
}
.feedback-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 28px rgba(0,0,0,0.15);
}

/* Fade-in Animation */
.fade-in { opacity: 0; transform: translateY(25px); transition: all 0.8s ease; }
.fade-in.visible { opacity: 1; transform: translateY(0); }

/* Section Title */
h2.section-title {
  text-align: center;
  font-weight: 700;
  color: #198754;
  margin-top: 2rem; /* ⬆️ Added breathing space above */
  margin-bottom: 2.5rem; /* increased slightly for better spacing */
}

/* Add bottom breathing room before footer */
section.container:last-of-type {
  margin-bottom: 5rem; /* ⬆️ matches home page layout rhythm */
}

/* Responsive Tweaks */
@media (max-width: 768px) {
  .hero {
    padding-top: 3rem;
    padding-bottom: 3rem;
  }
  .feedback-card {
    padding: 1.5rem;
    margin-bottom: 1rem;
  }
}
</style>

</head>

<body class="d-flex flex-column min-vh-100">
<?php include './includes/navbar.php'; ?>

<!-- =======================
     HERO SECTION
======================= -->
<section class="hero fade-in">
  <div class="container">
    <div class="card mx-auto text-center">
      <!-- Center the logo image -->
      <div class="d-flex justify-content-center mb-4">
        <img src="./Assets/images/logo.png" alt="A To B Delivery Logo" 
             class="img-fluid" style="max-width: 130px;">
      </div>
      <h1 class="fw-bold mb-3 text-success">User Feedbacks</h1>
      <p class="lead text-muted">View all feedbacks submitted by users for deliveries.</p>
      <p class="mm-text">ပစ္စည်းပို့ခြင်းအတွက် အသုံးပြုသူများမှ အကြောင်းပြန်လွှာများကို ဤနေရာတွင် ကြည့်ရှုနိုင်ပါသည်။</p>
    </div>
  </div>
</section>

<!-- =======================
     FEEDBACK CARDS SECTION
     ======================= -->
<section class="container fade-in">
  <h2 class="section-title">All Feedbacks <br><span class="mm-text">အားလုံး၏ အကြောင်းပြန်လွှာများ</span></h2>

  <?php if (empty($feedbacks)): ?>
    <div class="alert alert-warning text-center">
        No feedbacks have been submitted yet.
    </div>
    <div class="alert alert-warning text-center mm-text">
        အကြောင်းပြန်လွှာများ မရှိပါ။
    </div>
  <?php else: ?>
  <div class="row g-4">
    <?php foreach ($feedbacks as $feedback): ?>
      <div class="col-md-4">
        <div class="feedback-card text-center">
          <h5 class="text-success mb-2">Delivery #<?= htmlspecialchars($feedback['request_id']) ?></h5>
          <p class="mb-1"><strong>From:</strong> <?= htmlspecialchars($feedback['pickup_location']) ?></p>
          <p class="mb-1"><strong>To:</strong> <?= htmlspecialchars($feedback['drop_location']) ?></p>
          <p class="mb-1"><strong>Driver:</strong> <?= htmlspecialchars($feedback['driver_fname'] . ' ' . $feedback['driver_lname']) ?></p>
          <p class="mb-3"><strong>Sender:</strong> <?= htmlspecialchars($feedback['sender_fname'] . ' ' . $feedback['sender_lname']) ?></p>

          <div class="star-rating mb-2">
            <?php 
              $driver_rating = $feedback['rating'];
              for ($i = 1; $i <= 5; $i++) {
                echo $i <= $driver_rating ? '<i class="fas fa-star"></i>' : '<i class="fas fa-star-o"></i>';
              }
            ?>
          </div>

          <p class="text-muted fst-italic mb-2">“<?= htmlspecialchars($feedback['comments']) ?>”</p>
          <p><small class="text-muted">Submitted on <?= date('F j, Y', strtotime($feedback['created_at'])) ?></small></p>

          <hr>
          <p class="mm-text"><strong>မှတ်ချက်များ:</strong> <?= htmlspecialchars($feedback['comments']) ?></p>
          <p class="mm-text text-muted"><small>တင်သွင်းသည့်ရက်စွဲ: <?= date('F j, Y, g:i a', strtotime($feedback['created_at'])) ?></small></p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</section>

<?php include './includes/footer.php'; ?>

<script>
const fadeEls=document.querySelectorAll('.fade-in');
const show=()=>{const t=window.innerHeight*0.9;
fadeEls.forEach(e=>{if(e.getBoundingClientRect().top<t)e.classList.add('visible');});};
window.addEventListener('scroll',show);window.addEventListener('load',show);
</script>
</body>
</html>
