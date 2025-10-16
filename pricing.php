<?php
/**
 * pricing.php
 * --------------------------------------------------------
 * A To B Delivery – Pricing Page (Bilingual Version)
 *
 * Purpose:
 *  • Display pricing for bike, motorbike, and car delivery services
 *  • Pricing is calculated as 1 km = 1000 Kyats
 *  • Show the prices in both English and Myanmar (Burmese)
 *
 * Author: Sai Htet Aung Hlaing
 * Updated: 2025-10-10
 * --------------------------------------------------------
 */

session_start();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing | A To B Delivery</title>
    <?php include './includes/head_tags.php'; ?>
    <style>
        * { font-family: "Roboto Mono", monospace; }
        body { background-color: var(--bs-success-bg-subtle); }

        /* Myanmar text */
        .mm-text {
            font-family: "Pyidaungsu", sans-serif;
            font-size: 0.95rem;
            color: #555;
            line-height: 1.6;
        }

        /* Fade animation */
        .fade-in { opacity:0; transform:translateY(25px); transition:all 0.8s ease; }
        .fade-in.visible { opacity:1; transform:translateY(0); }

        /* Hero */
        .hero {
            padding-top:6rem;
            text-align:center;
        }
        .hero .card {
            border:none;
            border-radius:20px;
            background:#fff;
            box-shadow:0 10px 30px rgba(0,0,0,0.1);
            padding:3rem 2rem;
        }

        /* Section spacing */
        section { margin-top:2rem; margin-bottom:2rem; }

        /* Service cards */
        .service-card {
            background:#fff;
            border:none;
            border-radius:1rem;
            box-shadow:0 6px 18px rgba(0,0,0,0.1);
            transition:transform 0.3s ease, box-shadow 0.3s ease;
            height:100%;
        }
        .service-card:hover {
            transform:translateY(-5px);
            box-shadow:0 12px 28px rgba(0,0,0,0.15);
        }
        .service-card i {
            color:#198754;
        }

        /* Pricing Table */
        .pricing-table {
            background-color: #fff;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .pricing-table h4 {
            margin-bottom: 1.5rem;
            color: #198754;
        }

        .pricing-table .price {
            font-size: 1.5rem;
            color: #198754;
        }

        /* Responsive tweaks */
        @media (max-width: 768px){
            .pricing-table {
                padding: 1.5rem;
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
      <h1 class="fw-bold mb-3 text-success">Our Pricing</h1>
      <p class="lead text-muted mb-1">
        Affordable and transparent pricing for all delivery types.
      </p>
      <p class="mm-text">
        မြန်မာနိုင်ငံတွင် အားလုံးအတွက် အစွမ်းထက်သော ဈေးနှုန်းများ။
      </p>
    </div>
  </div>
</section>

<!-- =======================
     DELIVERY PRICE SECTION
     ======================= -->
<section class="container fade-in">
  <h2 class="section-title">Delivery Prices <br><span class="mm-text">ပစ္စည်းပို့ဝန်ဆောင်မှုဈေးနှုန်းများ</span></h2>

  <div class="row g-4">
    <!-- Bike Delivery -->
    <div class="col-md-4">
      <div class="card service-card text-center p-4">
        <i class="bi bi-bicycle display-5 mb-3"></i>
        <h5 class="fw-bold mb-1">Bike Delivery</h5>
        <p class="text-muted mb-1">Delivery by bike for shorter distances.</p>
        <div class="pricing-table">
          <h4>Price per 1 km</h4>
          <p class="price">1,000 Ks</p>
        </div>
        <p class="mm-text">ကျော်ဖြတ်တိုးတက်မှုမရှိသော လမ်းကြောင်းများအတွက် စျေးနှုန်း ၁ကီလိုမီတာချင်းတွင် ၁၀၀၀ ကျပ်။</p>
      </div>
    </div>

    <!-- Motorbike Delivery -->
    <div class="col-md-4">
      <div class="card service-card text-center p-4">
        <i class="bi bi-motorcycle display-5 mb-3"></i>
        <h5 class="fw-bold mb-1">Motorbike Delivery</h5>
        <p class="text-muted mb-1">Motorbike delivery for medium distances.</p>
        <div class="pricing-table">
          <h4>Price per 1 km</h4>
          <p class="price">1,200 Ks</p>
        </div>
        <p class="mm-text">လတ်ဆတ်သော လမ်းကြောင်းများအတွက် စျေးနှုန်း ၁ကီလိုမီတာချင်းတွင် ၁၂၀၀ ကျပ်။</p>
      </div>
    </div>

    <!-- Car Delivery -->
    <div class="col-md-4">
      <div class="card service-card text-center p-4">
        <i class="bi bi-car-front display-5 mb-3"></i>
        <h5 class="fw-bold mb-1">Car Delivery</h5>
        <p class="text-muted mb-1">Car delivery for longer distances.</p>
        <div class="pricing-table">
          <h4>Price per 1 km</h4>
          <p class="price">1,500 Ks</p>
        </div>
        <p class="mm-text">ရှည်လျားသော လမ်းကြောင်းများအတွက် စျေးနှုန်း ၁ကီလိုမီတာချင်းတွင် ၁၅၀၀ ကျပ်။</p>
      </div>
    </div>
  </div>
</section>

<!-- =======================
     CTA SECTION
     ======================= -->
<section class="text-center my-5 fade-in">
  <div class="container">
    <div class="p-5 rounded-4 bg-white shadow">
      <h3 class="fw-bold mb-3 text-success">Ready to Send Your First Package?</h3>
      <p class="text-muted mb-2">Create your first delivery request now.</p>
      <p class="mm-text mb-4">သင်၏ ပထမဆုံး ပစ္စည်းပို့အော်ဒါကို ယခုပဲ တင်ပါ။</p>
      <a href="./DeliveryFeatures/create_delivery.php" class="btn btn-success btn-lg px-4">
        <i class="bi bi-send me-2"></i>Start Now
      </a>
    </div>
  </div>
</section>

<!-- Footer -->
<?php include './includes/footer.php'; ?>

<!-- Fade-in animation -->
<script>
const fadeElements=document.querySelectorAll('.fade-in');
const reveal=()=>{const trigger=window.innerHeight*0.9;
fadeElements.forEach(e=>{if(e.getBoundingClientRect().top<trigger)e.classList.add('visible');});};
window.addEventListener('scroll',reveal);window.addEventListener('load',reveal);
</script>
</body>
</html>
