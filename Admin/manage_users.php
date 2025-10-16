<?php
include __DIR__."/middleware/auth.php";
include '../Database/db.php';
include __DIR__."/middleware/csrf.php";
csrf_verify();

$keyword = trim($_GET['q'] ?? '');
if ($keyword !== '') {
  $kw = "%".$keyword."%";
  $stmt = $conn->prepare("SELECT user_id, first_name, last_name, email, phone, role, created_at
                          FROM users
                          WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ?
                          ORDER BY created_at DESC LIMIT 200");
  $stmt->bind_param("sss", $kw, $kw, $kw);
  $stmt->execute();
  $users = $stmt->get_result();
} else {
  $users = $conn->query("SELECT user_id, first_name, last_name, email, phone, role, created_at
                         FROM users ORDER BY created_at DESC LIMIT 200");
}
?>
<?php include __DIR__."/includes/head.php"; ?>
<?php include __DIR__."/includes/sidebar.php"; ?>
<main>
<?php include __DIR__."/includes/topbar.php"; ?>

<div class="card p-3">
  <form class="row g-2 align-items-center" method="get">
    <div class="col-md-4"><input class="form-control" name="q" value="<?= htmlspecialchars($keyword) ?>" placeholder="Search name or email"></div>
    <div class="col-auto"><button class="btn btn-primary"><i class="fa fa-search me-1"></i>Search</button></div>
    <div class="col-auto"><a href="manage_users.php" class="btn btn-outline-secondary">Reset</a></div>
  </form>
</div>

<div class="card p-3 mt-3">
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light"><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Joined</th></tr></thead>
      <tbody>
      <?php if($users && $users->num_rows): while($u=$users->fetch_assoc()): ?>
        <tr>
          <td>#<?= $u['user_id'] ?></td>
          <td><?= htmlspecialchars($u['first_name'].' '.$u['last_name']) ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><?= htmlspecialchars($u['phone']) ?></td>
          <td><span class="badge <?= $u['role']==='driver'?'bg-info':($u['role']==='sender'?'bg-primary':'bg-secondary') ?>"><?= htmlspecialchars($u['role']) ?></span></td>
          <td><?= date("M d, Y", strtotime($u['created_at'])) ?></td>
        </tr>
      <?php endwhile; else: ?>
        <tr><td colspan="6" class="text-center text-muted">No users found</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</main>
<?php include __DIR__."/includes/footer.php"; ?>
