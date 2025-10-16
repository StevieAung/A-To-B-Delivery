<?php /* Sidebar */ ?>
<nav class="sidebar text-center">
  <h5>👨🏽‍💻 Admin Panel</h5>
  <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF'])==='dashboard.php'?'active':'' ?>"><i class="fas fa-home"></i> Dashboard</a>
  <a href="manage_users.php" class="<?= basename($_SERVER['PHP_SELF'])==='manage_users.php'?'active':'' ?>"><i class="fas fa-users"></i> Manage Users</a>
  <a href="manage_deliveries.php" class="<?= basename($_SERVER['PHP_SELF'])==='manage_deliveries.php'?'active':'' ?>"><i class="fas fa-shipping-fast"></i> Manage Deliveries</a>
  <?php if (($ADMIN_ROLE ?? 'admin') === 'super_admin'): ?>
    <a href="manage_admins.php" class="<?= basename($_SERVER['PHP_SELF'])==='manage_admins.php'?'active':'' ?>"><i class="fas fa-user-shield"></i> Manage Admins</a>
  <?php endif; ?>
  <a href="reports.php" class="<?= basename($_SERVER['PHP_SELF'])==='reports.php'?'active':'' ?>"><i class="fas fa-chart-line"></i> Reports</a>
  <a href="settings.php" class="<?= basename($_SERVER['PHP_SELF'])==='settings.php'?'active':'' ?>"><i class="fas fa-cog"></i> Settings</a>
  <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</nav>
