<?php /* Topbar */ ?>
<nav class="navbar navbar-light bg-white shadow-sm mb-3 px-3">
  <span class="navbar-brand mb-0 h5 text-primary">
    Welcome, <?= htmlspecialchars($ADMIN_NAME ?? 'Admin'); ?> (<?= htmlspecialchars($ADMIN_ROLE ?? 'admin'); ?>)
  </span>
  <a href="logout.php" class="btn btn-outline-primary ms-auto"><i class="fa fa-right-from-bracket me-1"></i> Logout</a>
</nav>
<div class="alert alert-primary shadow-sm" role="alert">
  ✨ <strong>Welcome back, <?= htmlspecialchars($ADMIN_NAME ?? 'Admin'); ?>!</strong> Here’s a quick overview of the platform activity.
</div>
