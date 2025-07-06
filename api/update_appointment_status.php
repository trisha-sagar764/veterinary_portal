<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';

header('Content-Type: application/json');

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// Validate request
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['appointment_id'], $_POST['new_status'], $_POST['csrf_token'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

// Verify CSRF token
if (!validateCsrfToken($_POST['csrf_token'])) {
    echo json_encode(['success' => false, 'error' => 'CSRF token validation failed']);
    exit;
}

// Validate inputs
$appointment_id = (int)$_POST['appointment_id'];
$new_status = $_POST['new_status'];
$allowed_statuses = ['Pending', 'Confirmed', 'Completed', 'Cancelled'];

if (!in_array($new_status, $allowed_statuses)) {
    echo json_encode(['success' => false, 'error' => 'Invalid status']);
    exit;
}

// Update status
require_once __DIR__ . '/../includes/staff_functions.php';
$staff_id = $_SESSION['staff_id'] ?? 0;

if (updateAppointmentStatus($appointment_id, $new_status, $staff_id)) {
    // Create medical record if completed
    if ($new_status === 'Completed') {
        createMedicalRecordFromAppointment($appointment_id, $staff_id);
    }
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database update failed']);
}