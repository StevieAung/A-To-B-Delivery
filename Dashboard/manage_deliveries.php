<?php
// filepath: /Applications/XAMPP/xamppfiles/htdocs/Delivery/Dashboard/manage_deliveries.php
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

// Fetch all deliveries
$deliveries = $conn->query("SELECT * FROM delivery_requests ORDER BY created_at DESC");

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Deliveries</title>
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
            <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a>
            <a href="manage_deliveries.php" class="active"><i class="fas fa-shipping-fast"></i> Manage Deliveries</a>
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
                    Manage Deliveries
                </span>
                <a href="../logout.php" class="btn btn-outline-primary">Logout</a>
            </nav>

            <!-- Deliveries Table -->
            <div class="card shadow-sm p-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>User ID</th>
                                <th>Pickup Location</th>
                                <th>Delivery Location</th>
                                <th>Status</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($deliveries->num_rows > 0): ?>
                                <?php while ($delivery = $deliveries->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo $delivery['request_id']; ?></td>
                                        <td><?php echo htmlspecialchars($delivery['user_id']); ?></td>
                                        <td><?php echo htmlspecialchars($delivery['pickup_location']); ?></td>
                                        <td><?php echo htmlspecialchars($delivery['delivery_location']); ?></td>
                                        <td>
                                            <span class="badge
                                                <?php echo $delivery['status']=='pending' ? 'bg-warning' : ($delivery['status']=='delivered' ? 'bg-success' : 'bg-secondary'); ?>">
                                                <?php echo ucfirst($delivery['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date("M d, Y H:i", strtotime($delivery['created_at'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-muted text-center">No deliveries found</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>