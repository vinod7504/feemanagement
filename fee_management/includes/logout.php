<?php
// Define constant to prevent direct access to included files
define('INCLUDED_FROM_INDEX', true);

// Include bootstrap file
require_once '../config/init.php';

// Destroy session
session_destroy();

// Redirect to login page
header("Location: ../index.php");
exit();
?> 