<?php
require_once '../includes/config.php';
require_once '../includes/csrf.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
    http_response_code(403); // Forbidden
    echo json_encode([
        'available' => false,
        'message' => 'CSRF token validation failed',
        'debug' => [
            'expected_token' => $_SESSION['csrf_token'] ?? 'NOT SET',
            'received_token' => $_POST['csrf_token'] ?? 'NOT PROVIDED'
        ]
    ]);
    exit;
}

if (!isset($_SESSION['otp'])) {
    echo json_encode(['success' => false, 'message' => 'OTP expired. Please request a new one.']);
    exit;
}

if (empty($_POST['otp']) || $_POST['otp'] != $_SESSION['otp']) {
    echo json_encode(['success' => false, 'message' => 'Invalid OTP. Please try again.']);
    exit;
}

// OTP verified successfully
$_SESSION['mobile_verified'] = true;
unset($_SESSION['otp']);

echo json_encode(['success' => true, 'message' => 'Mobile number verified successfully']);