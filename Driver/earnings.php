<?php
/**
 * earnings.php
 * --------------------------------------------------------
 * A To B Delivery – Driver Earnings Page
 *
 * Purpose:
 *  • Display driver’s total completed deliveries and earnings
 *  • Show payment details (amount, method, status)
 *  • Consistent with driver module design
 *
 * Author: Sai Htet Aung Hlaing
 * Updated: 2025-10-09
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

$user_id = $_SESSION['user_id'];
$first_name = $_SESSION['user_firstname'] ?? 'Driver';
if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(32)); }

// Optional filters
$from = isset($_GET['from']) ? $_GET['from'] : '';
$to = isset($_GET['to']) ? $_GET['to'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Basic validation
if ($from && !preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $from)) { $from = ''; }
if ($to && !preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $to)) { $to = ''; }
if ($status && !in_array($status, ['Pending','Completed','Failed'])) { $status = ''; }

// =============================
// Fetch completed deliveries
// =============================

// Build dynamic where
$where = "p.driver_id = ? AND d.delivery_status = 'delivered'";
$types = 'i';
$params = [$user_id];
if ($from) { $where .= " AND DATE(p.created_at) >= ?"; $types .= 's'; $params[] = $from; }
if ($to) { $where .= " AND DATE(p.created_at) <= ?"; $types .= 's'; $params[] = $to; }
if ($status) { $where .= " AND p.payment_status = ?"; $types .= 's'; $params[] = $status; }

$query = "
    SELECT 
        p.payment_id,
        p.amount,
        p.method AS payment_method,
        p.payment_status,
        p.created_at AS paid_at,
        d.pickup_location,
        d.drop_location,
        d.item_description,
        d.delivery_status
    FROM payments p
    JOIN delivery_requests d ON p.request_id = d.request_id
    WHERE $where
    ORDER BY p.created_at DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// =============================
// Calculate total earnings
// =============================
// Totals
$tWhere = "p.driver_id = ? AND d.delivery_status = 'delivered' AND p.payment_status = 'Completed'";
$tTypes = 'i';
$tParams = [$user_id];
if ($from) { $tWhere .= " AND DATE(p.created_at) >= ?"; $tTypes .= 's'; $tParams[] = $from; }
if ($to) { $tWhere .= " AND DATE(p.created_at) <= ?"; $tTypes .= 's'; $tParams[] = $to; }
$totalQuery = "
    SELECT SUM(p.amount) AS total_earnings, COUNT(p.payment_id) AS total_deliveries
    FROM payments p
    JOIN delivery_requests d ON p.request_id = d.request_id
    WHERE $tWhere
";
$totalStmt = $conn->prepare($totalQuery);
$totalStmt->bind_param($tTypes, ...$tParams);
$totalStmt->execute();
$totalResult = $totalStmt->get_result()->fetch_assoc();

$totalEarnings = $totalResult['total_earnings'] ?? 0;
$totalDeliveries = $totalResult['total_deliveries'] ?? 0;

$totalStmt->close();
$stmt->close();
$conn->close();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Driver Earnings | A To B Delivery</title>
    <?php include '../includes/head_tags.php'; ?>
</head>
<body class="bg-success-subtle d-flex flex-column min-vh-100">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand text-success fw-bold" href="driver_dashboard.php">A To B Delivery</a>
        <div class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav">
                <li class="nav-item me-3">
                    <span class="nav-link">Hello, <strong><?php echo htmlspecialchars($first_name); ?></strong></span>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-danger" href="../logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- MAIN CONTENT -->
<div class="container mt-5 flex-grow-1">
    <div class="row">
        <!-- Earnings Summary Card -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-success">
                <div class="card-header bg-success text-white text-center fw-bold">Earnings Summary</div>
                <div class="card-body text-center">
                    <h4 class="fw-bold text-success">Ks <?= number_format($totalEarnings, 0) ?></h4>
                    <p class="text-muted mb-1">Total Earnings</p>
                    <h5 class="fw-bold"><?= $totalDeliveries ?></h5>
                    <p class="text-muted mb-0">Completed Deliveries</p>
                    <hr>
                    <form method="GET" class="text-start">
                        <div class="mb-2">
                            <label class="form-label">From</label>
                            <input type="date" name="from" class="form-control" value="<?= htmlspecialchars($from) ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">To</label>
                            <input type="date" name="to" class="form-control" value="<?= htmlspecialchars($to) ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Payment Status</label>
                            <select name="status" class="form-select">
                                <option value="">All</option>
                                <option value="Completed" <?= $status==='Completed'?'selected':''; ?>>Completed</option>
                                <option value="Pending" <?= $status==='Pending'?'selected':''; ?>>Pending</option>
                                <option value="Failed" <?= $status==='Failed'?'selected':''; ?>>Failed</option>
                            </select>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">Apply Filters</button>
                            <a href="earnings.php" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Earnings Table -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <span>Completed Deliveries & Payments</span>
                    <a href="driver_dashboard.php" class="btn btn-light btn-sm">Back to Dashboard</a>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-end mb-2">
                        <button class="btn btn-sm btn-outline-success" onclick="exportCsv()"><i class="bi bi-download"></i> Export CSV</button>
                    </div>
                    <?php if ($result->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="earningsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Pickup</th>
                                        <th>Drop</th>
                                        <th>Item</th>
                                        <th>Amount (Ks)</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Paid At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td><?= htmlspecialchars($row['pickup_location']) ?></td>
                                        <td><?= htmlspecialchars($row['drop_location']) ?></td>
                                        <td><?= htmlspecialchars($row['item_description']) ?></td>
                                        <td><?= number_format($row['amount'], 0) ?></td>
                                        <td>
                                            <?php if ($row['payment_method'] === 'Cash'): ?>
                                                <span class="badge bg-secondary">Cash</span>
                                            <?php elseif ($row['payment_method'] === 'Card'): ?>
                                                <span class="badge bg-primary">Card</span>
                                            <?php else: ?>
                                                <span class="badge bg-info text-dark">Wallet</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($row['payment_status'] === 'Completed'): ?>
                                                <span class="badge bg-success">Completed</span>
                                            <?php elseif ($row['payment_status'] === 'Pending'): ?>
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Failed</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $row['paid_at'] ? date("M d, Y H:i", strtotime($row['paid_at'])) : '-' ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info text-center">No completed deliveries yet.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FOOTER -->
<footer class="bg-white shadow-sm text-center py-3 mt-auto">
    <span class="text-muted">&copy; <?= date('Y'); ?> A To B Delivery</span>
</footer>

<script>
function exportCsv() {
  const rows = [];
  const headers = [];
  document.querySelectorAll('#earningsTable thead th').forEach(th => headers.push(th.textContent.trim()));
  rows.push(headers.join(','));
  document.querySelectorAll('#earningsTable tbody tr').forEach(tr => {
    const cols = [];
    tr.querySelectorAll('td').forEach(td => {
      let text = td.textContent.trim().replaceAll('"','""');
      if (text.includes(',') || text.includes('"')) text = '"' + text + '"';
      cols.push(text);
    });
    rows.push(cols.join(','));
  });
  const blob = new Blob([rows.join('\n')], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  const dt = new Date();
  a.download = `earnings_${dt.getFullYear()}-${String(dt.getMonth()+1).padStart(2,'0')}-${String(dt.getDate()).padStart(2,'0')}.csv`;
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
}
</script>

</body>
</html>
