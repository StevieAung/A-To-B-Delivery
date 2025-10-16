<!-- ================================
     GLOBAL HEAD TAGS
     A To B Delivery Project
     ================================ -->
<?php
// Resolve base path for assets depending on current location
$__path = $_SERVER['PHP_SELF'] ?? '';
$__base = (strpos($__path, '/Delivery/') !== false || strpos($__path, '/Driver/') !== false || strpos($__path, '/Admin/') !== false)
  ? '../'
  : './';
?>

<!-- Meta Configuration -->
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="description" content="A To B Delivery – Fast, Reliable, and Secure Delivery Services across Myanmar." />
<meta name="keywords" content="delivery, logistics, Myanmar, courier, driver, tracking, A To B Delivery" />
<meta name="author" content="Sai Htet Aung Hlaing" />

<!-- ✅ FIXED CONTENT SECURITY POLICY -->
<meta http-equiv="Content-Security-Policy" content="
  default-src 'self' data: blob:;
  connect-src 'self' http://localhost:* https://nominatim.openstreetmap.org https://*.tile.openstreetmap.org https://unpkg.com;
  img-src 'self' data: blob: https://*.tile.openstreetmap.org https://unpkg.com;
  font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net;
  script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://unpkg.com https://cdnjs.cloudflare.com;
  style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com https://unpkg.com https://cdnjs.cloudflare.com;
  frame-ancestors 'self';
  upgrade-insecure-requests;
">

<!-- Favicon -->
<link rel="icon" type="image/png" href="<?= $__base ?>Assets/images/logo.png" />

<!-- Project Main Stylesheet -->
<link rel="stylesheet" href="<?= $__base ?>Assets/css/style.css" />

<!-- ================================
     BOOTSTRAP 5.3.3 + ICONS
     ================================ -->
<link
  href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
  rel="stylesheet"
  integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
  crossorigin="anonymous"
/>
<link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
/>

<!-- ================================
     GOOGLE FONTS
     ================================ -->
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link
  href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;600&family=Rubik:wght@400;600;700&family=Pyidaungsu&display=swap"
  rel="stylesheet"
/>

<!-- ================================
     MAP LIBRARIES (Leaflet)
     ================================ -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Leaflet Routing Machine -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
<script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>

<!-- ================================
     FONT AWESOME (optional)
     ================================ -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" referrerpolicy="no-referrer" />

<!-- ================================
     CUSTOM THEME VARIABLES
     ================================ -->
<style>
  :root {
    --bs-success-bg-subtle: #e9fbe9;
    --bs-success-border-subtle: #a8e3a8;
    --font-main: "Roboto Mono", monospace;
    --font-mm: "Pyidaungsu", sans-serif;
  }
  body {
    font-family: var(--font-main);
    background-color: var(--bs-success-bg-subtle);
  }
  .mm-text {
    font-family: var(--font-mm);
  }
</style>
