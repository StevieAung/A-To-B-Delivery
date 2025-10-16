<?php
/**
 * my_deliveries.php
 * --------------------------------------------------------
 * A To B Delivery – Driver Deliveries Page (Tracking + Payment Integration)
 * --------------------------------------------------------
 */

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../Database/db.php';

// Restrict to driver role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'driver') {
    header("Location: ../login.php");
    exit();
}

$driver_id = $_SESSION['user_id'];
$first_name = $_SESSION['user_firstname'] ?? 'Driver';
$alert = "";
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

// =======================================================
// STATUS UPDATE HANDLER (Extended for Payment Integration)
// =======================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
        $alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">Security validation failed.</div>';
    } else {
        $request_id = (int)$_POST['request_id'];
        $new_status = $_POST['new_status'];
        // Enforce transitions: accepted -> picked_up -> in_transit -> delivered
        $allowed = [
            'accepted'   => ['picked_up'],
            'picked_up'  => ['in_transit'],
            'in_transit' => ['delivered']
        ];

        $currStmt = $conn->prepare("SELECT delivery_status FROM delivery_requests WHERE request_id = ? AND driver_id = ?");
        $currStmt->bind_param("ii", $request_id, $driver_id);
        $currStmt->execute();
        $curr = $currStmt->get_result()->fetch_assoc();
        $currStmt->close();

        if (!$curr || !isset($allowed[$curr['delivery_status']]) || !in_array($new_status, $allowed[$curr['delivery_status']], true)) {
            $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">Invalid status transition.</div>';
        } else {
            // Update main delivery status
            $stmt = $conn->prepare("UPDATE delivery_requests SET delivery_status = ? WHERE request_id = ? AND driver_id = ?");
            $stmt->bind_param("sii", $new_status, $request_id, $driver_id);
            $ok = $stmt->execute();
            $stmt->close();

            if ($ok) {
                // ====== PAYMENT INTEGRATION ======
                if ($new_status === 'delivered') {
                    // Update tracking table
                    $trk = $conn->prepare("UPDATE delivery_tracking SET current_status = 'delivered', updated_at = NOW() WHERE request_id = ? AND driver_id = ?");
                    $trk->bind_param("ii", $request_id, $driver_id);
                    $trk->execute();
                    $trk->close();

                    // Get delivery data
                    $q = $conn->prepare("SELECT sender_id, estimated_price, payment_method FROM delivery_requests WHERE request_id = ?");
                    $q->bind_param("i", $request_id);
                    $q->execute();
                    $delivery = $q->get_result()->fetch_assoc();
                    $q->close();

                    if ($delivery) {
                        $sender_id = $delivery['sender_id'];
                        $amount = $delivery['estimated_price'];
                        $method = $delivery['payment_method'];

                        // Check if payment already exists
                        $check = $conn->prepare("SELECT payment_id FROM payments WHERE request_id = ?");
                        $check->bind_param("i", $request_id);
                        $check->execute();
                        $exists = $check->get_result()->num_rows > 0;
                        $check->close();

                        if (!$exists) {
                            $p = $conn->prepare("
                                INSERT INTO payments (request_id, sender_id, driver_id, amount, method, payment_status)
                                VALUES (?, ?, ?, ?, ?, 'Completed')
                            ");
                            $p->bind_param("iiids", $request_id, $sender_id, $driver_id, $amount, $method);
                            $p->execute();
                            $p->close();
                        }
                    }
                }
                // ====== END PAYMENT LOGIC ======
                $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">Delivery status updated successfully!</div>';
            } else {
                $alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">Failed to update status.</div>';
            }
        }
    }
}

// =======================================================
// FETCH DRIVER DELIVERIES
// =======================================================
$result = $conn->prepare("
    SELECT dr.*, u.first_name AS sender_fname, u.last_name AS sender_lname
    FROM delivery_requests dr
    JOIN users u ON dr.sender_id = u.user_id
    WHERE dr.driver_id = ? 
    ORDER BY dr.created_at DESC
");
$result->bind_param("i", $driver_id);
$result->execute();
$deliveries = $result->get_result();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Deliveries | A To B Delivery</title>
<?php include '../includes/head_tags.php'; ?>
<style>
.badge { font-size:0.85rem; }
.status-btn { min-width:120px; }
</style>
</head>
<body class="bg-success-subtle d-flex flex-column min-vh-100">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand text-success fw-bold" href="driver_dashboard.php">A To B Delivery</a>
    <div class="collapse navbar-collapse justify-content-end">
      <ul class="navbar-nav">
        <li class="nav-item me-3"><span class="nav-link">Hello, <strong><?= htmlspecialchars($first_name) ?></strong></span></li>
        <li class="nav-item"><a class="btn btn-outline-danger" href="../logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- MAIN CONTENT -->
<div class="container mt-5 flex-grow-1">
  <div class="card shadow-sm">
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
      <span><i class="bi bi-truck me-2"></i> My Deliveries</span>
      <a href="driver_dashboard.php" class="btn btn-light btn-sm">Back to Dashboard</a>
    </div>
    <div class="card-body">
      <?= $alert ?>

      <?php if ($deliveries->num_rows > 0): ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Sender</th>
              <th>Pickup</th>
              <th>Drop</th>
              <th>Item</th>
              <th>Weight</th>
              <th>Status</th>
              <th>Action</th>
              <th>Updated</th>
            </tr>
          </thead>
          <tbody>
            <?php $i=1; while ($row=$deliveries->fetch_assoc()): ?>
            <?php
              $status = $row['delivery_status']; 
              $badge = match($status) {
                'accepted'=>'info',
                'picked_up'=>'warning',
                'in_transit'=>'primary',
                'delivered'=>'success',
                default=>'secondary'
              };
            ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><?= htmlspecialchars($row['sender_fname'] . ' ' . $row['sender_lname']) ?></td>
              <td><?= htmlspecialchars($row['pickup_location']) ?></td>
              <td><?= htmlspecialchars($row['drop_location']) ?></td>
              <td><?= htmlspecialchars($row['item_description']) ?></td>
              <td><?= htmlspecialchars($row['weight']) ?> kg</td>
              <td><span class="badge bg-<?= $badge ?>"><?= ucfirst(str_replace('_', ' ', $status)) ?></span></td>
              <td>
                <?php if ($status === 'accepted'): ?>
                  <button class="btn btn-sm btn-warning status-btn" data-id="<?= $row['request_id'] ?>" data-status="picked_up" data-bs-toggle="modal" data-bs-target="#confirmModal">Pick Up</button>
                <?php elseif ($status === 'picked_up'): ?>
                  <button class="btn btn-sm btn-primary status-btn" data-id="<?= $row['request_id'] ?>" data-status="in_transit" data-bs-toggle="modal" data-bs-target="#confirmModal">Out for Delivery</button>
                <?php elseif ($status === 'in_transit'): ?>
                  <button class="btn btn-sm btn-success status-btn" data-id="<?= $row['request_id'] ?>" data-status="delivered" data-bs-toggle="modal" data-bs-target="#confirmModal">Delivered</button>
                <?php else: ?>
                  <em class="text-muted">—</em>
                <?php endif; ?>
              </td>
              <td><?= date("M d, Y H:i", strtotime($row['updated_at'] ?? $row['created_at'])) ?></td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
      <?php else: ?>
        <div class="alert alert-info text-center">No deliveries assigned yet.</div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- CONFIRM MODAL -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="confirmModalLabel">Confirm Status Update</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center py-4">
        <p id="modalMessage" class="fw-semibold mb-3"></p>
        <form method="POST" id="statusForm">
          <input type="hidden" name="request_id" id="deliveryIdField">
          <input type="hidden" name="new_status" id="newStatusField">
          <input type="hidden" name="update_status" value="1">
          <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success px-4">Yes, Confirm</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- FOOTER -->
<footer class="bg-white shadow-sm text-center py-3 mt-auto">
  <span class="text-muted">&copy; <?= date('Y'); ?> A To B Delivery</span>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
  const modal = document.getElementById('confirmModal');
  if (modal) {
    const modalMessage = document.getElementById('modalMessage');
    const deliveryIdField = document.getElementById('deliveryIdField');
    const newStatusField = document.getElementById('newStatusField');

    modal.addEventListener('show.bs.modal', e => {
      const button = e.relatedTarget;
      const deliveryId = button.getAttribute('data-id');
      const newStatus = button.getAttribute('data-status');
      
      deliveryIdField.value = deliveryId;
      newStatusField.value = newStatus;
      
      const friendlyStatus = newStatus.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
      modalMessage.textContent = `Are you sure you want to mark this delivery as "${friendlyStatus}"?`;
    });
  }
});
</script>

</body>
</html>
<?php $conn->close(); ?>
