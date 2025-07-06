<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

/**
 * Get today's appointments for the staff's facility
 */
function getTodaysAppointments($facility_id) {
    try {
        $db = getDatabaseConnection();
        $stmt = $db->prepare("
            SELECT a.*, 
                   p.pet_name, p.pet_id, p.species_id, p.breed_id,
                   p.age_value, p.age_unit,
                   po.name AS owner_name, po.mobile AS owner_phone,
                   s.species_name, b.breed_name
            FROM appointments a
            JOIN pets p ON a.pet_id = p.pet_id
            JOIN pet_owners po ON p.pet_owner_id = po.pet_owner_id
            JOIN species s ON p.species_id = s.species_id
            JOIN breeds b ON p.breed_id = b.breed_id
            WHERE a.facility_id = ? 
            AND a.preferred_date = CURDATE()
            ORDER BY a.token_number ASC
        ");
        $stmt->execute([$facility_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getTodaysAppointments: " . $e->getMessage());
        return [];
    }
}

/**
 * Update appointment status
 */
function updateAppointmentStatus($appointment_id, $new_status, $staff_id) {
    try {
        $db = getDatabaseConnection();
        $stmt = $db->prepare("
            UPDATE appointments 
            SET status = ?, 
                updated_at = NOW()
            WHERE appointment_id = ?
        ");
        $result = $stmt->execute([$new_status, $appointment_id]);
        
        if ($result) {
            logStaffAction($staff_id, "Updated appointment $appointment_id status to $new_status");
            return true;
        }
        return false;
    } catch (PDOException $e) {
        error_log("Database error in updateAppointmentStatus: " . $e->getMessage());
        return false;
    }
}

/**
 * Get appointment details by ID
 */
function getAppointmentDetails($appointment_id) {
    try {
        $db = getDatabaseConnection();
        $stmt = $db->prepare("
            SELECT a.*, 
                   p.*, 
                   po.*,
                   s.species_name, b.breed_name,
                   f.official_name AS facility_name,
                   f.address_line1, f.address_line2
            FROM appointments a
            JOIN pets p ON a.pet_id = p.pet_id
            JOIN pet_owners po ON p.pet_owner_id = po.pet_owner_id
            JOIN species s ON p.species_id = s.species_id
            JOIN breeds b ON p.breed_id = b.breed_id
            JOIN veterinary_facilities f ON a.facility_id = f.facility_id
            WHERE a.appointment_id = ?
        ");
        $stmt->execute([$appointment_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getAppointmentDetails: " . $e->getMessage());
        return false;
    }
}

/**
 * Log staff actions for audit trail
 */
function logStaffAction($staff_id, $action) {
    try {
        $db = getDatabaseConnection();
        $stmt = $db->prepare("
            INSERT INTO staff_activity_log 
            (staff_id, action, action_time) 
            VALUES (?, ?, NOW())
        ");
        $stmt->execute([$staff_id, $action]);
        return true;
    } catch (PDOException $e) {
        error_log("Database error in logStaffAction: " . $e->getMessage());
        return false;
    }
}

/**
 * Get pet medical history
 */
function getPetMedicalHistory($pet_id) {
    try {
        $db = getDatabaseConnection();
        $stmt = $db->prepare("
            SELECT m.*, 
                   s.full_name AS staff_name,
                   a.appointment_type,
                   a.preferred_date AS appointment_date,
                   fs.full_name AS attending_staff_name
            FROM medical_records m
            LEFT JOIN facility_staff s ON m.staff_id = s.staff_id
            LEFT JOIN appointments a ON m.appointment_id = a.appointment_id
            LEFT JOIN facility_staff fs ON m.attending_staff_id = fs.staff_id
            WHERE m.pet_id = ?
            ORDER BY m.record_date DESC
        ");
        $stmt->execute([$pet_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getPetMedicalHistory: " . $e->getMessage());
        return [];
    }
}

/**
 * Add medical record for a pet
 */
function addMedicalRecord($pet_id, $staff_id, $record_data) {
    try {
        $db = getDatabaseConnection();
        $stmt = $db->prepare("
            INSERT INTO medical_records 
            (pet_id, staff_id, attending_staff_id, diagnosis, treatment, 
             medications, notes, record_date, record_type, appointment_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, CURDATE(), ?, ?)
        ");
        $result = $stmt->execute([
            $pet_id,
            $staff_id,
            $record_data['attending_staff'] ?? null,
            $record_data['diagnosis'] ?? null,
            $record_data['treatment'] ?? null,
            $record_data['medications'] ?? null,
            $record_data['notes'] ?? null,
            $record_data['record_type'] ?? 'general',
            $record_data['appointment_id'] ?? null
        ]);
        if ($result) {
            logStaffAction($staff_id, "Added medical record for pet $pet_id");
            return $db->lastInsertId();
        }
        return false;
    } catch (PDOException $e) {
        error_log("Database error in addMedicalRecord: " . $e->getMessage());
        return false;
    }
}

// Modify createMedicalRecordFromAppointment function
function createMedicalRecordFromAppointment($appointment_id, $staff_id) {
    try {
        $db = getDatabaseConnection();
        $db->beginTransaction();
        
        // Get appointment details with lock to prevent concurrent updates
        $stmt = $db->prepare("
            SELECT a.*, p.pet_id 
            FROM appointments a
            JOIN pets p ON a.pet_id = p.pet_id
            WHERE a.appointment_id = ? 
            FOR UPDATE
        ");
        $stmt->execute([$appointment_id]);
        $appointment = $stmt->fetch();
        
        if (!$appointment) {
            $db->rollBack();
            return false;
        }
        
        // Insert medical record
        $stmt = $db->prepare("
            INSERT INTO medical_records 
            (pet_id, staff_id, attending_staff_id, diagnosis, treatment, 
             medications, notes, record_date, record_type, appointment_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, CURDATE(), ?, ?)
        ");
        
        $result = $stmt->execute([
            $appointment['pet_id'],
            $staff_id,
            $staff_id, // Attending staff is the one completing the appointment
            $appointment['symptoms'],
            $appointment['additional_notes'],
            NULL, // Medications can be added later
            "Auto-generated from appointment #{$appointment_id}",
            $appointment['appointment_type'], // Get type from appointment
            $appointment_id
        ]);
        
         if ($result) {
            // Update appointment to mark it as having a medical record
            $db->prepare("UPDATE appointments SET status = 'Completed' WHERE appointment_id = ?")
               ->execute([$appointment_id]);
            
            $db->commit();
            logStaffAction($staff_id, "Created medical record from appointment #{$appointment_id}");
            return $db->lastInsertId();
        }
        
        $db->rollBack();
        return false;
    } catch (PDOException $e) {
        $db->rollBack();
        error_log("Database error in createMedicalRecordFromAppointment: " . $e->getMessage());
        return false;
    }
    }
function getRecentEmergencyReports($facility_id) {
    try {
        $db = getDatabaseConnection(); // Use the connection function
        
        if (!$db) {
            throw new Exception("Database connection failed");
        }

        $query = "SELECT * FROM emergency_reports 
                 WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                 ORDER BY created_at DESC 
                 LIMIT 10";
        
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Error fetching emergency reports: " . $e->getMessage());
        return []; // Return empty array on error
    } catch (Exception $e) {
        error_log("General error: " . $e->getMessage());
        return [];
    }

}

