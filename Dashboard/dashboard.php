<?php
// filepath: /Applications/XAMPP/xamppfiles/htdocs/Delivery/Dashboard/dashboard.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Protect page: only logged in admins allowed
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}
include '../Database/db.php';
$adminName = $_SESSION['admin_name'] ?? "Admin";
$adminRole = $_SESSION['admin_role'] ?? "admin";

// Quick stats
$totalUsers = $conn->query("SELECT COUNT(*) AS cnt FROM users")->fetch_assoc()['cnt'] ?? 0;
$totalDeliveries = $conn->query("SELECT COUNT(*) AS cnt FROM delivery_requests")->fetch_assoc()['cnt'] ?? 0;
$pendingDeliveries = $conn->query("SELECT COUNT(*) AS cnt FROM delivery_requests WHERE status='pending'")->fetch_assoc()['cnt'] ?? 0;
$totalAdmins = $conn->query("SELECT COUNT(*) AS cnt FROM admins")->fetch_assoc()['cnt'] ?? 0;

// Recent deliveries
$recentDeliveries = $conn->query("
    SELECT d.request_id, u.first_name, u.last_name, d.pickup_location, d.delivery_location, d.status, d.created_at
    FROM delivery_requests d
    JOIN users u ON d.user_id = u.user_id
    ORDER BY d.created_at DESC
    LIMIT 5
");

// Recent users
$recentUsers = $conn->query("
    SELECT user_id, first_name, last_name, email, created_at
    FROM users
    ORDER BY created_at DESC
    LIMIT 5
");

// Chart: Deliveries by status
$statusCounts = [
    'pending'   => $conn->query("SELECT COUNT(*) FROM delivery_requests WHERE status='pending'")->fetch_row()[0] ?? 0,
    'completed' => $conn->query("SELECT COUNT(*) FROM delivery_requests WHERE status='delivered'")->fetch_row()[0] ?? 0,
    'cancelled' => $conn->query("SELECT COUNT(*) FROM delivery_requests WHERE status='cancelled'")->fetch_row()[0] ?? 0,
];

// Chart: Users registered in last 7 days
$registrationData = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date("Y-m-d", strtotime("-$i days"));
    $count = $conn->query("SELECT COUNT(*) FROM users WHERE DATE(created_at)='$date'")->fetch_row()[0] ?? 0;
    $registrationData[] = ['date' => $date, 'count' => $count];
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard</title>
  <?php include '../includes/head_tags.php'; ?>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    /* General Styles */
    body {
      background-color: #f8f9fa;
      font-family: 'Arial', sans-serif;
    }

    /* Sidebar Styles */
    .sidebar {
      height: 100vh;
      background: var(--bs-primary);
      color: white;
      padding-top: 1rem;
      position: fixed; /* Fixed sidebar */
      width: 250px; /* Adjust width as needed */
      top: 0;
      left: 0;
      z-index: 100; /* Ensure it's above other content */
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }

    .sidebar a {
      color: #ffffffcc;
      text-decoration: none;
      display: block;
      padding: 10px 20px;
      border-radius: 8px;
      margin: 5px 10px;
      transition: all 0.2s ease-in-out;
      display: flex;
      align-items: center;
    }

    .sidebar a i {
      margin-right: 10px; /* Space for icons */
      width: 20px; /* Ensure consistent icon size */
      text-align: center;
    }

    .sidebar a.active,
    .sidebar a:hover {
      background: #ffffff33;
      color: #fff;
    }

    /* Main Content Styles */
    main {
      padding-left: 250px; /* Account for fixed sidebar */
      padding-top: 20px;
    }

    /* Card Styles */
    .card {
      border-radius: 1rem;
      transition: transform 0.2s;
      border: none;
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
    }

    .card:hover {
      transform: translateY(-3px);
    }

    /* Navbar Styles */
    .navbar {
      border-radius: 1rem;
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
    }

    /* Badge Styles */
    .badge {
      font-size: 0.85rem;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
      .sidebar {
        position: static;
        width: 100%;
        height: auto;
        padding: 0;
      }

      main {
        padding-left: 0;
        padding-top: 10px;
      }

      .sidebar a {
        text-align: center;
        margin: 5px;
      }

      .sidebar h4 {
        text-align: center;
      }
    }
  </style>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <nav class="col-md-3 col-lg-2 d-md-block sidebar">
      <h4 class="text-center text-white">👨🏽‍💻 Admin Panel</h4>
      <hr class="text-light">
      <a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
      <a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a>
      <a href="manage_deliveries.php"><i class="fas fa-shipping-fast"></i> Manage Deliveries</a>
      <?php if ($adminRole === 'super_admin'): ?>
        <a href="manage_admins.php"><i class="fas fa-user-shield"></i> Manage Admins</a>
      <?php endif; ?>
      <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>

    <!-- Main content -->
    <main class="col-md-9 ms-sm-auto px-md-4">
      <!-- Top Navbar -->
      <nav class="navbar navbar-light bg-white shadow-sm mt-3 mb-4 px-3">
        <span class="navbar-brand mb-0 h4 text-primary">
          Welcome, <?php echo htmlspecialchars($adminName); ?> (<?php echo $adminRole; ?>)
        </span>
        <a href="../logout.php" class="btn btn-outline-primary">Logout</a>
      </nav>

      <!-- Dashboard Stats -->
      <div class="row g-4">
        <div class="col-md-3">
          <div class="card shadow-sm text-center p-3 border-primary">
            <h5 class="text-primary">👥 Users</h5>
            <h2><?php echo $totalUsers; ?></h2>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card shadow-sm text-center p-3 border-primary">
            <h5 class="text-primary">📦 Deliveries</h5>
            <h2><?php echo $totalDeliveries; ?></h2>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card shadow-sm text-center p-3 border-primary">
            <h5 class="text-primary">⏳ Pending</h5>
            <h2><?php echo $pendingDeliveries; ?></h2>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card shadow-sm text-center p-3 border-primary">
            <h5 class="text-primary">🛡 Admins</h5>
            <h2><?php echo $totalAdmins; ?></h2>
          </div>
        </div>
      </div>

      <!-- Graphs Section -->
      <div class="row mt-5 g-4">
        <div class="col-md-6">
          <div class="card shadow-sm p-3">
            <h5 class="text-primary">📊 Deliveries by Status</h5>
            <canvas id="deliveryChart" height="200"></canvas>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card shadow-sm p-3">
            <h5 class="text-primary">📈 User Registrations (7 days)</h5>
            <canvas id="userChart" height="200"></canvas>
          </div>
        </div>
      </div>

      <!-- Recent Deliveries -->
      <div class="mt-5">
        <h4 class="text-primary">📦 Recent Deliveries</h4>
        <div class="card shadow-sm p-3">
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th>ID</th>
                  <th>Sender</th>
                  <th>Pickup</th>
                  <th>Drop-off</th>
                  <th>Status</th>
                  <th>Requested At</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($recentDeliveries->num_rows > 0): ?>
                  <?php while ($row = $recentDeliveries->fetch_assoc()): ?>
                    <tr>
                      <td>#<?php echo $row['request_id']; ?></td>
                      <td><?php echo htmlspecialchars($row['first_name'] . " " . $row['last_name']); ?></td>
                      <td><?php echo htmlspecialchars($row['pickup_location']); ?></td>
                      <td><?php echo htmlspecialchars($row['delivery_location']); ?></td>
                      <td>
                        <span class="badge 
                          <?php echo $row['status']=='pending' ? 'bg-warning' : ($row['status']=='delivered' ? 'bg-success' : 'bg-secondary'); ?>">
                          <?php echo ucfirst($row['status']); ?>
                        </span>
                      </td>
                      <td><?php echo date("M d, Y H:i", strtotime($row['created_at'])); ?></td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr><td colspan="6" class="text-muted text-center">No recent deliveries</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Recent Users -->
      <div class="mt-5">
        <h4 class="text-primary">👥 Recent Users</h4>
        <div class="card shadow-sm p-3">
          <ul class="list-group list-group-flush">
            <?php if ($recentUsers->num_rows > 0): ?>
              <?php while ($u = $recentUsers->fetch_assoc()): ?>
                <li class="list-group-item">
                  <strong><?php echo htmlspecialchars($u['first_name'] . " " . $u['last_name']); ?></strong>
                  <small class="text-muted"> (<?php echo htmlspecialchars($u['email']); ?>)</small>
                  <br>
                  <span class="text-muted">Joined: <?php echo date("M d, Y", strtotime($u['created_at'])); ?></span>
                </li>
              <?php endwhile; ?>
            <?php else: ?>
              <li class="list-group-item text-muted text-center">No recent users</li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </main>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Deliveries chart
new Chart(document.getElementById('deliveryChart'), {
    type: 'doughnut',
    data: {
        labels: ['Pending', 'Completed', 'Cancelled'],
        datasets: [{
            data: [<?php echo $statusCounts['pending']; ?>, <?php echo $statusCounts['completed']; ?>, <?php echo $statusCounts['cancelled']; ?>],
            backgroundColor: ['#ffc107', '#28a745', '#6c757d']
        }]
    }
});

// Users chart (last 7 days)
new Chart(document.getElementById('userChart'), {
    type: 'line',
    data: {
        labels: [<?php echo implode(',', array_map(fn($d) => "'" . date("M d", strtotime($d['date'])) . "'", $registrationData)); ?>],
        datasets: [{
            label: 'New Users',
            data: [<?php echo implode(',', array_column($registrationData, 'count')); ?>],
            borderColor: '#007bff',
            backgroundColor: 'rgba(0,123,255,0.3)',
            tension: 0.3,
            fill: true
        }]
    },
    options: {
        scales: {
            y: { beginAtZero: true, precision: 0 }
        }
    }
});
</script>
</body>
</html>
