<?php
/**
 * services.php (Bilingual Version)
 * --------------------------------------------------------
 * A To B Delivery – Services Page (English + Myanmar)
 *
 * Author: Sai Htet Aung Hlaing
 * Updated: 2025-10-06
 * --------------------------------------------------------
 */
session_start();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Services | A To B Delivery</title>
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

/* Process flow icons */
.flow-step {
  background:#fff;
  border-radius:50%;
  width:80px;
  height:80px;
  display:flex;
  justify-content:center;
  align-items:center;
  color:#198754;
  box-shadow:0 4px 10px rgba(0,0,0,0.1);
  margin:auto;
}
.flow-step i { font-size:2rem; }
.flow-line {
  width:60px;
  height:3px;
  background:#198754;
  margin:auto;
  opacity:0.6;
}

/* Headings */
h2.section-title {
  text-align:center;
  font-weight:700;
  color:#198754;
  margin-bottom:2.5rem;
}

/* Responsive tweaks */
@media (max-width: 768px){
  .flow-line { display:none; }
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
      <h1 class="fw-bold mb-3 text-success">Our Services</h1>
      <p class="lead text-muted mb-1">
        Fast, secure, and flexible delivery solutions for every need.
      </p>
      <p class="mm-text">
        မြန်မာနိုင်ငံအနှံ့ လျင်မြန်တိကျသော ပစ္စည်းပို့ဝန်ဆောင်မှုများကို 
        လုံခြုံစိတ်ချစွာ သုံးစွဲနိုင်ပါသည်။
      </p>
    </div>
  </div>
</section>

<!-- =======================
     SERVICE FEATURES SECTION
     ======================= -->
<section class="container fade-in">
  <h2 class="section-title">What We Offer <br><span class="mm-text">ဝန်ဆောင်မှုအမျိုးအစားများ</span></h2>
  <div class="row g-4">
    <div class="col-md-4">
      <div class="card service-card text-center p-4">
        <i class="bi bi-lightning-charge display-5 mb-3"></i>
        <h5 class="fw-bold mb-1">Express Delivery</h5>
        <p class="text-muted mb-1">Priority and same-day delivery.</p>
        <p class="mm-text">အရေးပေါ် ပစ္စည်းပို့ခြင်းများကို တစ်နေ့တည်းအတွင်း စနစ်တကျဖြေရှင်းပေးပါသည်။</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card service-card text-center p-4">
        <i class="bi bi-calendar-check display-5 mb-3"></i>
        <h5 class="fw-bold mb-1">Scheduled Pickup</h5>
        <p class="text-muted mb-1">Book your pickup at a specific time.</p>
        <p class="mm-text">သင့်အချိန်ဇယားအတိုင်း Pickup အချိန်ကို ကြိုတင်ဘွတ်ကင်လုပ်နိုင်ပါသည်။</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card service-card text-center p-4">
        <i class="bi bi-geo-alt display-5 mb-3"></i>
        <h5 class="fw-bold mb-1">Live Tracking</h5>
        <p class="text-muted mb-1">Real-time driver tracking system.</p>
        <p class="mm-text">ပစ္စည်းပို့သူ၏ တည်နေရာကို အချိန်နှင့်တစ်ပြေးညီ စောင့်ကြည့်နိုင်ပါသည်။</p>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card service-card text-center p-4">
        <i class="bi bi-shield-check display-5 mb-3"></i>
        <h5 class="fw-bold mb-1">Secure Handling</h5>
        <p class="text-muted mb-1">Every parcel handled with care.</p>
        <p class="mm-text">ပစ္စည်းတိုင်းကို လုံခြုံစိတ်ချရသော နည်းစနစ်ဖြင့် ကိုင်တွယ်ပေးပါသည်။</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card service-card text-center p-4">
        <i class="bi bi-truck display-5 mb-3"></i>
        <h5 class="fw-bold mb-1">Vehicle Variety</h5>
        <p class="text-muted mb-1">From bikes to cars — choose easily.</p>
        <p class="mm-text">အကွာအဝေးနှင့် ပစ္စည်းအရွယ်အစားအပေါ် မူတည်၍ ယာဉ်အမျိုးအစားရွေးချယ်နိုင်ပါသည်။</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card service-card text-center p-4">
        <i class="bi bi-cash-stack display-5 mb-3"></i>
        <h5 class="fw-bold mb-1">Transparent Pricing</h5>
        <p class="text-muted mb-1">No hidden fees. Pay what you see.</p>
        <p class="mm-text">အမြင်အတိုင်း ဈေးနှုန်းသိသာရှင်းလင်းပါသည်။ လျှို့ဝှက်ခများမရှိပါ။</p>
      </div>
    </div>
  </div>
</section>

<!-- =======================
     DELIVERY PROCESS FLOW
     ======================= -->
<section class="container fade-in">
  <h2 class="section-title">How Our Delivery Works <br><span class="mm-text">ပစ္စည်းပို့ခြင်း လုပ်ငန်းစဉ်</span></h2>
  <div class="row text-center align-items-center justify-content-center">
    <div class="col-md-2 col-4 mb-3">
      <div class="flow-step"><i class="bi bi-person-plus"></i></div>
      <p class="fw-semibold mt-3 mb-0">Sign Up</p>
      <p class="mm-text">အကောင့်ဖွင့်ခြင်း</p>
    </div>
    <div class="col-md-1 d-none d-md-block"><div class="flow-line"></div></div>
    <div class="col-md-2 col-4 mb-3">
      <div class="flow-step"><i class="bi bi-box"></i></div>
      <p class="fw-semibold mt-3 mb-0">Book Delivery</p>
      <p class="mm-text">ပစ္စည်းပို့အော်ဒါတင်ခြင်း</p>
    </div>
    <div class="col-md-1 d-none d-md-block"><div class="flow-line"></div></div>
    <div class="col-md-2 col-4 mb-3">
      <div class="flow-step"><i class="bi bi-bicycle"></i></div>
      <p class="fw-semibold mt-3 mb-0">Pickup & Transit</p>
      <p class="mm-text">ပစ္စည်းကောက်ယူခြင်းနှင့် လမ်းလျှောက်ခြင်း</p>
    </div>
    <div class="col-md-1 d-none d-md-block"><div class="flow-line"></div></div>
    <div class="col-md-2 col-4 mb-3">
      <div class="flow-step"><i class="bi bi-geo-alt"></i></div>
      <p class="fw-semibold mt-3 mb-0">Live Tracking</p>
      <p class="mm-text">အချိန်နှင့်တစ်ပြေးညီ စောင့်ကြည့်ခြင်း</p>
    </div>
    <div class="col-md-1 d-none d-md-block"><div class="flow-line"></div></div>
    <div class="col-md-2 col-4 mb-3">
      <div class="flow-step"><i class="bi bi-check2-circle"></i></div>
      <p class="fw-semibold mt-3 mb-0">Delivered</p>
      <p class="mm-text">ပစ္စည်းအောင်မြင်စွာ ရောက်ရှိခြင်း</p>
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
