<?php
session_start();

// Enforce POST and CSRF validation
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: home.php');
    exit;
}

$tokenValid = isset($_POST['csrf'], $_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $_POST['csrf']);
if (!$tokenValid) {
    http_response_code(403);
    echo 'Invalid CSRF token.';
    exit;
}

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Optional: Delete session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect to home page
header("Location: home.php");
exit;
?>
