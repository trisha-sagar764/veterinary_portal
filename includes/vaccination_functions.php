<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

/**
 * Get all vaccinations for a specific pet
 */
function getPetVaccinations($pet_id, $pet_owner_id) {
    $db = getDatabaseConnection();
    $stmt = $db->prepare("
        SELECT v.*, vt.vaccine_name 
        FROM vaccinations v
        JOIN vaccine_types vt ON v.vaccine_type_id = vt.vaccine_id
        JOIN pets p ON v.pet_id = p.pet_id
        WHERE v.pet_id = ? AND p.pet_owner_id = ?
        ORDER BY v.next_due_date DESC
    ");
    $stmt->execute([$pet_id, $pet_owner_id]);
    return $stmt->fetchAll();
}

/**
 * Get all vaccine types available
 */
function getAllVaccineTypes() {
    $db = getDatabaseConnection();
    $stmt = $db->query("SELECT * FROM vaccine_types ORDER BY vaccine_name");
    return $stmt->fetchAll();
}

/**
 * Add a new vaccination record
 */
function addVaccinationRecord($data, $pet_owner_id) {
    $db = getDatabaseConnection();
    
    // Validate pet belongs to owner
    $stmt = $db->prepare("SELECT pet_id FROM pets WHERE pet_id = ? AND pet_owner_id = ?");
    $stmt->execute([$data['pet_id'], $pet_owner_id]);
    if (!$stmt->fetch()) {
        return ['success' => false, 'error' => 'Invalid pet selected'];
    }
    
    // Get vaccine duration
    $stmt = $db->prepare("SELECT default_duration_months FROM vaccine_types WHERE vaccine_id = ?");
    $stmt->execute([$data['vaccine_type_id']]);
    $vaccine = $stmt->fetch();
    
    if (!$vaccine) {
        return ['success' => false, 'error' => 'Invalid vaccine type selected'];
    }
    
    $duration_months = $vaccine['default_duration_months'];
    $next_due_date = date('Y-m-d', strtotime($data['date_administered'] . " + $duration_months months"));
    
    try {
        $stmt = $db->prepare("
            INSERT INTO vaccinations (
                pet_id, 
                vaccine_type_id, 
                date_administered, 
                next_due_date, 
                administered_by, 
                notes
            ) VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $success = $stmt->execute([
            $data['pet_id'],
            $data['vaccine_type_id'],
            $data['date_administered'],
            $next_due_date,
            $data['administered_by'],
            $data['notes']
        ]);
        
        return ['success' => $success, 'vaccination_id' => $db->lastInsertId()];
        
    } catch (PDOException $e) {
        error_log("Add vaccination error: " . $e->getMessage());
        return ['success' => false, 'error' => 'Database error occurred'];
    }
}

/**
 * Validate vaccination form data
 */
function validateVaccinationForm($data) {
    $errors = [];
    
    if (empty($data['pet_id'])) {
        $errors[] = "Pet selection is required";
    }
    
    if (empty($data['vaccine_type_id'])) {
        $errors[] = "Vaccine type is required";
    }
    
    if (empty($data['date_administered'])) {
        $errors[] = "Date administered is required";
    } elseif (strtotime($data['date_administered']) > time()) {
        $errors[] = "Date administered cannot be in the future";
    }
    
    if (empty($data['administered_by'])) {
        $errors[] = "Administered by field is required";
    }
    
    return $errors;
}

/**
 * Get upcoming vaccinations for a pet owner
 */
function getUpcomingVaccinations($pet_owner_id, $limit = 3) {
    $db = getDatabaseConnection();
    $stmt = $db->prepare("
        SELECT v.*, p.pet_name, vt.vaccine_name
        FROM vaccinations v
        JOIN pets p ON v.pet_id = p.pet_id
        JOIN vaccine_types vt ON v.vaccine_type_id = vt.vaccine_id
        WHERE p.pet_owner_id = ? 
        AND v.next_due_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 30 DAY)
        ORDER BY v.next_due_date ASC
        LIMIT ?
    ");
    $stmt->execute([$pet_owner_id, $limit]);
    return $stmt->fetchAll();
}