<?php
// includes/forms/pet_form.php
?>
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Basic Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Pet Name *</label>
                        <input type="text" class="form-control" id="name" name="pet_name" 
                               value="<?= htmlspecialchars($formData['pet_name']) ?>" required>
                        <div class="invalid-feedback">
                            Please provide a pet name.
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="species_id" class="form-label">Species *</label>
                        <select class="form-select" id="species_id" name="species_id" required>
                            <option value="">Select Species</option>
                            <?php foreach ($species as $specie): ?>
                                <option value="<?= htmlspecialchars($specie['species_id']) ?>" 
                                    <?= $formData['species_id'] === $specie['species_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($specie['species_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">
                            Please select a species.
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="breed_id" class="form-label">Breed *</label>
                        <select class="form-select" id="breed_id" name="breed_id" required>
                            <option value="">Select Breed</option>
                            <?php foreach ($breeds as $breed): ?>
                                <option value="<?= htmlspecialchars($breed['breed_id']) ?>" 
                                    <?= $formData['breed_id'] === $breed['breed_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($breed['breed_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">
                            Please select a breed.
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="sex" class="form-label">Sex *</label>
                        <select class="form-select" id="sex" name="sex" required>
                            <option value="Male" <?= $formData['sex'] === 'Male' ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= $formData['sex'] === 'Female' ? 'selected' : '' ?>>Female</option>
                            <option value="Unknown" <?= $formData['sex'] === 'Unknown' ? 'selected' : '' ?>>Unknown</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-check mt-4 pt-2">
                            <input class="form-check-input" type="checkbox" id="neutered" name="neutered" 
                                   value="1" <?= $formData['neutered'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="neutered">
                                Neutered/Spayed
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="age_value" class="form-label">Age *</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="age_value" name="age_value" 
                                   value="<?= htmlspecialchars($formData['age_value']) ?>" min="1" required>
                            <select class="form-select" id="age_unit" name="age_unit">
                                <option value="days" <?= $formData['age_unit'] === 'days' ? 'selected' : '' ?>>Days</option>
                                <option value="months" <?= $formData['age_unit'] === 'months' ? 'selected' : '' ?>>Months</option>
                                <option value="years" <?= $formData['age_unit'] === 'years' ? 'selected' : '' ?>>Years</option>
                            </select>
                        </div>
                        <div class="invalid-feedback">
                            Please provide a valid age.
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="date_of_birth" class="form-label">Date of Birth (Optional)</label>
                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                               value="<?= htmlspecialchars($formData['date_of_birth']) ?>">
                    </div>
                    
                    <div class="col-md-6">
                        <label for="color" class="form-label">Color (Optional)</label>
                        <input type="text" class="form-control" id="color" name="color" 
                               value="<?= htmlspecialchars($formData['color']) ?>">
                    </div>
                    
                    <div class="col-md-6">
                        <label for="weight" class="form-label">Weight (kg) (Optional)</label>
                        <input type="number" step="0.01" class="form-control" id="weight" name="weight" 
                               value="<?= htmlspecialchars($formData['weight']) ?>">
                    </div>
                    
                    <div class="col-12">
                        <label for="identification_mark" class="form-label">Identification Mark (Optional)</label>
                        <textarea class="form-control" id="identification_mark" name="identification_mark" 
                                  rows="2"><?= htmlspecialchars($formData['identification_mark']) ?></textarea>
                        <small class="text-muted">Distinctive physical features or markings</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Profile Picture</h5>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <img id="profile-picture-preview" src="assets/images/default-pet.jpg" 
                         class="img-thumbnail" style="max-height: 200px;">
                </div>
                <div class="mb-3">
                    <label for="profile_picture" class="form-label">Upload Photo</label>
                    <input class="form-control" type="file" id="profile_picture" name="profile_picture" 
                           accept="image/jpeg, image/png, image/gif">
                </div>
                <small class="text-muted">Max size: 2MB. Formats: JPG, PNG, GIF.</small>
            </div>
        </div>
        
        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-save"></i> Save Pet
            </button>
            <a href="my_pets.php" class="btn btn-outline-secondary">
                Cancel
            </a>
        </div>
    </div>
</div>