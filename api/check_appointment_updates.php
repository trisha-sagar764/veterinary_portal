<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['pet_owner_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$last_check = (int)$_GET['last_check'];
$pet_owner_id = (int)$_SESSION['pet_owner_id'];

try {
    $db = getDatabaseConnection();
    $stmt = $db->prepare("
        SELECT COUNT(*) as changes 
        FROM appointments a
        JOIN pets p ON a.pet_id = p.pet_id
        WHERE p.pet_owner_id = ? 
        AND a.last_updated > FROM_UNIXTIME(?)
    ");
    $stmt->execute([$pet_owner_id, $last_check]);
    $result = $stmt->fetch();
    
    echo json_encode(['updated' => $result['changes'] > 0]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error']);
}