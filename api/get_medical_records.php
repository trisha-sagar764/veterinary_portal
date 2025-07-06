<?php
require_once  'C:\xampp\htdocs\veterinary_portal\includes\config.php';
require_once  'C:\xampp\htdocs\veterinary_portal\includes\functions.php';

$petId = $_GET['pet_id'] ?? null;
if (!$petId) die('Invalid request');

try {
    $db = getDatabaseConnection();
    $stmt = $db->prepare("
        SELECT mr.*, s.full_name as staff_name 
        FROM medical_records mr
        LEFT JOIN facility_staff s ON mr.staff_id = s.staff_id
        WHERE mr.pet_id = ?
        ORDER BY mr.record_date DESC
    ");
    $stmt->execute([$petId]);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($records as $record): ?>
        <div class="card mb-3">
            <div class="card-header">
                <?= date('M j, Y', strtotime($record['record_date'])) ?>
                <span class="badge bg-secondary float-end">
                    <?= htmlspecialchars($record['staff_name'] ?? 'System') ?>
                </span>
            </div>
            <div class="card-body">
                <h5><?= htmlspecialchars($record['diagnosis']) ?></h5>
                <p><?= nl2br(htmlspecialchars($record['treatment'])) ?></p>
                <?php if ($record['medications']): ?>
                    <div class="alert alert-info p-2">
                        <strong>Medications:</strong> 
                        <?= nl2br(htmlspecialchars($record['medications'])) ?>
                    </div>
                <?php endif; ?>
                <?php if ($record['notes']): ?>
                    <div class="mt-2">
                        <strong>Notes:</strong>
                        <?= nl2br(htmlspecialchars($record['notes'])) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach;
} catch (PDOException $e) {
    echo '<div class="alert alert-danger">Error loading records</div>';
}