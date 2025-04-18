<?php
// Define constant to prevent direct access to included files
define('INCLUDED_FROM_INDEX', true);

// Include bootstrap file
require_once 'bootstrap.php';

// New credentials
$username = 'admin';
$password = 'admin';

// Generate password hash
$hash = password_hash($password, PASSWORD_DEFAULT);

// Update existing admin or create new one
$stmt = $conn->prepare("
    INSERT INTO admins (username, password) 
    VALUES (?, ?) 
    ON DUPLICATE KEY UPDATE password = ?
");

$stmt->bind_param("sss", $username, $hash, $hash);

if ($stmt->execute()) {
    echo "Admin credentials updated successfully!\n";
    echo "Username: admin\n";
    echo "Password: admin\n";
} else {
    echo "Error updating admin credentials: " . $conn->error;
}

$stmt->close();
$conn->close(); 