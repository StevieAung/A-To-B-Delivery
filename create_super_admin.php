<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include DB connection
include './Database/db.php'; // adjust path to your db.php

// Super admin details
$first_name = "Super";
$last_name = "Admin";
$email = "superadmin@mail.com";
$password_plain = "SuperAdmin123!!"; // choosing strong password
$role = "super_admin";

// Hash password
$password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);

// Insert super admin
$sql = "INSERT INTO admins (first_name, last_name, email, password, role) 
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $first_name, $last_name, $email, $password_hashed, $role);

if ($stmt->execute()) {
    echo "✅ Super Admin created successfully. You can log in with: <br>Email: $email<br>Password: $password_plain";
} else {
    echo "❌ Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
