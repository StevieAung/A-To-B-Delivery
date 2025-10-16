<?php
/** 
 * track_delivery.php
 * --------------------------------------------------------
 * A To B Delivery – Sender Live Tracking Page
 * 
 * Author: Sai Htet Aung Hlaing 
 * Updated: 2025-10-10
 */

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../Database/db.php';

$delivery = null;
$error = '';
$request_id = $_GET['id'] ?? ($_GET['request_id'] ?? null);

if ($request_id) {
    $sql = "
        SELECT 
            dr.*, 
            s.first_name AS sender_fname, s.last_name AS sender_lname, s.phone AS sender_phone, 
            d.first_name AS driver_fname, d.last_name AS driver_lname, d.phone AS driver_phone, 
            dp.vehicle_type, dp.vehicle_number, dp.profile_photo, 
            dt.current_lat, dt.current_long, 
            p.amount AS payment_amount, p.payment_status AS payment_status,
            (SELECT AVG(rating) FROM feedback WHERE driver_id = d.user_id) AS driver_rating,
            (SELECT COUNT(*) FROM feedback WHERE driver_id = d.user_id) AS driver_rating_count
        FROM delivery_requests dr 
        JOIN users s ON dr.sender_id = s.user_id
        LEFT JOIN users d ON dr.driver_id = d.user_id
        LEFT JOIN driver_profiles dp ON d.user_id = dp.user_id
        LEFT JOIN delivery_tracking dt ON dr.request_id = dt.request_id
        LEFT JOIN payments p ON dr.request_id = p.request_id
        WHERE dr.request_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $delivery = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$delivery) {
        $error = "⚠️ Delivery not found. Please check your tracking ID.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Track Delivery | A To B Delivery</title>
    <link rel="icon" type="image/png" href="../Assets/images/logo.png">
    <?php include '../includes/head_tags.php'; ?>
    <style>
        body {
            background-color: var(--bs-success-bg-subtle);
            font-family: "Roboto Mono", monospace;
            display: flex;
            flex-direction: column;
            height: 100vh; /* Full height */
            padding-top: 50px; /* Adjusted padding for centering */
        }
        .tracking-section {
            display: flex;
            justify-content: center;
            align-items: flex-start; /* Align items at the top */
            flex: 1; /* Take up the remaining space */
            padding: 2rem 1rem;
        }

        .tracking-card { 
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.12);
            width: 100%;
            max-width: 1200px; /* Center the card */
            padding: 2rem;
            display: flex;
            flex-direction: column;
        }
        .header-section { text-align: center; margin-bottom: 2rem; }
        .header-section h2 { font-weight: 700; color: #198754; }
        .header-section p { color: #6c757d; font-size: 1rem; }
        .card-body { display: flex; justify-content: space-between; margin-bottom: 2rem; }
        .map-col { flex: 0.7; padding-right: 20px; }
        .map-actions { display:flex; gap: .5rem; margin-bottom: .5rem; flex-wrap: wrap; }
        .map-actions .btn { padding: .25rem .5rem; font-size: .85rem; }
        
        /* Map styling */
        #map { 
            height: 400px; 
            border-radius: 1rem; 
            width: 100%;
        }

        .timeline-container { 
            flex: 0.3; 
            padding-left: 20px; 
        }

        .subheading { font-size: 1.25rem; font-weight: 600; color: #198754; margin-top: 20px; margin-bottom: 15px; }
        .timeline { list-style: none; padding: 0; position: relative; margin-left: 0; }
        .timeline:before { content: ''; position: absolute; top: 0; bottom: 0; width: 3px; background: #198754; left: 1.5rem; }
        .timeline-item { margin-bottom: 2rem; position: relative; padding-left: 3.5rem; display: flex; flex-direction: column; align-items: flex-start; }
        .timeline-icon { position: absolute; left: 0; width: 3rem; height: 3rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #e9ecef; color: #6c757d; font-size: 1.5rem; top: 0; z-index: 2; }
        .timeline-item.active .timeline-icon { background: #198754; color: #fff; }
        .feedback-form { background-color: #f8f9fa; padding: 1.5rem; border-radius: 1rem; box-shadow: 0 4px 12px rgba(0,0,0,0.08); margin-top: 1rem; }
        .star-rating { display: flex; justify-content: flex-start; font-size: 1.5rem; cursor: pointer; }
        .star-rating i { color: #ccc; margin-right: 0.2rem; }
        .star-rating i.selected, .star-rating i.hover { color: #ffcc00; }
        #thankYouCard { display: none; background-color: #e9fbe9; border-radius: 1rem; padding: 2rem; text-align: center; margin-top: 1rem; }
        .delivery-details { background-color: #f8f9fa; border-radius: 1rem; padding: 1.5rem; margin-top: 1rem; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .driver-info { display: flex; align-items: center; gap: 0.8rem; margin-top: 1rem; }
        .driver-info img { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid #198754; }
        @media (max-width: 768px) { .tracking-section { flex-direction: column; } #map { margin-bottom: 1rem; width: 100%; } .timeline { margin-top: 1rem; } }
        footer {
            margin-top: auto; /* Push the footer to the bottom */
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <section class="tracking-section fade-in">
        <div class="tracking-card">
            <?php if (!$request_id || !$delivery): ?>
                <!-- Search Box Only -->
                <div class="text-center p-4">
                    <h2 class="text-success fw-semibold">Track Your Delivery</h2>
                    <p>Enter your Delivery Tracking ID to view your package status.</p>
                    <form action="" method="GET" class="d-flex justify-content-center mt-3">
                        <input type="text" name="id" class="form-control w-50" placeholder="Enter Delivery ID" required>
                        <button type="submit" class="btn btn-success ms-2">Track</button>
                    </form>
                </div>
            <?php else: ?>
                <!-- Existing Map + Status + Details Sections -->
                <div class="header-section">
                    <h2>Track Your Delivery</h2>
                    <p>View real-time updates and the status of your package.</p>
                </div>

                <div class="card-body">
                    <div class="map-col">
                        <div class="map-actions">
                            <button type="button" id="btnFitAll" class="btn btn-outline-success btn-sm"><i class="bi bi-bounding-box"></i> Fit All</button>
                            <button type="button" id="btnCenterDriver" class="btn btn-outline-primary btn-sm"><i class="bi bi-crosshair"></i> Driver</button>
                            <button type="button" id="btnCenterPD" class="btn btn-outline-secondary btn-sm"><i class="bi bi-geo"></i> Pickup/Drop</button>
                        </div>
                        <div id="map"></div>
                        <div id="mapInfo" class="text-muted small mt-2"></div>
                    </div>

                    <div class="timeline-container">
                        <div class="subheading">Delivery Status</div>
                        <ul class="timeline" id="timeline">
                            <?php
                            $statuses = ['pending','accepted','picked_up','in_transit','delivered'];
                            $icons = ['bi-clock-history','bi-person-check','bi-box-seam','bi-bicycle','bi-house-check-fill'];
                            $current_status_index = isset($delivery['delivery_status']) ? array_search($delivery['delivery_status'], $statuses) : 0;
                            foreach ($statuses as $i => $status):
                            ?>
                                <li class="timeline-item <?= ($i <= $current_status_index) ? 'active' : '' ?>" id="status-<?= $status ?>">
                                    <div class="timeline-icon"><i class="bi <?= $icons[$i] ?>"></i></div>
                                    <div class="fw-bold mt-2"><?= ucfirst(str_replace('_', ' ', $status)) ?></div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="delivery-details">
                            <h5 class="text-success fw-bold">Delivery Details</h5>
                            <p><strong>Sender:</strong> <?= isset($delivery['sender_fname']) ? htmlspecialchars($delivery['sender_fname'].' '.$delivery['sender_lname']) : 'N/A' ?> (<?= isset($delivery['sender_phone']) ? htmlspecialchars($delivery['sender_phone']) : 'N/A' ?>)</p>
                            <p><strong>Pickup:</strong> <?= isset($delivery['pickup_location']) ? htmlspecialchars($delivery['pickup_location']) : 'N/A' ?></p>
                            <p><strong>Drop:</strong> <?= isset($delivery['drop_location']) ? htmlspecialchars($delivery['drop_location']) : 'N/A' ?></p>
                            <p><strong>Item:</strong> <?= isset($delivery['item_description']) ? htmlspecialchars($delivery['item_description']) : 'N/A' ?></p>
                            <p><strong>Weight:</strong> <?= isset($delivery['weight']) ? htmlspecialchars($delivery['weight']) : 'N/A' ?> kg</p>
                            <p><strong>Amount:</strong> <?= isset($delivery['payment_amount']) ? htmlspecialchars($delivery['payment_amount']) : '0.00' ?> Ks</p>
                            <p><strong>Payment Status:</strong> <?= isset($delivery['payment_status']) ? htmlspecialchars($delivery['payment_status']) : 'Pending' ?></p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <?php if (isset($delivery['driver_id']) && $delivery['driver_id']): ?>
                            <div class="driver-info-card">
                                <h5 class="text-success fw-bold">Assigned Driver</h5>
                                <div class="driver-info">
                                    <img src="<?= isset($delivery['profile_photo']) && $delivery['profile_photo'] ? '../uploads/drivers/' . htmlspecialchars($delivery['profile_photo']) : '../Assets/images/driver.png' ?>" alt="Driver Photo">
                                    <div>
                                        <strong><?= isset($delivery['driver_fname']) ? htmlspecialchars($delivery['driver_fname'].' '.$delivery['driver_lname']) : 'N/A' ?></strong><br>
                                        <span>Phone: <?= isset($delivery['driver_phone']) ? htmlspecialchars($delivery['driver_phone']) : 'N/A' ?></span><br>
                                        <span>Rating: <?= isset($delivery['driver_rating']) ? ($delivery['driver_rating'] ? round($delivery['driver_rating'], 1)." / 5 ({$delivery['driver_rating_count']} ratings)" : "No ratings yet") : "No ratings yet" ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="driver-info-card">
                                <h5 class="text-warning fw-bold">Waiting for a nearby driver to accept</h5>
                                <div class="loading-circle">
                                    <div class="spinner-border" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                                <p>We are looking for the nearest available driver to pick up your delivery.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (isset($delivery['delivery_status']) && $delivery['delivery_status'] === 'delivered' && isset($_SESSION['user_id'])): ?>
                    <div class="feedback-form mt-3" id="feedbackSection">
                        <h6>Leave Your Feedback</h6>
                        <form id="feedbackForm">
                            <div class="star-rating" id="starRating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star" data-value="<?= $i ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <input type="hidden" name="rating" id="ratingInput" value="0">
                            <div class="mb-3 mt-2">
                                <label for="comments" class="form-label">Comments</label>
                                <textarea name="comments" id="comments" class="form-control" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Submit Feedback</button>
                        </form>
                    </div>
                <?php endif; ?>

                <div id="thankYouCard">
                    <h4 class="fw-bold text-success">Thank You!</h4>
                    <p>Thank you for trusting our services. Ready for your next delivery?</p>
                    <button class="btn btn-success" onclick="window.location.href='../home.php'">Go to Home</button>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>

    <!-- Leaflet Routing Machine (CSS + JS) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
    <script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>

    <script>
// ===== MAP INITIALIZATION =====
const map = L.map('map');
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

let pickupLat = <?= json_encode($delivery['pickup_lat'] ?? null) ?>;
let pickupLng = <?= json_encode($delivery['pickup_lng'] ?? null) ?>;
let driverLat = <?= json_encode($delivery['current_lat'] ?? null) ?>;
let driverLng = <?= json_encode($delivery['current_long'] ?? null) ?>;
let dropLat = <?= json_encode($delivery['drop_lat'] ?? null) ?>;
let dropLng = <?= json_encode($delivery['drop_lng'] ?? null) ?>;

let pickupMarker, driverMarker, dropMarker, routeControl;
const infoEl = document.getElementById('mapInfo');

// ===== Helper: labeled marker =====
function addLabeledMarker(lat, lng, label, color) {
  if (!lat || !lng) return null;
  return L.marker([lat, lng], {
    icon: L.divIcon({
      className: 'custom-marker',
      html: `<div style="background:${color};width:12px;height:12px;border-radius:50%"></div>`,
      iconSize: [20, 20]
    })
  }).addTo(map).bindTooltip(label, { permanent: true, direction: 'top' }).openTooltip();
}

// ===== Ensure pickup/drop coords =====
async function ensureCoords() {
  async function geocode(q) {
    try {
      const r = await fetch('../api/geocode_forward.php?q=' + encodeURIComponent(q));
      const d = await r.json();
      return d.success ? [parseFloat(d.lat), parseFloat(d.lon)] : [null, null];
    } catch { return [null, null]; }
  }
  if (!pickupLat || !pickupLng) [pickupLat, pickupLng] = await geocode(<?= json_encode($delivery['pickup_location'] ?? '') ?>);
  if (!dropLat || !dropLng) [dropLat, dropLng] = await geocode(<?= json_encode($delivery['drop_location'] ?? '') ?>);
}

// ===== Place markers =====
function placeInitialMarkers() {
  if (pickupLat && pickupLng) pickupMarker = addLabeledMarker(pickupLat, pickupLng, 'Pickup', 'green');
  if (dropLat && dropLng) dropMarker = addLabeledMarker(dropLat, dropLng, 'Drop', 'red');
  if (driverLat && driverLng) driverMarker = addLabeledMarker(driverLat, driverLng, 'Driver', 'blue');
  fitToMarkers();
}

// ===== Fit view =====
function fitToMarkers() {
  const pts = [];
  if (pickupLat && pickupLng) pts.push([pickupLat, pickupLng]);
  if (dropLat && dropLng) pts.push([dropLat, dropLng]);
  if (driverLat && driverLng) pts.push([driverLat, driverLng]);
  if (!pts.length) return map.setView([21.95, 96.08], 13);
  map.fitBounds(L.latLngBounds(pts).pad(0.2));
}

// ===== Smooth marker movement =====
function moveMarkerSmoothly(marker, newLat, newLng) {
  const old = marker.getLatLng();
  const steps = 20, dLat = (newLat - old.lat) / steps, dLng = (newLng - old.lng) / steps;
  let i = 0;
  const interval = setInterval(() => {
    if (i >= steps) return clearInterval(interval);
    marker.setLatLng([old.lat + dLat * i, old.lng + dLng * i]);
    i++;
  }, 50);
}

// ===== Init map =====
(async function init() {
  await ensureCoords();
  placeInitialMarkers();
  if (!driverLat && pickupLat && pickupLng && dropLat && dropLng) {
    routeControl = L.Routing.control({
      waypoints: [L.latLng(pickupLat, pickupLng), L.latLng(dropLat, dropLng)],
      createMarker: () => null,
      lineOptions: { styles: [{ color: 'green', opacity: 0.6, weight: 5 }] }
    }).on('routesfound', e => {
      const s = e.routes[0].summary;
      infoEl.textContent = `Pickup → Drop: ${(s.totalDistance/1000).toFixed(2)} km • ETA: ${Math.round(s.totalTime/60)} mins`;
    }).addTo(map);
  }
})();
setTimeout(() => map.invalidateSize(), 400);

// ===== Buttons =====
document.getElementById('btnFitAll').addEventListener('click', fitToMarkers);
document.getElementById('btnCenterDriver').addEventListener('click', () => { if (driverMarker) map.setView(driverMarker.getLatLng(), 14); });
document.getElementById('btnCenterPD').addEventListener('click', () => {
  if (pickupLat && dropLat) map.fitBounds(L.latLngBounds([L.latLng(pickupLat,pickupLng), L.latLng(dropLat,dropLng)]).pad(0.2));
});

// ===== Update driver marker =====
async function updateDriverLocation() {
  try {
    const res = await fetch('../Driver/get_driver_location.php?request_id=<?= (int)($delivery['request_id'] ?? 0) ?>');
    const d = await res.json();
    if (d.success && d.current_lat && d.current_lng) {
      driverLat = parseFloat(d.current_lat);
      driverLng = parseFloat(d.current_lng);
      if (!driverMarker) driverMarker = addLabeledMarker(driverLat, driverLng, 'Driver', 'blue');
      else moveMarkerSmoothly(driverMarker, driverLat, driverLng);
      if (dropLat && dropLng) {
        if (!routeControl) {
          routeControl = L.Routing.control({
            waypoints: [L.latLng(driverLat, driverLng), L.latLng(dropLat, dropLng)],
            createMarker: () => null,
            lineOptions: { styles: [{ color: 'green', opacity: 0.7, weight: 5 }] }
          }).on('routesfound', e => {
            const s = e.routes[0].summary;
            infoEl.textContent = `Route : ${(s.totalDistance/1000).toFixed(2)} km • ETA: ${Math.round(s.totalTime/60)} mins`;
          }).addTo(map);
        } else routeControl.spliceWaypoints(0, 1, L.latLng(driverLat, driverLng));
      }
    }
  } catch(e){ console.warn(e); }
}

// ===== Poll delivery & driver info =====
async function pollStatus() {
  try {
    const res = await fetch('../api/get_delivery_status.php?id=<?= (int)($delivery['request_id'] ?? 0) ?>');
    const j = await res.json();
    if (!j.success || !j.data) return;

    const s = j.data.delivery_status?.toLowerCase() ?? '';
    const order = ['pending','accepted','picked_up','in_transit','delivered'];
    const idx = order.indexOf(s);
    order.forEach((st,i)=>{
      const el=document.getElementById('status-'+st);
      if(el)(i<=idx)?el.classList.add('active'):el.classList.remove('active');
    });

    // ---- Live driver info (when accepted) ----
    if (s === 'accepted' && j.data.driver_info) {
      const info = j.data.driver_info;
      const container = document.querySelector('.driver-info-card');
      if (container && container.querySelector('h5').textContent.includes('Waiting')) {
        container.innerHTML = `
          <h5 class="text-success fw-bold">Assigned Driver</h5>
          <div class="driver-info">
            <img src="../uploads/drivers/${info.profile_photo ?? 'driver.png'}" class="rounded-circle" style="width:60px;height:60px;border:2px solid #198754;object-fit:cover;">
            <div>
              <strong>${info.first_name ?? ''} ${info.last_name ?? ''}</strong><br>
              <span>Phone: ${info.phone ?? 'N/A'}</span><br>
              <span>Rating: ${info.rating ? info.rating.toFixed(1)+' / 5 ('+info.rating_count+' ratings)' : 'No ratings yet'}</span>
            </div>
          </div>`;
      }
    }

    if (j.data.current_lat && j.data.current_long) {
      const lat=parseFloat(j.data.current_lat), lng=parseFloat(j.data.current_long);
      if (!driverMarker) driverMarker=addLabeledMarker(lat,lng,'Driver','blue');
      else moveMarkerSmoothly(driverMarker,lat,lng);
    }
  } catch(e){ console.warn('Poll failed', e); }
}

// ===== Real-time every 10 s =====
setInterval(()=>{ updateDriverLocation(); pollStatus(); },10000);

// ===== FEEDBACK & RATING =====
document.addEventListener('DOMContentLoaded',()=>{
  const stars=document.querySelectorAll('#starRating i');
  const ratingInput=document.getElementById('ratingInput');
  const form=document.getElementById('feedbackForm');
  const feedbackSection=document.getElementById('feedbackSection');
  const thankYouCard=document.getElementById('thankYouCard');

  if(stars.length){
    stars.forEach(star=>{
      star.addEventListener('mouseover',()=>{
        stars.forEach(s=>s.classList.remove('hover'));
        for(let i=0;i<parseInt(star.dataset.value);i++)stars[i].classList.add('hover');
      });
      star.addEventListener('mouseout',()=>stars.forEach(s=>s.classList.remove('hover')));
      star.addEventListener('click',()=>{
        stars.forEach(s=>s.classList.remove('selected'));
        const val=parseInt(star.dataset.value);
        ratingInput.value=val;
        for(let i=0;i<val;i++)stars[i].classList.add('selected');
      });
    });
  }

  if(form){
    form.addEventListener('submit',async e=>{
      e.preventDefault();
      const rating=ratingInput.value,comments=document.getElementById('comments').value.trim();
      if(!rating||rating==0)return alert('Please select a rating.');
      try{
        const res=await fetch('submit_feedback.php',{
          method:'POST',
          headers:{'Content-Type':'application/x-www-form-urlencoded'},
          body:new URLSearchParams({
            request_id:<?= (int)($delivery['request_id']??0) ?>,
            rating,comments
          })
        });
        const data=await res.json();
        if(data.success){
          feedbackSection.style.display='none';
          thankYouCard.style.display='block';
          window.scrollTo({top:thankYouCard.offsetTop,behavior:'smooth'});
        } else alert(data.message||'Failed to submit feedback.');
      }catch(err){console.error(err);alert('Error submitting feedback.');}
    });
  }
});
</script>

</body>
</html>
