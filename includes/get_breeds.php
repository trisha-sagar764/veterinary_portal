<?php
// includes/get_breeds.php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

header('Content-Type: application/json');

if (!isset($_GET['species_id'])) {
    echo json_encode([]);
    exit;
}

$speciesId = $_GET['species_id'];
$breeds = [];

try {
    $db = getDatabaseConnection();
    $stmt = $db->prepare("SELECT breed_id, breed_name FROM breeds WHERE species_id = ? ORDER BY breed_name");
    $stmt->execute([$speciesId]);
    $breeds = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Breed fetch error: " . $e->getMessage());
}

echo json_encode($breeds);