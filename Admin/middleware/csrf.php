<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

function csrf_token() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_input() {
    echo '<input type="hidden" name="csrf" value="'.htmlspecialchars(csrf_token()).'">';
}
function csrf_verify() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $ok = isset($_POST['csrf']) && hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf']);
        if (!$ok) { http_response_code(403); die("Invalid CSRF token."); }
    }
}
