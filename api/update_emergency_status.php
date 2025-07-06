<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';

header('Content-Type: application/json');

if (!isset($_SESSION['staff_logged_in'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

if (!validateCSRFToken($_POST['csrf_token'])) {
    echo json_encode(['success' => false, 'error' => 'CSRF token validation failed']);
    exit;
}

$report_id = $_POST['report_id'] ?? null;
$new_status = $_POST['new_status'] ?? null;

if (!$report_id || !$new_status) {
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit;
}

$allowed_statuses = ['pending', 'dispatched', 'resolved', 'cancelled'];
if (!in_array($new_status, $allowed_statuses)) {
    echo json_encode(['success' => false, 'error' => 'Invalid status']);
    exit;
}

try {
    $db = getDatabaseConnection();
    $stmt = $db->prepare("UPDATE emergency_reports SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $report_id]);
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    error_log("Error updating emergency status: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error']);
}