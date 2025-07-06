<?php
function validateAppointmentForm($data, $pets, $facilities) {
    $errors = [];
    $required_fields = [
        'pet_id' => 'Pet',
        'facility_id' => 'Veterinary Facility',
        'appointment_type' => 'Appointment Type',
        'preferred_date' => 'Preferred Date',
        'preferred_time' => 'Preferred Time',
    ];
    
    foreach ($required_fields as $field => $name) {
        if (empty($data[$field])) {
            $errors[] = "$name is required.";
        }
    }
    
    // Validate pet belongs to owner
    $valid_pet = false;
    foreach ($pets as $pet) {
        if ($pet['pet_id'] == $data['pet_id']) {
            $valid_pet = true;
            break;
        }
    }
    
    if (!$valid_pet) {
        $errors[] = "Invalid pet selected.";
    }
    
    // Validate facility exists
    $valid_facility = false;
    foreach ($facilities as $facility) {
        if ($facility['facility_id'] == $data['facility_id']) {
            $valid_facility = true;
            break;
        }
    }
    
    if (!$valid_facility) {
        $errors[] = "Invalid facility selected.";
    }
    
        // Validate date and time
    if (!empty($data['preferred_date']) && !empty($data['preferred_time'])) {
        try {
            $today = new DateTime();
            $appointment_date = new DateTime($data['preferred_date']);
            $isToday = ($appointment_date->format('Y-m-d') == $today->format('Y-m-d'));
            
            // Date cannot be in the past
            if ($appointment_date < $today && !$isToday) {
                $errors[] = "Appointment date cannot be in the past.";
            }
            
            // For today's appointments, time cannot be in the past
            if ($isToday) {
                $appointment_time = DateTime::createFromFormat('H:i', $data['preferred_time']);
                $current_time = new DateTime();
                
                if ($appointment_time < $current_time) {
                    $errors[] = "Appointment time cannot be in the past for today's date.";
                }
            }
        } catch (Exception $e) {
            $errors[] = "Invalid date or time format.";
        }
    }
    
    return $errors;
}


function processAppointmentBooking($db, $formData, $pet_owner_id) {
    try {
        // Use the stored procedure
        $stmt = $db->prepare("CALL sp_create_appointment_with_token(?, ?, ?, ?, ?, ?, ?, ?, @app_id, @token)");
        $stmt->execute([
            $formData['pet_id'],
            $formData['facility_id'],
            $formData['appointment_type'],
            $formData['preferred_date'],
            $formData['preferred_time'],
            $formData['symptoms'],
            $formData['additional_notes'],
            $pet_owner_id
        ]);
        
        // Get the outputs
        $result = $db->query("SELECT @app_id AS appointment_id, @token AS token_number")->fetch();
        
        if ($result && $result['appointment_id']) {
            return [
                'success' => true,
                'appointment_id' => $result['appointment_id'],
                'token_number' => $result['token_number']
            ];
        } else {
            return ['success' => false, 'error' => 'Failed to create appointment'];
        }
    } catch (PDOException $e) {
        error_log("Appointment booking error: " . $e->getMessage());
        return ['success' => false, 'error' => 'Database error occurred'];
    }
}

function getPetDetails($db, $pet_id) {
    $stmt = $db->prepare("
        SELECT p.*, s.species_name, b.breed_name 
        FROM pets p
        JOIN species s ON p.species_id = s.species_id
        JOIN breeds b ON p.breed_id = b.breed_id
        WHERE p.pet_id = ?
    ");
    $stmt->execute([$pet_id]);
    return $stmt->fetch();
}

function getFacilityDetails($db, $facility_id) {
    $stmt = $db->prepare("SELECT * FROM veterinary_facilities WHERE facility_id = ?");
    $stmt->execute([$facility_id]);
    return $stmt->fetch();
}
?>