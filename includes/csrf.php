<?php
require_once __DIR__ . '\config.php';

// Generate and store CSRF token if not exists
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validates submitted CSRF token
 * @param string $token The submitted token to validate
 * @return bool True if valid, false if invalid
 */
function validateCsrfToken($token) {
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        return false;
    }
    return true;
}

/**
 * Validates POST request CSRF token
 * Dies with JSON response if invalid (for AJAX)
 */
function validatePostCsrfToken() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!validateCsrfToken($token)) {
            die(json_encode(['error' => 'CSRF token validation failed']));
        }
    }
}