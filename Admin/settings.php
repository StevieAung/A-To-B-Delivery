<?php
include __DIR__."/middleware/auth.php";
include '../Database/db.php';
include __DIR__."/middleware/csrf.php";
csrf_verify();

$msg='';

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['current'],$_POST['new'],$_POST['confirm'])) {
  csrf_verify();
  $cur = $_POST['current'];
  $new = $_POST['new'];
  $con = $_POST['confirm'];
  if ($new !== $con) { $msg="New passwords do not match."; }
  else {
    $stmt=$conn->prepare("SELECT password_hash FROM admins WHERE admin_id=?");
    $stmt->bind_param("i", $_SESSION['admin_id']);
    $stmt->execute(); $r=$stmt->get_result()->fetch_assoc();
    if ($r && password_verify($cur,$r['password_hash'])) {
      $hash=password_hash($new,PASSWORD_BCRYPT);
      $up=$conn->prepare("UPDATE admins SET password_hash=? WHERE admin_id=?");
      $up->bind_param("si",$hash,$_SESSION['admin_id']);
      $up->execute();
      $msg="Password updated.";
    } else { $msg="Current password incorrect."; }
  }
}
?>
<?php include __DIR__."/includes/head.php"; ?>
<?php include __DIR__."/includes/sidebar.php"; ?>
<main>
<?php include __DIR__."/includes/topbar.php"; ?>

<div class="row g-4">
  <div class="col-lg-6">
    <div class="card p-3">
      <h5 class="text-primary">🔐 Change Password</h5>
      <?php if($msg): ?><div class="alert alert-info mt-2"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
      <form method="post" class="mt-2">
        <?php csrf_input(); ?>
        <div class="mb-2"><label class="form-label">Current Password</label><input type="password" name="current" class="form-control" required></div>
        <div class="mb-2"><label class="form-label">New Password</label><input type="password" name="new" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Confirm New Password</label><input type="password" name="confirm" class="form-control" required></div>
        <button class="btn btn-primary">Update</button>
      </form>
    </div>
  </div>
</div>
</main>
<?php include __DIR__."/includes/footer.php"; ?>
