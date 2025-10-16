<?php
/**
 * A To B Delivery – Manage Admins (Adjusted for new schema)
 * ---------------------------------------------------------
 * Table: admins
 * Columns: first_name, last_name, email, password_hash, role
 * ---------------------------------------------------------
 */

include __DIR__."/middleware/auth.php";
include '../Database/db.php';
include __DIR__."/middleware/csrf.php";

// ✅ Super Admin–only access
if (($ADMIN_ROLE ?? 'admin') !== 'super_admin') {
    http_response_code(403);
    die("<div style='text-align:center;margin-top:10%;font-family:sans-serif;'>
            <h2>🚫 Access Denied</h2>
            <p>Only Super Admins can access this page.</p>
            <a href='dashboard.php' style='color:#0d6efd;'>Back to Dashboard</a>
        </div>");
}

$alert = "";

// ➕ CREATE ADMIN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create') {
    csrf_verify();

    $first = trim($_POST['first_name']);
    $last  = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];
    $conf  = $_POST['confirm'];
    $role  = $_POST['role'] ?? 'admin';

    if ($pass !== $conf) {
        $alert = "<div class='alert alert-danger mt-3 p-2 text-center'>❌ Passwords do not match.</div>";
    } else {
        $check = $conn->prepare("SELECT admin_id FROM admins WHERE email=?");
        $check->bind_param("s", $email);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows > 0) {
            $alert = "<div class='alert alert-warning mt-3 p-2 text-center'>⚠️ Email already registered.</div>";
        } else {
            $hash = password_hash($pass, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO admins (first_name, last_name, email, password_hash, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $first, $last, $email, $hash, $role);

            if ($stmt->execute()) {
                $alert = "<div class='alert alert-success mt-3 p-2 text-center'>✅ Admin created successfully!</div>";
            } else {
                $alert = "<div class='alert alert-danger mt-3 p-2 text-center'>Database error. Try again later.</div>";
            }
        }
    }
}

// 🗑 DELETE ADMIN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    csrf_verify();
    $deleteId = (int)$_POST['delete_id'];

    if ($deleteId === $_SESSION['admin_id']) {
        $alert = "<div class='alert alert-warning mt-3 p-2 text-center'>⚠️ You cannot delete your own account.</div>";
    } else {
        $stmt = $conn->prepare("DELETE FROM admins WHERE admin_id=?");
        $stmt->bind_param("i", $deleteId);
        if ($stmt->execute()) {
            $alert = "<div class='alert alert-success mt-3 p-2 text-center'>🗑 Admin deleted successfully.</div>";
        } else {
            $alert = "<div class='alert alert-danger mt-3 p-2 text-center'>Failed to delete admin.</div>";
        }
    }
}
?>
<?php include __DIR__."/includes/head.php"; ?>
<?php include __DIR__."/includes/sidebar.php"; ?>
<main>
<?php include __DIR__."/includes/topbar.php"; ?>

<h4 class="text-primary mb-4">🛡 Manage Admin Accounts</h4>
<?php if (!empty($alert)) echo $alert; ?>

<div class="row g-4">
  <!-- CREATE ADMIN FORM -->
  <div class="col-lg-6">
    <div class="card p-4">
      <h5 class="text-primary mb-3">➕ Create New Admin</h5>
      <form method="POST" action="manage_admins.php" autocomplete="off">
        <?php csrf_input(); ?>
        <input type="hidden" name="action" value="create">

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">First Name:</label>
            <input type="text" name="first_name" class="form-control" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold">Last Name:</label>
            <input type="text" name="last_name" class="form-control" required>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">Email Address:</label>
          <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">Password:</label>
          <div class="input-group">
            <input type="password" name="password" id="password" class="form-control" required minlength="8">
            <span class="input-group-text" onclick="togglePassword('password', this)" style="cursor:pointer;">
              <i class="bi bi-eye"></i>
            </span>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">Confirm Password:</label>
          <div class="input-group">
            <input type="password" name="confirm" id="confirm" class="form-control" required minlength="8">
            <span class="input-group-text" onclick="togglePassword('confirm', this)" style="cursor:pointer;">
              <i class="bi bi-eye"></i>
            </span>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">Assign Role:</label>
          <select name="role" class="form-select">
            <option value="admin">Admin</option>
            <option value="super_admin">Super Admin</option>
          </select>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2">
          <i class="fa fa-user-plus me-1"></i> Create Admin
        </button>
      </form>
    </div>
  </div>

  <!-- ADMIN LIST -->
  <div class="col-lg-6">
    <div class="card p-4">
      <h5 class="text-primary mb-3">📋 Current Admins</h5>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Created</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $admins = $conn->query("SELECT admin_id, first_name, last_name, email, role, created_at FROM admins ORDER BY created_at DESC");
            if ($admins && $admins->num_rows > 0):
              while($a = $admins->fetch_assoc()):
            ?>
            <tr>
              <td>#<?= $a['admin_id'] ?></td>
              <td><?= htmlspecialchars($a['first_name'].' '.$a['last_name']) ?></td>
              <td><?= htmlspecialchars($a['email']) ?></td>
              <td><span class="badge <?= $a['role']==='super_admin'?'bg-danger':'bg-secondary' ?>"><?= $a['role'] ?></span></td>
              <td><?= date("M d, Y", strtotime($a['created_at'])) ?></td>
              <td>
                <?php if ($a['admin_id'] !== $_SESSION['admin_id']): ?>
                  <form method="POST" action="manage_admins.php" onsubmit="return confirm('Are you sure you want to delete this admin?');" class="d-inline">
                    <?php csrf_input(); ?>
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="delete_id" value="<?= $a['admin_id'] ?>">
                    <button class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
                  </form>
                <?php else: ?>
                  <span class="text-muted small">You</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endwhile; else: ?>
              <tr><td colspan="6" class="text-center text-muted">No admins found</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</main>

<?php include __DIR__."/includes/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.js"></script>
<script>
function togglePassword(fieldId, spanElement) {
  const field = document.getElementById(fieldId);
  const icon = spanElement.querySelector('i');
  if (field.type === "password") {
    field.type = "text";
    icon.classList.replace("bi-eye", "bi-eye-slash");
  } else {
    field.type = "password";
    icon.classList.replace("bi-eye-slash", "bi-eye");
  }
}
</script>
