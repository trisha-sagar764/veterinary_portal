<?php
function validatePetData($data) {
    $errors = [];

    if (empty($data['name'])) {
        $errors[] = "Pet name is required.";
    } elseif (strlen($data['name']) > 100) {
        $errors[] = "Pet name must be 100 characters or less.";
    }

    if (empty($data['species_id'])) {
        $errors[] = "Species is required.";
    }

    if (empty($data['breed_id'])) {
        $errors[] = "Breed is required.";
    }

    if (empty($data['sex']) || !in_array($data['sex'], ['Male', 'Female', 'Unknown'])) {
        $errors[] = "Valid sex selection is required.";
    }

    if (empty($data['age_value']) || !is_numeric($data['age_value']) || $data['age_value'] <= 0) {
        $errors[] = "Valid age is required.";
    }

    if (!empty($data['date_of_birth']) && !strtotime($data['date_of_birth'])) {
        $errors[] = "Invalid date of birth format.";
    }

    if (!empty($data['weight']) && (!is_numeric($data['weight']) || $data['weight'] <= 0)) {
        $errors[] = "Weight must be a positive number.";
    }

    return $errors;
}
?>