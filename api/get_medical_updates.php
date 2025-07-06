<?php
require_once  'C:\xampp\htdocs\veterinary_portal\includes\config.php';
require_once  'C:\xampp\htdocs\veterinary_portal\includes\functions.php';

header('Content-Type: application/json');

$petId = $_GET['pet_id'] ?? null;
if (!$petId) {
    die(json_encode(['error' => 'No pet ID provided']));
}

try {
    $db = getDatabaseConnection();
    $lastUpdate = $db->prepare("SELECT last_medical_update FROM pets WHERE pet_id = ?")
                    ->execute([$petId])
                    ->fetchColumn();
    
    echo json_encode([
        'last_update' => $lastUpdate ?: 'never',
        'pet_id' => $petId
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error']);
}