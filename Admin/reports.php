<?php
/**
 * A To B Delivery – Admin Reports (Fixed)
 * -----------------------------------------
 * Features:
 *  - Date filter (From–To)
 *  - Summary counts (Total, Delivered, Pending, Cancelled)
 *  - Bar chart visualization
 * -----------------------------------------
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include __DIR__."/middleware/auth.php";
include '../Database/db.php';

// Default filter dates (first day of month → today)
$from = $_GET['from'] ?? date('Y-m-01');
$to   = $_GET['to']   ?? date('Y-m-d');

// ✅ Use correct column name (delivery_status)
$stmt = $conn->prepare("
    SELECT delivery_status, COUNT(*) AS cnt
    FROM delivery_requests
    WHERE DATE(created_at) BETWEEN ? AND ?
    GROUP BY delivery_status
");
$stmt->bind_param("ss", $from, $to);
$stmt->execute();
$res = $stmt->get_result();

// ✅ Initialize counts
$map = [
    'pending'   => 0,
    'delivered' => 0,
    'cancelled' => 0
];

// ✅ Fill counts from query results
while ($row = $res->fetch_assoc()) {
    $status = $row['delivery_status'];
    if (isset($map[$status])) {
        $map[$status] = (int)$row['cnt'];
    }
}

$total = array_sum($map);
?>
<?php include __DIR__."/includes/head.php"; ?>
<?php include __DIR__."/includes/sidebar.php"; ?>

<main>
<?php include __DIR__."/includes/topbar.php"; ?>

<!-- Filter Form -->
<div class="card p-3 shadow-sm">
  <form class="row g-2 align-items-end" method="GET">
    <div class="col-md-3">
      <label class="form-label">From</label>
      <input type="date" name="from" class="form-control" value="<?= htmlspecialchars($from) ?>">
    </div>
    <div class="col-md-3">
      <label class="form-label">To</label>
      <input type="date" name="to" class="form-control" value="<?= htmlspecialchars($to) ?>">
    </div>
    <div class="col-auto">
      <button class="btn btn-primary"><i class="bi bi-filter me-1"></i> Apply</button>
    </div>
  </form>
</div>

<!-- Summary Cards -->
<div class="row g-4 mt-3">
  <div class="col-md-3">
    <div class="card p-3 text-center shadow-sm">
      <h6>📦 Total</h6>
      <h3><?= $total ?></h3>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card p-3 text-center shadow-sm">
      <h6>✅ Delivered</h6>
      <h3><?= $map['delivered'] ?></h3>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card p-3 text-center shadow-sm">
      <h6>⏳ Pending</h6>
      <h3><?= $map['pending'] ?></h3>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card p-3 text-center shadow-sm">
      <h6>🗑 Cancelled</h6>
      <h3><?= $map['cancelled'] ?></h3>
    </div>
  </div>
</div>

<!-- Chart -->
<div class="card p-3 mt-3 shadow-sm">
  <h5 class="text-primary mb-2">📊 Delivery Breakdown</h5>
  <div class="chart-container" style="height:350px;">
    <canvas id="rep"></canvas>
  </div>
</div>

</main>

<?php include __DIR__."/includes/footer.php"; ?>

<!-- =================== Charts =================== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('rep').getContext('2d'), {
  type: 'bar',
  data: {
    labels: ['Delivered', 'Pending', 'Cancelled'],
    datasets: [{
      label: 'Delivery Requests',
      data: [<?= (int)$map['delivered'] ?>, <?= (int)$map['pending'] ?>, <?= (int)$map['cancelled'] ?>],
      backgroundColor: ['#28a745', '#ffc107', '#6c757d']
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
      y: { beginAtZero: true, ticks: { precision: 0 } }
    },
    plugins: {
      legend: { display: false },
      tooltip: { backgroundColor: 'rgba(0,0,0,0.7)' }
    }
  }
});
</script>
