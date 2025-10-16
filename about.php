<?php
session_start();
include 'includes/head_tags.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About Us | A To B Delivery</title>
<?php include './includes/head_tags.php'; ?>

<style>
* { font-family: "Roboto Mono", monospace; }
body { background-color: var(--bs-success-bg-subtle); }

/* Fix overlapping logout button */
.navbar { z-index: 1030 !important; }

/* Myanmar text */
.mm-text {
  font-family: "Pyidaungsu", sans-serif;
  font-size: 0.95rem;
  color: #555;
  line-height: 1.6;
}

/* Section titles */
.section-title {
  font-weight: 700;
  margin-bottom: 1.5rem;
  color: #198754;
}

/* Layout */
.about-columns {
  padding-top: 7rem;
  padding-bottom: 3rem;
}
.about-left, .about-right {
  background-color: transparent;
}

/* Card style sections */
.card-section {
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.08);
  padding: 2.5rem;
  margin-bottom: 2rem;
  transition: all 0.3s ease;
}
.card-section:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 30px rgba(0,0,0,0.12);
}

/* Core values */
.value-card {
  background-color: #fff;
  border: none;
  border-radius: 12px;
  box-shadow: 0 6px 16px rgba(0,0,0,0.08);
  transition: all 0.4s ease;
}
.value-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 28px rgba(0,0,0,0.15);
}
.value-icon {
  font-size: 2.2rem;
  color: #198754;
  margin-bottom: 0.75rem;
}

/* Timeline */
.timeline {
  position: relative;
  margin: 0 auto;
  max-width: 700px;
  padding: 1rem 0;
}
.timeline::before {
  content: "";
  position: absolute;
  left: 50%;
  width: 3px;
  background: #198754;
  top: 0;
  bottom: 0;
  transform: translateX(-50%);
  opacity: 0.4;
}
.timeline-item {
  position: relative;
  width: 50%;
  padding: 1rem 2rem;
}
.timeline-item::before {
  content: "";
  position: absolute;
  top: 22px;
  width: 22px;
  height: 22px;
  border-radius: 50%;
  background: #198754;
  border: 3px solid #e9fbe9;
  z-index: 2;
}
.timeline-item.left::before { right: -11px; }
.timeline-item.right::before { left: -11px; }
.timeline-item[data-icon="rocket"]::after { content: "\f5fc"; }
.timeline-item[data-icon="laptop"]::after { content: "\f5fe"; }
.timeline-item[data-icon="box"]::after { content: "\f5f1"; }
.timeline-item[data-icon="globe"]::after { content: "\f5eb"; }
.timeline-item[data-icon="star"]::after { content: "\f5a2"; }
.timeline-item::after {
  font-family: "bootstrap-icons";
  font-size: 0.85rem;
  color: #fff;
  position: absolute;
  top: 25px;
  z-index: 3;
}
.timeline-item.left::after { right: -6px; }
.timeline-item.right::after { left: -6px; }

.timeline-content {
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 6px 18px rgba(0,0,0,0.08);
  padding: 1.5rem;
}
.timeline-item.left { left: 0; text-align: right; }
.timeline-item.right { left: 50%; text-align: left; }

/* Responsive Fix */
@media (max-width: 992px) {
  .timeline::before { left: 8px; }
  .timeline-item {
    width: 100%;
    padding-left: 2.5rem;
    text-align: left !important;
  }
  .timeline-item::before { left: 0; }
  .timeline-item::after { left: 5px; }
}

/* Team Section */
.team-section { background-color: #f8f9fa; padding: 4rem 0; }
.team-member img {
  width: 120px; height: 120px; object-fit: cover;
  border-radius: 50%;
  border: 3px solid #198754;
  margin-bottom: 1rem;
}
.team-member:hover { transform: translateY(-5px); }

/* CTA Section */
.cta-section {
  background-color: #198754;
  color: #fff;
  text-align: center;
  padding: 3rem 1rem;
}
.cta-section h3 {
  font-weight: 700;
  margin-bottom: 1rem;
}
.cta-section p {
  color: #d1e7dd;
  margin-bottom: 1.5rem;
}
.cta-section .btn {
  border-radius: 30px;
  padding: 0.6rem 1.5rem;
  font-weight: 600;
}

/* Footer */
footer {
  background-color: #fff;
  text-align: center;
  padding: 1.5rem 0;
  font-size: 0.9rem;
  color: #6c757d;
  border-top: 1px solid rgba(0,0,0,0.05);
}
footer a {
  color: #198754;
  text-decoration: none;
  margin: 0 .5rem;
}
footer a:hover { text-decoration: underline; }

/* Fade animation */
.fade-in {
  opacity: 0;
  transform: translateY(25px);
  transition: all 0.8s ease;
}
.fade-in.visible {
  opacity: 1;
  transform: translateY(0);
}
</style>
</head>

<body class="d-flex flex-column min-vh-100">
<?php include './includes/navbar.php'; ?>

<div class="container about-columns">
  <div class="row g-5">
    <!-- LEFT COLUMN -->
    <div class="col-lg-6 about-left fade-in">
      <!-- HERO (Card Style) -->
      <div class="card-section">
        <h1 class="fw-bold text-success mb-3">About A To B Delivery</h1>
        <p class="lead mb-2">Connecting People and Parcels — Fast, Safe, and Reliable.</p>
        <p class="mm-text">လူနှင့်ပစ္စည်းများကို ချိတ်ဆက်ပေးသော လျင်မြန်ပြီး လုံခြုံသည့် ပစ္စည်းပို့ဝန်ဆောင်မှုဖြစ်ပါသည်။</p>
      </div>

      <!-- PHILOSOPHY (Card Style) -->
      <div class="card-section">
        <h3 class="section-title">Our Delivery Philosophy<br><span class="mm-text">ပစ္စည်းပို့ ဆောင်ရွက်မှု သဘောတရား</span></h3>
        <p class="fs-5 text-muted">At A To B Delivery, we believe logistics should be simple and human. Our mission is to connect communities through fast, trusted, and efficient delivery solutions.</p>
        <p class="mm-text">A To B Delivery သည် ပစ္စည်းပို့လုပ်ငန်းကို ရိုးရှင်းလှပြီး လူသားအခြေပြုစနစ်ဖြင့် တန်ဖိုးရှိအောင် ဖန်တီးရန် ရည်ရွယ်ထားပါသည်။</p>
      </div>

      <!-- CORE VALUES -->
      <section class="bg-success-subtle p-4 rounded">
        <h3 class="section-title text-center">Our Core Values<br><span class="mm-text">ကျွန်ုပ်တို့၏ စံတန်ဖိုးများ</span></h3>
        <div class="row g-3">
          <div class="col-12">
            <div class="card value-card p-4 text-center">
              <div class="value-icon"><i class="bi bi-lightning-charge-fill"></i></div>
              <h5 class="fw-semibold">Efficiency</h5>
              <p class="text-muted small">Optimized routes and real-time tracking.</p>
              <p class="mm-text small">လမ်းကြောင်းထိရောက်မှုနှင့် အချိန်နှင့်တစ်ပြေးညီ စောင့်ကြည့်နိုင်မှု။</p>
            </div>
          </div>
          <div class="col-12">
            <div class="card value-card p-4 text-center">
              <div class="value-icon"><i class="bi bi-shield-check"></i></div>
              <h5 class="fw-semibold">Reliability</h5>
              <p class="text-muted small">Handled with care and precision.</p>
              <p class="mm-text small">သေချာစွာ ပြုလုပ်ထားသည့် ယုံကြည်ရသော ပို့ဆောင်မှု။</p>
            </div>
          </div>
          <div class="col-12">
            <div class="card value-card p-4 text-center">
              <div class="value-icon"><i class="bi bi-heart-fill"></i></div>
              <h5 class="fw-semibold">Customer Care</h5>
              <p class="text-muted small">Your satisfaction drives our service.</p>
              <p class="mm-text small">ဖောက်သည်အကျေနပ်မှုသည် ကျွန်ုပ်တို့၏ အဓိကရည်ရွယ်ချက်ဖြစ်ပါသည်။</p>
            </div>
          </div>
        </div>
      </section>
    </div>

    <!-- RIGHT COLUMN -->
    <div class="col-lg-6 about-right fade-in">
      <div class="card-section">
        <h3 class="section-title text-center mb-4">Our Journey<br><span class="mm-text">A To B Delivery ၏ ခရီးပြမှု</span></h3>
        <div class="timeline">
          <div class="timeline-item left" data-icon="rocket">
            <div class="timeline-content">
              <h5 class="fw-semibold text-success">2023 – The Beginning</h5>
              <p class="text-muted mb-1">Founded to simplify logistics across Myanmar.</p>
              <p class="mm-text">မြန်မာနိုင်ငံအနှံ့ ပစ္စည်းပို့လုပ်ငန်းကို ရိုးရှင်းစွာ စနစ်တကျ စတင်ခဲ့သည်။</p>
            </div>
          </div>
          <div class="timeline-item right" data-icon="laptop">
            <div class="timeline-content">
              <h5 class="fw-semibold text-success">2024 – Platform Launch</h5>
              <p class="text-muted mb-1">Launched real-time tracking and secure driver access.</p>
              <p class="mm-text">အချိန်နှင့်တစ်ပြေးညီ စောင့်ကြည့်နိုင်သော စနစ်ကို မိတ်ဆက်ခဲ့သည်။</p>
            </div>
          </div>
          <div class="timeline-item left" data-icon="box">
            <div class="timeline-content">
              <h5 class="fw-semibold text-success">Mid-2024 – 1,000+ Deliveries</h5>
              <p class="text-muted mb-1">Reached over one thousand successful deliveries.</p>
              <p class="mm-text">ပစ္စည်းပို့အော်ဒါ ၁၀၀၀ ခုပေါ်ကျော်သို့ ရောက်ရှိခဲ့သည်။</p>
            </div>
          </div>
          <div class="timeline-item right" data-icon="globe">
            <div class="timeline-content">
              <h5 class="fw-semibold text-success">2025 – Expansion & Innovation</h5>
              <p class="text-muted mb-1">Expanding with bilingual interfaces and smarter routing.</p>
              <p class="mm-text">မြန်မာ-အင်္ဂလိပ် နှစ်ဘာသာစနစ်နှင့် ပိုမိုတိုးတက်သည့် လမ်းကြောင်းတွက်ချက်မှုများ။</p>
            </div>
          </div>
          <div class="timeline-item left" data-icon="star">
            <div class="timeline-content">
              <h5 class="fw-semibold text-success">Beyond 2025 – The Future</h5>
              <p class="text-muted mb-1">Empowering logistics with data-driven innovation.</p>
              <p class="mm-text">ဒေတာအခြေခံ စနစ်များဖြင့် တာရှည်တည်တံ့သော ပစ္စည်းပို့နည်းလမ်းများသို့ တိုးတက်နေပါသည်။</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- MEET THE TEAM -->
<section class="team-section text-center fade-in">
  <div class="container">
    <h3 class="section-title mb-4">Meet the Team<br><span class="mm-text">အဖွဲ့အစည်းကို မိတ်ဆက်ပါ</span></h3>
    <div class="row justify-content-center">
      <div class="col-md-4 col-lg-3 mb-4">
        <div class="team-member p-3">
          <img src="Assets/images/team1.jpeg" alt="Stevie Aung">
          <h6 class="fw-semibold">Stevie Aung</h6>
          <p class="text-muted small mb-1">Founder & Lead Developer</p>
          <p class="mm-text small">တည်ထောင်သူ နှင့် ဦးဆောင်ဖွံ့ဖြိုးရေးသူ</p>
        </div>
      </div>
      <div class="col-md-4 col-lg-3 mb-4">
        <div class="team-member p-3">
          <img src="Assets/images/team2.jpeg" alt="Stevie Aung">
          <h6 class="fw-semibold">Stevie Aung</h6>
          <p class="text-muted small mb-1">UI/UX Designer</p>
          <p class="mm-text small">အသုံးပြုသူ အတွေ့အကြုံ ဒီဇိုင်နာ</p>
        </div>
      </div>
      <div class="col-md-4 col-lg-3 mb-4">
        <div class="team-member p-3">
          <img src="Assets/images/team3.jpeg" alt="Stevie Aung">
          <h6 class="fw-semibold">Stevie Aung</h6>
          <p class="text-muted small mb-1">Project Manager</p>
          <p class="mm-text small">ပရောဂျက် စီမံခန့်ခွဲသူ</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA SECTION -->
<section class="cta-section fade-in">
  <div class="container">
    <h3>Ready to send your next delivery?</h3>
    <p>Fast, reliable, and secure — A To B Delivery is here for you.</p>
    <a href="./DeliveryFeatures/create_delivery.php" class="btn btn-light me-2 text-success fw-bold">Create Delivery</a>
    <a href="contact.php" class="btn btn-outline-light fw-bold">Contact Support</a>
  </div>
</section>

<!-- FOOTER -->
<?php include './includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const fadeEls=document.querySelectorAll('.fade-in,.value-card,.team-member,.timeline-item,.timeline-content');
const reveal=()=>{const t=window.innerHeight*0.9;
fadeEls.forEach(e=>{if(e.getBoundingClientRect().top<t)e.classList.add('visible');});};
window.addEventListener('scroll',reveal);
window.addEventListener('load',reveal);
</script>
</body>
</html>
