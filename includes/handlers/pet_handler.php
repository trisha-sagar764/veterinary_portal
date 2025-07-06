<?php

function handlePetFormSubmission($ownerId) {
    $errors = [];
    $formData = [
        'pet_name' => trim($_POST['name'] ?? ''),  // Changed from 'name' to 'pet_name'
        'species_id' => trim($_POST['species_id'] ?? ''),
        'breed_id' => trim($_POST['breed_id'] ?? ''),
        'sex' => in_array($_POST['sex'], ['Male', 'Female', 'Unknown']) ? $_POST['sex'] : 'Unknown',
        'neutered' => isset($_POST['neutered']) ? 1 : 0,
        'age_value' => trim($_POST['age_value'] ?? ''),
        'age_unit' => in_array($_POST['age_unit'], ['days', 'months', 'years']) ? $_POST['age_unit'] : 'years',
        'date_of_birth' => trim($_POST['date_of_birth'] ?? ''),
        'color' => trim($_POST['color'] ?? ''),
        'weight' => trim($_POST['weight'] ?? ''),
        'identification_mark' => trim($_POST['identification_mark'] ?? '')
    ];

    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'])) {
        $errors[] = "Invalid CSRF token. Please try again.";
    }

    // Validate required fields
    if (empty($formData['pet_name'])) $errors[] = "Pet name is required.";  // Changed from 'name' to 'pet_name'
    if (empty($formData['species_id'])) $errors[] = "Species is required.";
    if (empty($formData['breed_id'])) $errors[] = "Breed is required.";
    if (empty($formData['age_value']) || !is_numeric($formData['age_value']) || $formData['age_value'] <= 0) {
        $errors[] = "Valid age is required.";
    }

    // Handle file upload
    $profilePicture = 'assets/images/default-pet.jpg';
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = handlePetImageUpload($_FILES['profile_picture']);
        if ($uploadResult['success']) {
            $profilePicture = $uploadResult['path'];
        } else {
            $errors[] = $uploadResult['error'];
        }
    }

    // If no errors, insert into database
    if (empty($errors)) {
        try {
            $db = getDatabaseConnection();
            $db->beginTransaction();
            
            $petId = 'PET' . bin2hex(random_bytes(2));
            
            $stmt = $db->prepare("
                INSERT INTO pets (
                    pet_id, pet_owner_id, species_id, breed_id, pet_name, sex, neutered, 
                    age_value, age_unit, date_of_birth, color, weight, 
                    identification_mark, profile_picture
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $petId,
                $ownerId,
                $formData['species_id'],
                $formData['breed_id'],
                $formData['pet_name'],  // Changed from 'name' to 'pet_name'
                $formData['sex'],
                $formData['neutered'],
                $formData['age_value'],
                $formData['age_unit'],
                !empty($formData['date_of_birth']) ? $formData['date_of_birth'] : null,
                !empty($formData['color']) ? $formData['color'] : null,
                !empty($formData['weight']) ? $formData['weight'] : null,
                !empty($formData['identification_mark']) ? $formData['identification_mark'] : null,
                $profilePicture
            ]);
            
            $db->commit();
            return ['success' => true, 'pet_id' => $petId];
            
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Pet insertion error: " . $e->getMessage());
            $errors[] = "Failed to add pet. Please try again.";
        }
    }

    return ['success' => false, 'errors' => $errors, 'formData' => $formData];
}

function handlePetImageUpload($file) {
    $uploadDir = __DIR__ . '/assets/uploads/pets/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = uniqid('pet_') . '.' . strtolower($fileExt);
    $targetPath = $uploadDir . $fileName;
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $fileType = mime_content_type($file['tmp_name']);
    
    if (!in_array($fileType, $allowedTypes)) {
        return ['success' => false, 'error' => "Invalid file type. Only JPG, PNG, and GIF are allowed."];
    }
    
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => false, 'error' => "Failed to upload profile picture."];
    }
    
    return ['success' => true, 'path' => 'assets/uploads/pets/' . $fileName];
}