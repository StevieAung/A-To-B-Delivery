<?php
/**
 * --------------------------------------------------------
 * Database Connection – A To B Delivery Service Website
 * --------------------------------------------------------
 * Author: Sai Htet Aung Hlaing
 * Date: 2025-10-05
 * Version: 1.1 (Finalized)
 *
 * Description:
 *  • Connects PHP application to MySQL database
 *  • Designed for XAMPP localhost environment
 *  • Uses UTF-8 charset for multilingual (English/Myanmar) support
 * --------------------------------------------------------
 */

$servername = "localhost";
$username   = "root";        // Default XAMPP user
$password   = "";            // No password by default
$dbname     = "a2b_delivery"; // Updated to match finalized database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Set UTF-8 for multilingual text compatibility
$conn->set_charset("utf8mb4");

// Optional: confirm connection success (for debugging only)
// echo "✅ Database connected successfully!";
?>
