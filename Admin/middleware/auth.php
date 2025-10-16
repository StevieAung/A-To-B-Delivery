<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}
$ADMIN_NAME = $_SESSION['admin_name'] ?? "Admin";
$ADMIN_ROLE = $_SESSION['admin_role'] ?? "admin";
