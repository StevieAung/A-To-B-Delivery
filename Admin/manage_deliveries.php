<?php
/**
 * A To B Delivery – Manage Deliveries (Scrollable + DataTables)
 * -------------------------------------------------------------
 * Features:
 *  - Filter by delivery status
 *  - Update delivery status with CSRF protection
 *  - Searchable, sortable, exportable table with horizontal scroll
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include __DIR__ . "/middleware/auth.php";
include '../Database/db.php';
include __DIR__ . "/middleware/csrf.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') csrf_verify();

$allowed = ['all', 'pending', 'accepted', 'picked_up', 'in_transit', 'delivered', 'cancelled'];

$filter = $_GET['status'] ?? 'all';
if (!in_array($filter, $allowed)) $filter = 'all';

// ✅ Include driver name
$sql = "
    SELECT 
        d.request_id, 
        s.first_name AS sender_first, s.last_name AS sender_last,
        dr.first_name AS driver_first, dr.last_name AS driver_last,
        d.pickup_location, d.drop_location, 
        d.delivery_status, d.created_at
    FROM delivery_requests d
    JOIN users s ON d.sender_id = s.user_id
    LEFT JOIN users dr ON d.driver_id = dr.user_id
";
if ($filter !== 'all') {
    $sql .= " WHERE d.delivery_status = '" . $conn->real_escape_string($filter) . "'";
}
$sql .= " ORDER BY d.created_at DESC";
$rows = $conn->query($sql);

// ✅ Update delivery status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'], $_POST['new_status'])) {
    $rid = (int) $_POST['request_id'];
    $new = $_POST['new_status'];
    if (in_array($new, $allowed) && $new !== 'all') {
        $stmt = $conn->prepare("UPDATE delivery_requests SET delivery_status=? WHERE request_id=?");
        $stmt->bind_param("si", $new, $rid);
        $stmt->execute();
        $stmt->close();
        $msg = "Updated request #$rid to " . htmlspecialchars(str_replace('_',' ', $new));
        header("Location: manage_deliveries.php?status=" . urlencode($filter) . "&msg=" . urlencode($msg));
        exit;
    }
}
?>
<?php include __DIR__."/includes/head.php"; ?>
<?php include __DIR__."/includes/sidebar.php"; ?>

<main>
<?php include __DIR__."/includes/topbar.php"; ?>

<!-- Message -->
<?php if(isset($_GET['msg'])): ?>
  <div class="alert alert-success alert-dismissible fade show mt-2 shadow-sm" role="alert">
    <i class="bi bi-check-circle me-1"></i> <?= htmlspecialchars($_GET['msg']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<!-- Filter -->
<div class="card p-3 shadow-sm border-0 mb-3">
  <form class="d-flex align-items-center gap-2 flex-wrap" method="GET">
    <label class="form-label mb-0 fw-semibold">Filter by Status:</label>
    <select name="status" class="form-select shadow-sm" style="max-width:220px" onchange="this.form.submit()">
      <option value="all" <?= $filter==='all'?'selected':'' ?>>All</option>
      <?php foreach($allowed as $s): if($s==='all') continue; ?>
        <option value="<?= $s ?>" <?= $filter===$s?'selected':'' ?>>
          <?= ucfirst(str_replace('_',' ',$s)) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </form>
</div>

<!-- Table -->
<div class="card p-3 shadow-sm border-0">
  <div class="table-responsive" style="overflow-x: auto;">
    <table id="deliveriesTable" class="table table-striped table-bordered align-middle text-nowrap" style="min-width: 1100px;">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Sender</th>
          <th>Driver</th>
          <th>Pickup</th>
          <th>Drop</th>
          <th>Status</th>
          <th>Requested</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
      <?php if($rows && $rows->num_rows): while($r=$rows->fetch_assoc()): ?>
        <?php
          $color = match($r['delivery_status']) {
            'pending' => 'bg-warning text-dark',
            'accepted' => 'bg-info text-dark',
            'picked_up' => 'bg-primary',
            'in_transit' => 'bg-secondary',
            'delivered' => 'bg-success',
            'cancelled' => 'bg-dark',
            default => 'bg-light text-dark'
          };
          $driver = $r['driver_first']
              ? htmlspecialchars($r['driver_first'].' '.$r['driver_last'])
              : '<span class="text-muted fst-italic">Unassigned</span>';
        ?>
        <tr>
          <td class="fw-semibold">#<?= (int)$r['request_id'] ?></td>
          <td><?= htmlspecialchars($r['sender_first'].' '.$r['sender_last']) ?></td>
          <td><?= $driver ?></td>
          <td><i class="bi bi-geo-alt text-danger me-1"></i><?= htmlspecialchars($r['pickup_location']) ?></td>
          <td><i class="bi bi-flag text-success me-1"></i><?= htmlspecialchars($r['drop_location']) ?></td>
          <td><span class="badge <?= $color ?>"><?= ucfirst(str_replace('_',' ',$r['delivery_status'])) ?></span></td>
          <td><?= date("M d, Y H:i", strtotime($r['created_at'])) ?></td>
          <td>
            <form method="post" class="d-flex gap-2 align-items-center">
              <?php csrf_input(); ?>
              <input type="hidden" name="request_id" value="<?= (int)$r['request_id'] ?>">
              <select name="new_status" class="form-select form-select-sm border-0 bg-light" style="max-width:150px">
                <?php foreach($allowed as $s){ if($s==='all') continue; ?>
                  <option value="<?= $s ?>" <?= $s===$r['delivery_status']?'selected':'' ?>>
                    <?= ucfirst(str_replace('_',' ',$s)) ?>
                  </option>
                <?php } ?>
              </select>
              <button class="btn btn-sm btn-outline-primary" title="Update status">
                <i class="bi bi-arrow-repeat"></i>
              </button>
            </form>
          </td>
        </tr>
      <?php endwhile; else: ?>
        <tr><td colspan="8" class="text-center text-muted py-3">No deliveries found</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

</main>

<?php include __DIR__."/includes/footer.php"; ?>

<!-- =================== DataTables =================== -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
$(document).ready(function () {
  const table = $('#deliveriesTable').DataTable({
    scrollX: true,
    pageLength: 10,
    lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
    order: [[0, 'desc']],
    responsive: false,
    dom: '<"d-flex justify-content-between align-items-center mb-2 flex-wrap"Bf>rt<"d-flex justify-content-between mt-3"lip>',
    language: {
      search: "", // remove default label
      searchPlaceholder: "Search deliveries..."
    },
    buttons: [
      { extend: 'copy',  text: '📋 Copy',  className: 'btn btn-sm dt-btn-flat' },
      { extend: 'csv',   text: '🧾 CSV',   className: 'btn btn-sm dt-btn-flat' },
      { extend: 'excel', text: '🗂 Excel', className: 'btn btn-sm dt-btn-flat' },
      { extend: 'pdf',   text: '📄 PDF',   className: 'btn btn-sm dt-btn-flat' },
      { extend: 'print', text: '🖨 Print', className: 'btn btn-sm dt-btn-flat' }
    ]
  });

  /* === Replace default search bar with custom Bootstrap input group === */
  const dataTableWrapper = $('#deliveriesTable_wrapper');
  const searchInput = dataTableWrapper.find('input[type="search"]');
  searchInput
    .addClass('form-control form-control-sm shadow-sm border-secondary')
    .attr('placeholder', 'Search by sender, driver, location...');

  // Wrap the search input into a Bootstrap input-group with a button
  const inputGroup = $('<div class="input-group input-group-sm" style="max-width:280px;"></div>');
  const searchBtn = $('<button class="btn btn-outline-secondary" type="button"><i class="bi bi-search"></i></button>');

  // Move the original search input into our new input group
  searchInput.wrap(inputGroup);
  searchInput.after(searchBtn);

  // Handle search button click
  searchBtn.on('click', function() {
    table.search(searchInput.val()).draw();
  });

  // Handle Enter key
  searchInput.on('keypress', function(e) {
    if (e.which === 13) {
      table.search(this.value).draw();
    }
  });
});
</script>

