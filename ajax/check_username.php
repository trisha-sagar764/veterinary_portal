<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

// Debugging - log the actual request method
error_log("Actual request method: " . $_SERVER['REQUEST_METHOD']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Additional debug info
    $debugInfo = [
        'request_method' => $_SERVER['REQUEST_METHOD'],
        'post_data' => $_POST,
        'server' => $_SERVER
    ];
    error_log("Invalid request method debug: " . print_r($debugInfo, true));
    
    echo json_encode([
        'available' => false,
        'message' => 'Invalid request method. Only POST is allowed.',
        'debug' => $debugInfo
    ]);
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
// Database check
try {
    $db = getDatabaseConnection();
    $stmt = $db->prepare("SELECT id FROM pet_owners WHERE username = :username");
    $stmt->bindParam(':username', $_POST['username']);
    $stmt->execute();
    
    $response = [
        'available' => $stmt->rowCount() === 0,
        'message' => $stmt->rowCount() === 0 ? 'Username available' : 'Username already taken',
        'username' => $_POST['username']
    ];
    
    echo json_encode($response);
    
} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    error_log("Database error in check_username.php: " . $e->getMessage());
    echo json_encode([
        'available' => false,
        'message' => 'Database error',
        'error_details' => $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    error_log("General error in check_username.php: " . $e->getMessage());
    echo json_encode([
        'available' => false,
        'message' => 'An error occurred',
        'error_details' => $e->getMessage()
    ]);
}