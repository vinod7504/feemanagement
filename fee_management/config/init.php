<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Base URL configuration
$base_url = '/fee_management/';

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Time zone
date_default_timezone_set('Asia/Kolkata');

// Load database connection
require_once __DIR__ . '/db_connect.php'; 