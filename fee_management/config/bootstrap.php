<?php
// Prevent direct access
if (!defined('INCLUDED_FROM_INDEX')) {
    die('Direct access not permitted');
}

// Session configuration (must be set before session starts)
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));

// Start session
session_start();

// Base URL configuration
$base_url = '/fee_management/';

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Time zone
date_default_timezone_set('Asia/Kolkata');

// Load database connection
require_once __DIR__ . '/db_connect.php'; 