<?php
// config/config.php

// 1. BASE_URL Detection (Robust for Windows/XAMPP)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
// Normalize slashes for Windows compatibility
$scriptDir = str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME']))); 
$path = rtrim($scriptDir, '/');
define('BASE_URL', $protocol . "://" . $host . $path . "/");

// 2. Application Constants
define('APP_NAME', 'QuizMaster');
define('CURRENCY', 'USD');

// 3. PayPal Configuration
// Using the Sandbox Credentials provided.
// IMPORTANT: NEVER expose PAYPAL_SECRET to the frontend/JS.
define('PAYPAL_CLIENT_ID', 'AaG_Dfgi9pSCT8FPYwoXb_OWh6Cwyflomb8kBqUQHTAkmAv6HyOPrIAw4Vn8WkG2ft6GAyF1FdSshSiW'); 
define('PAYPAL_SECRET', 'EDwRLSc27Gfx34vnywykToNdVpS-oC-klOf6f5Ku8GGolrdqn9TSFekGBsOZkxtSWDLkgY3qAXnSNNdq');
define('PAYPAL_MODE', 'sandbox'); // Set to 'live' for production

// 4. Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
?>
