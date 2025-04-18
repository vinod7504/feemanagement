<?php
// This file should only be included, not accessed directly
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>JNTUACEA Fee Management</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <?php if (isset($additional_css)) echo $additional_css; ?>
</head>
<body>
    <!-- Top Header Bar -->
    <div class="top-header">
        <div class="top-header-content">
            <div class="top-header-contact">
                <a href="tel:+918518282222"><i class="fas fa-phone"></i> 08554-273013</a>
                <a href="mailto:principal@jntuacea.ac.in"><i class="fas fa-envelope"></i> principal.cea@jntua.ac.in</a>
            </div>
            <?php if (isset($_SESSION['admin_id'])): ?>
                <div class="user-info">
                    Welcome, Admin <Button><a href="<?php echo $base_url; ?>includes/logout.php">Logout</a></Button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Header -->
    <header class="main-header">
        <div class="header-content">
            <div class="logo-section">
                <img src="https://www.jntuacea.ac.in/images/jntuaceatp.png" alt="JNTUACEA Logo">
                <div class="logo-text">
                    <h1>JNTUACEA</h1>
                    <p>Fee Management System</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Navigation -->
    <?php if (isset($_SESSION['admin_id'])): ?>
    <nav class="main-nav">
        <div class="nav-content">
            <ul class="nav-menu">
                <li><a href="<?php echo $base_url; ?>dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="<?php echo $base_url; ?>students/add.php"><i class="fas fa-user-plus"></i> Add Student</a></li>
                <li><a href="<?php echo $base_url; ?>students/view.php"><i class="fas fa-users"></i> View Students</a></li>
                <li><a href="<?php echo $base_url; ?>fees/add.php"><i class="fas fa-money-bill-wave"></i> Add Fee</a></li>
            </ul>
        </div>
    </nav>
    <?php endif; ?> 