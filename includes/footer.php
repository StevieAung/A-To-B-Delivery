<!-- includes/footer.php -->
<footer class="bg-success text-white pt-5 pb-3 position-relative">
  <div class="container">
    <div class="row g-4">
      <!-- About -->
      <div class="col-md-4">
        <h5 class="fw-bold text-uppercase mb-3">About Us</h5>
        <p class="small text-light">
          A to B Delivery is a fast, reliable, and transparent delivery platform connecting people and parcels across Myanmar.
          We empower senders, drivers, and businesses through innovative logistics technology and real-time tracking.
        </p>
      </div>

      <!-- Locate Us (moved left) -->
      <div class="col-md-3">
        <h5 class="fw-bold text-uppercase mb-3">Locate Us</h5>
        <p class="small mb-1"><strong>Yangon Office:</strong> No. 239, Pyay Road, Sanchaung Township, Yangon.</p>
        <p class="small mb-1"><strong>Mandalay Office:</strong> Block 3, Unit 2, 73rd Road, Mandalay.</p>
        <p class="small mb-0"><strong>Email:</strong> info@atobdelivery.com</p>
      </div>

      <!-- Quick Links (moved right, hover added) -->
      <div class="col-md-2">
        <h5 class="fw-bold text-uppercase mb-3">Quick Links</h5>
        <ul class="list-unstyled small">
          <li><a href="home.php" class="footer-link d-block py-1">Home</a></li>
          <li><a href="about.php" class="footer-link d-block py-1">About Us</a></li>
          <li><a href="services.php" class="footer-link d-block py-1">Services</a></li>
          <li><a href="contact.php" class="footer-link d-block py-1">Contact</a></li>
          <li><a href="#" class="footer-link d-block py-1">Privacy Policy</a></li>
        </ul>
      </div>

      <!-- Contact -->
      <div class="col-md-3">
        <h5 class="fw-bold text-uppercase mb-3">Contact Us</h5>
        <p class="small mb-1"><i class="bi bi-telephone-fill me-2"></i> +95 9 250 7171 66 / 67 / 68</p>
        <p class="small mb-3"><i class="bi bi-envelope-fill me-2"></i> support@atobdelivery.com</p>
        <div class="d-flex gap-3 fs-5">
          <a href="#" class="social-link"><i class="bi bi-facebook"></i></a>
          <a href="#" class="social-link"><i class="bi bi-instagram"></i></a>
          <a href="#" class="social-link"><i class="bi bi-telegram"></i></a>
          <a href="#" class="social-link"><i class="bi bi-linkedin"></i></a>
          <a href="#" class="social-link"><i class="bi bi-youtube"></i></a>
        </div>
      </div>
    </div>

    <hr class="border-light mt-4">

    <!-- Bottom -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center text-center small">
      <p class="mb-0">&copy; <?php echo date('Y'); ?> A to B Delivery. All rights reserved.</p>
      <p class="mb-0">
        <a href="#" class="footer-link me-3">Terms & Conditions</a>
        <a href="#" class="footer-link">Privacy Policy</a>
      </p>
    </div>
  </div>

  <!-- Back to Top Button -->
  <button id="backToTop" class="btn btn-light border-0 rounded-circle shadow position-fixed"
          style="bottom: 25px; right: 25px; width: 45px; height: 45px; display:none; z-index:1000;">
    <i class="bi bi-arrow-up text-success fs-5"></i>
  </button>

  <style>
    .footer-link {
      color: #f8f9fa;
      text-decoration: none;
      transition: all 0.3s ease;
    }
    .footer-link:hover {
      color: #ffc107;
      text-decoration: underline;
      transform: translateX(3px);
    }
    .social-link {
      color: #fff;
      transition: transform 0.3s ease, color 0.3s ease;
    }
    .social-link:hover {
      color: #ffc107;
      transform: scale(1.2);
    }
  </style>

  <script>
    // Back-to-top button visibility and smooth scroll
    const backToTop = document.getElementById('backToTop');
    window.addEventListener('scroll', () => {
      backToTop.style.display = window.scrollY > 200 ? 'block' : 'none';
    });
    backToTop.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  </script>
</footer>
