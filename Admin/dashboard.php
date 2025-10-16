<?php
/**
 * A To B Delivery – Admin Dashboard (Fixed & Optimized)
 * ------------------------------------------------------
 * Features:
 *  - Pie chart with % labels (Chart.js + DataLabels)
 *  - Line chart for 7-day user registration
 *  - Quick stats & recent activity
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include __DIR__ . "/middleware/auth.php";
include '../Database/db.php';

// Helper
function quickCount($conn, $sql){
    $res = $conn->query($sql);
    return $res ? (int)$res->fetch_row()[0] : 0;
}

// Stats
$totalUsers      = quickCount($conn,"SELECT COUNT(*) FROM users");
$totalDrivers    = quickCount($conn,"SELECT COUNT(*) FROM users WHERE role='driver'");
$totalDeliveries = quickCount($conn,"SELECT COUNT(*) FROM delivery_requests");
$pending         = quickCount($conn,"SELECT COUNT(*) FROM delivery_requests WHERE delivery_status='pending'");
$delivered       = quickCount($conn,"SELECT COUNT(*) FROM delivery_requests WHERE delivery_status='delivered'");
$cancelled       = quickCount($conn,"SELECT COUNT(*) FROM delivery_requests WHERE delivery_status='cancelled'");
$successRate     = $totalDeliveries > 0 ? round(($delivered / $totalDeliveries) * 100, 2) : 0;

// Recent Deliveries
$recentDeliveries = $conn->query("
    SELECT d.request_id, u.first_name, u.last_name, 
           d.pickup_location, d.drop_location, d.delivery_status, d.created_at
    FROM delivery_requests d
    JOIN users u ON d.sender_id = u.user_id
    ORDER BY d.created_at DESC
    LIMIT 5
");

// Recent Users
$recentUsers = $conn->query("
    SELECT user_id, first_name, last_name, email, created_at
    FROM users
    ORDER BY created_at DESC
    LIMIT 5
");

// Registrations chart data (7 days)
$regLabels = [];
$regData   = [];
for($i=6; $i>=0; $i--){
    $date = date("Y-m-d", strtotime("-$i days"));
    $count = quickCount($conn, "SELECT COUNT(*) FROM users WHERE DATE(created_at)='$date'");
    $regLabels[] = date("M d", strtotime($date));
    $regData[]   = $count;
}
?>
<?php include __DIR__."/includes/head.php"; ?>
<?php include __DIR__."/includes/sidebar.php"; ?>

<main>
<?php include __DIR__."/includes/topbar.php"; ?>

<!-- Quick Stats -->
<div class="row g-4">
  <div class="col-md-3"><div class="card p-3 text-center shadow-sm"><h6>👥 Users</h6><h2><?= $totalUsers ?></h2></div></div>
  <div class="col-md-3"><div class="card p-3 text-center shadow-sm"><h6>🚘 Drivers</h6><h2><?= $totalDrivers ?></h2></div></div>
  <div class="col-md-3"><div class="card p-3 text-center shadow-sm"><h6>📦 Deliveries</h6><h2><?= $totalDeliveries ?></h2></div></div>
  <div class="col-md-3"><div class="card p-3 text-center shadow-sm"><h6>✅ Success Rate</h6><h2><?= $successRate ?>%</h2></div></div>
</div>

<!-- Charts -->
<div class="row mt-4 g-4">
  <div class="col-md-6">
    <div class="card p-3 text-center shadow-sm">
      <h5 class="text-primary mb-2">🥧 Deliveries by Status</h5>
      <div class="chart-container" style="height:300px">
        <canvas id="deliveryChart"></canvas>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card p-3 shadow-sm">
      <h5 class="text-primary mb-2">📈 New Users (7 Days)</h5>
      <div class="chart-container" style="height:300px">
        <canvas id="userChart"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Recent Activity (Vertical Layout) -->
<div class="mt-4">
  <!-- Recent Users -->
  <h5 class="text-primary mb-3">👥 Recent Users</h5>
  <div class="card p-3 shadow-sm mb-4">
    <ul class="list-group list-group-flush">
      <?php if($recentUsers && $recentUsers->num_rows): while($u=$recentUsers->fetch_assoc()): ?>
        <li class="list-group-item">
          <strong><?= htmlspecialchars($u['first_name'].' '.$u['last_name']) ?></strong>
          <small class="text-muted">(<?= htmlspecialchars($u['email']) ?>)</small><br>
          <span class="text-muted">Joined: <?= date("M d, Y", strtotime($u['created_at'])) ?></span>
        </li>
      <?php endwhile; else: ?>
        <li class="list-group-item text-center text-muted">No recent users</li>
      <?php endif; ?>
    </ul>
  </div>

  <!-- Recent Deliveries -->
  <h5 class="text-primary mb-3">📦 Recent Deliveries</h5>
  <div class="card p-3 shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Sender</th>
            <th>Pickup</th>
            <th>Drop</th>
            <th>Status</th>
            <th>Requested</th>
          </tr>
        </thead>
        <tbody>
        <?php if($recentDeliveries && $recentDeliveries->num_rows): while($r=$recentDeliveries->fetch_assoc()): ?>
          <tr>
            <td>#<?= (int)$r['request_id'] ?></td>
            <td><?= htmlspecialchars($r['first_name'].' '.$r['last_name']) ?></td>
            <td><?= htmlspecialchars($r['pickup_location']) ?></td>
            <td><?= htmlspecialchars($r['drop_location']) ?></td>
            <td>
              <span class="badge 
                <?= $r['delivery_status']=='pending'?'bg-warning':
                    ($r['delivery_status']=='delivered'?'bg-success':
                    ($r['delivery_status']=='cancelled'?'bg-secondary':'bg-info')) ?>">
                <?= ucfirst(str_replace('_',' ',$r['delivery_status'])) ?>
              </span>
            </td>
            <td><?= date("M d, Y H:i", strtotime($r['created_at'])) ?></td>
          </tr>
        <?php endwhile; else: ?>
          <tr><td colspan="6" class="text-center text-muted">No recent deliveries</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

</main>

<?php include __DIR__."/includes/footer.php"; ?>

<!-- =================== Charts =================== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>

<script>
/* PIE CHART – Deliveries by Status */
const deliveryCtx = document.getElementById('deliveryChart').getContext('2d');
new Chart(deliveryCtx, {
  type: 'pie',
  data: {
    labels: ['Pending', 'Delivered', 'Cancelled'],
    datasets: [{
      data: [<?= (int)$pending ?>, <?= (int)$delivered ?>, <?= (int)$cancelled ?>],
      backgroundColor: ['#ffc107', '#28a745', '#6c757d'],
      borderColor: '#fff',
      borderWidth: 2
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { position: 'bottom', labels: { font: { size: 14 } } },
      datalabels: {
        color: '#fff',
        font: { weight: 'bold', size: 14 },
        anchor: 'center',
        clip: false,
        formatter: (value, ctx) => {
          const total = ctx.chart.data.datasets[0].data.reduce((a,b)=>a+b,0);
          return total ? ((value / total) * 100).toFixed(1) + '%' : '0%';
        }
      }
    }
  },
  plugins: [ChartDataLabels]
});

/* LINE CHART – New Users (7 Days) */
const userCtx = document.getElementById('userChart').getContext('2d');
new Chart(userCtx, {
  type: 'line',
  data: {
    labels: <?= json_encode($regLabels) ?>,
    datasets: [{
      label: 'New Users',
      data: <?= json_encode($regData) ?>,
      borderColor: '#0d6efd',
      backgroundColor: 'rgba(13,110,253,0.2)',
      tension: 0.3,
      fill: true
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
    plugins: { legend: { display: false } }
  }
});
</script>
