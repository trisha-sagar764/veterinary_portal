<?php
// Database configuration
define('DB_HOST', 'localhost:3307');
define('DB_NAME', 'veterinary_portal');
define('DB_USER', 'root');
define('DB_PASS', '');
define('BASE_URL', '/veterinary_portal/');

// Security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set timezone
date_default_timezone_set('Asia/Kolkata');

define('DEBUG_MODE', true);