<?php
require_once '../includes/config.php';
require_once '../includes/csrf.php';
require_once '../includes/functions.php';

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

if (empty($_POST['mobile']) || !preg_match('/^[0-9]{10}$/', $_POST['mobile'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid mobile number']);
    exit;
}

try {
    $db = getDatabaseConnection();
    
    // Check if mobile exists
    $stmt = $db->prepare("SELECT id FROM pet_owners WHERE mobile = ?");
    $stmt->execute([$_POST['mobile']]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'Mobile number already registered']);
        exit;
    }
    
    // Generate OTP
    $otp = generateOTP();
    $_SESSION['otp'] = $otp;
    $_SESSION['mobile'] = $_POST['mobile'];
    $_SESSION['otp_time'] = time();
    
    // In production: Send OTP via SMS API here
    // For demo purposes, we'll log it
    file_put_contents('../otp_log.txt', "OTP for {$_POST['mobile']}: $otp\n", FILE_APPEND);
    
    echo json_encode([
        'success' => true,
        'message' => 'OTP sent successfully',
        'demo_otp' => $otp // Remove in production
    ]);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}