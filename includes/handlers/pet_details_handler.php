<?php
// includes/handlers/pet_details_handler.php
function getPetDetails($petId, $ownerId) {
    try {
        $db = getDatabaseConnection();
        
        $pet = $db->prepare("
            SELECT p.*, s.species_name, b.breed_name, po.name as owner_name
            FROM pets p
            JOIN species s ON p.species_id = s.species_id
            JOIN breeds b ON p.breed_id = b.breed_id
            JOIN pet_owners po ON p.pet_owner_id = po.pet_owner_id
            WHERE p.pet_id = ? AND p.pet_owner_id = ?
        ")->execute([$petId, $ownerId])->fetch();

        if (!$pet) return false;

        return [
            'pet' => $pet,
            'vaccinations' => getPetVaccinations($db, $petId),
            'medicalRecords' => getPetMedicalRecords($db, $petId),
            'appointments' => getPetAppointments($db, $petId)
        ];
    } catch (PDOException $e) {
        error_log("Pet details error: " . $e->getMessage());
        return false;
    }
}

function getPetVaccinations($db, $petId) {
    return $db->prepare("
        SELECT * FROM vaccinations 
        WHERE pet_id = ?
        ORDER BY next_due_date ASC
    ")->execute([$petId])->fetchAll();
}

function getPetMedicalRecords($db, $petId) {
    return $db->prepare("
        SELECT mr.*, v.name as vet_name
        FROM medical_records mr
        LEFT JOIN veterinarians v ON mr.vet_id = v.vet_id
        WHERE mr.pet_id = ?
        ORDER BY mr.record_date DESC
    ")->execute([$petId])->fetchAll();
}

function getPetAppointments($db, $petId) {
    return $db->prepare("
        SELECT a.*, v.name as vet_name
        FROM appointments a
        LEFT JOIN veterinarians v ON a.vet_id = v.vet_id
        WHERE a.pet_id = ? AND a.status = 'Scheduled' AND a.appointment_date >= NOW()
        ORDER BY a.appointment_date ASC
        LIMIT 3
    ")->execute([$petId])->fetchAll();
}