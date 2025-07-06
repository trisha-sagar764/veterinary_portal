<?php
// includes/status_functions.php

function updateAppointmentStatus() {
    require_once __DIR__ . '/config.php';
    
    // Validate inputs
    $appointment_id = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);
    $new_status = trim($_POST['new_status'] ?? '');

    if (!$appointment_id || empty($new_status)) {
        http_response_code(400);
        die(json_encode(['error' => 'Invalid input']));
    }

    // Define allowed statuses
    $allowed_statuses = ['Pending', 'Confirmed', 'Completed', 'Cancelled', 'Rescheduled', 'No-Show'];
    if (!in_array($new_status, $allowed_statuses)) {
        http_response_code(400);
        die(json_encode(['error' => 'Invalid status']));
    }

    try {
        $pdo = getDatabaseConnection();
        
        // Verify staff has permission
        $stmt = $pdo->prepare("
            SELECT a.appointment_id 
            FROM appointments a
            JOIN facility_staff fs ON a.facility_id = fs.facility_id
            WHERE a.appointment_id = ?
            AND fs.staff_id = ?
        ");
        $stmt->execute([$appointment_id, $_SESSION['staff_id']]);
        
        if ($stmt->rowCount() === 0) {
            http_response_code(403);
            die(json_encode(['error' => 'Unauthorized']));
        }

        // Update status
        $update_stmt = $pdo->prepare("
            UPDATE appointments 
            SET status = ?, 
                updated_at = NOW()
            WHERE appointment_id = ?
        ");
        $update_stmt->execute([$new_status, $appointment_id]);

        // Return success
        echo json_encode([
            'success' => true,
            'new_status' => $new_status,
            'message' => 'Status updated'
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        die(json_encode(['error' => 'Database error']));
    }
}

function getStatusColor($status) {
    $colors = [
        'Pending' => 'warning',
        'Confirmed' => 'primary',
        'Completed' => 'success',
        'Cancelled' => 'danger',
        'Rescheduled' => 'info',
        'No-Show' => 'dark'
    ];
    return $colors[$status] ?? 'secondary';
}