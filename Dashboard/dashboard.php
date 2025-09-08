<?php
session_start();
// Protect page: only logged in admins allowed
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}
include '../Database/db.php';
$adminName = $_SESSION['admin_name'] ?? "Admin";
$adminRole = $_SESSION['admin_role'] ?? "admin";

// Example: Fetch some quick stats
$totalUsers = $conn->query("SELECT COUNT(*) AS cnt FROM users")->fetch_assoc()['cnt'] ?? 0;
$totalDeliveries = $conn->query("SELECT COUNT(*) AS cnt FROM delivery_requests")->fetch_assoc()['cnt'] ?? 0;
$pendingDeliveries = $conn->query("SELECT COUNT(*) AS cnt FROM delivery_requests WHERE status='pending'")->fetch_assoc()['cnt'] ?? 0;
$totalAdmins = $conn->query("SELECT COUNT(*) AS cnt FROM admins")->fetch_assoc()['cnt'] ?? 0;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard</title>
  <?php include '../includes/head_tags.php'; ?>
  <style>
    body { background-color: #f8f9fa; }
    .sidebar {
        height: 100vh;
        background: var(--bs-primary);
        color: white;
        padding-top: 1rem;
    }
    .sidebar a {
        color: #ffffffcc;
        text-decoration: none;
        display: block;
        padding: 10px 20px;
        border-radius: 8px;
        margin: 5px 10px;
        transition: all 0.2s ease-in-out;
    }
    .sidebar a.active, .sidebar a:hover {
        background: #ffffff33;
        color: #fff;
    }
    .card {
        border-radius: 1rem;
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-3px);
    }
    .navbar {
        border-radius: 1rem;
    }
  </style>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <nav class="col-md-2 d-none d-md-block sidebar">
      <h4 class="text-center text-white">🚚 Admin Panel</h4>
      <hr class="text-light">
      <a href="dashboard.php" class="active">🏠 Dashboard</a>
      <a href="manage_users.php">👥 Manage Users</a>
      <a href="manage_deliveries.php">📦 Manage Deliveries</a>
      <?php if ($adminRole === 'super_admin'): ?>
        <a href="manage_admins.php">🛡 Manage Admins</a>
      <?php endif; ?>
      <a href="logout.php">🚪 Logout</a>
    </nav>

    <!-- Main content -->
    <main class="col-md-10 ms-sm-auto px-md-4">
      <!-- Top Navbar -->
      <nav class="navbar navbar-light bg-white shadow-sm mt-3 mb-4 px-3">
        <span class="navbar-brand mb-0 h4 text-primary">
          Welcome, <?php echo htmlspecialchars($adminName); ?> (<?php echo $adminRole; ?>)
        </span>
        <a href="logout.php" class="btn btn-outline-primary">Logout</a>
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

      <!-- Placeholder for future sections -->
      <div class="mt-5">
        <h4 class="text-primary">Recent Activity</h4>
        <div class="card shadow-sm p-3">
          <p class="text-muted">Recent deliveries, user signups, etc. will appear here...</p>
        </div>
      </div>
    </main>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
