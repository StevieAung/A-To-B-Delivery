<?php
session_start();
include '../Database/db.php';

// Redirect to login if user not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: register.php");
    exit();
}

$alert = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $pickup = trim($_POST['pickup_location']);
    $delivery = trim($_POST['delivery_location']);
    $description = trim($_POST['item_description']);
    $weight = floatval($_POST['weight']);
    $fragile = isset($_POST['is_fragile']) ? 1 : 0;
    $preferred_time = trim($_POST['preferred_time']);
    $delivery_mode = $_POST['delivery_mode'];

    $stmt = $conn->prepare("INSERT INTO delivery_requests 
        (user_id, pickup_location, delivery_location, item_description, weight, is_fragile, preferred_time, delivery_mode) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssdiss", $user_id, $pickup, $delivery, $description, $weight, $fragile, $preferred_time, $delivery_mode);

    if ($stmt->execute()) {
        $alert = "<div class='alert alert-success'>Delivery request created successfully!</div>";
    } else {
        $alert = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
}
?>

<!doctype html>
<html lang="en">
    <head>
        <title>Create Delivery Request</title>
        <?php include '../head_tags.php'; ?>
    </head>
<body class="d-flex flex-column min-vh-100 bg-success-subtle">
    <header>
        <!-- place navbar here -->
    </header>
    <main>
        <div class="container mt-5">
            <div class="card shadow-lg p-4 mx-5">
                <h2 class="mb-4 text-center">Create Delivery Request</h2>
                <?php echo $alert; ?>
                <form method="POST" action="">
                    <div class="d-flex justify-content-end mb-3">
                        <a href="my_deliveries.php" class="btn btn-outline-success me-2">My Deliveries</a>
                        <a href="track_delivery.php" class="btn btn-outline-warning">Track Delivery</a>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <label for="pickup_location" class="form-label">Pickup Location</label>
                            <input type="text" class="form-control" name="pickup_location" id="pickup_location" required>
                        </div>
                        <div class="col">
                            <label for="delivery_location" class="form-label">Delivery Location</label>
                            <input type="text" class="form-control" name="delivery_location" id="delivery_location" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="item_description" class="form-label">Item Description</label>
                        <textarea class="form-control" name="item_description" id="item_description" rows="3" required></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="weight" class="form-label">Weight (kg)</label>
                            <input type="number" step="0.1" class="form-control" name="weight" id="weight" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label d-block">Is Fragile?</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_fragile" id="is_fragile">
                                <label class="form-check-label" for="is_fragile">Yes</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="preferred_time" class="form-label">Preferred Time</label>
                            <input type="text" class="form-control" name="preferred_time" id="preferred_time" placeholder="e.g., Morning, 10am-12pm" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="delivery_mode" class="form-label">Delivery Mode</label>
                        <select class="form-select" name="delivery_mode" id="delivery_mode" required>
                            <option value="" disabled selected>Select mode</option>
                            <option value="Motorbike">Motorbike</option>
                            <option value="Bicycle">Bicycle</option>
                        </select>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success fw-bold">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <footer>
        <!-- place footer here -->
    </footer>
    <!-- Bootstrap JavaScript Libraries -->
    <script
        src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"
    ></script>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"
    ></script>
</body>
</html>
