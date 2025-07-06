<?php
// includes/partials/pet_details_content.php
// This file contains the HTML for displaying pet details
// It's included in pet_details.php when $pet exists
?>

<div class="col-md-4">
    <div class="card mb-4">
        <img src="<?= htmlspecialchars($pet['profile_picture']) ?>" 
             class="card-img-top" 
             alt="Photo of <?= htmlspecialchars($pet['pet_name']) ?>"
             onerror="this.src='assets/images/default-pet.jpg'">
        <div class="card-body text-center">
            <h3 class="card-title"><?= htmlspecialchars($pet['pet_name']) ?></h3>
            <p class="text-muted mb-2">
                <?= htmlspecialchars($pet['species_name']) ?> • 
                <?= htmlspecialchars($pet['breed_name']) ?>
            </p>
            <div class="d-flex justify-content-center gap-2 mb-3">
                <span class="badge bg-<?= $pet['sex'] === 'Male' ? 'primary' : 'danger' ?>">
                    <?= htmlspecialchars($pet['sex']) ?>
                </span>
                <?php if ($pet['neutered']): ?>
                    <span class="badge bg-success">Neutered</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Owner Information</h5>
        </div>
        <div class="card-body">
            <p class="mb-1">
                <strong>Owner:</strong> 
                <?= htmlspecialchars($pet['owner_name']) ?>
            </p>
            <p class="mb-0">
                <strong>Registered Since:</strong> 
                <?= date('M j, Y', strtotime($pet['created_at'])) ?>
            </p>
        </div>
    </div>
</div>

<div class="col-md-8">
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Basic Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Species:</strong> <?= htmlspecialchars($pet['species_name']) ?></p>
                    <p><strong>Breed:</strong> <?= htmlspecialchars($pet['breed_name']) ?></p>
                    <p><strong>Sex:</strong> 
                        <?= htmlspecialchars($pet['sex']) ?>
                        <?= $pet['neutered'] ? '(Neutered)' : '' ?>
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Age:</strong> 
                        <?= htmlspecialchars($pet['age_value'] . ' ' . $pet['age_unit']) ?>
                    </p>
                    <p><strong>Date of Birth:</strong> 
                        <?= $pet['date_of_birth'] ? date('M j, Y', strtotime($pet['date_of_birth'])) : 'Unknown' ?>
                    </p>
                    <p><strong>Weight:</strong> 
                        <?= $pet['weight'] ? htmlspecialchars($pet['weight']) . ' kg' : 'Unknown' ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Physical Characteristics</h5>
        </div>
        <div class="card-body">
            <p><strong>Color:</strong> 
                <?= $pet['color'] ? htmlspecialchars($pet['color']) : 'Not specified' ?>
            </p>
            <p><strong>Identification Mark:</strong></p>
            <p><?= $pet['identification_mark'] ? nl2br(htmlspecialchars($pet['identification_mark'])) : 'None recorded' ?></p>
        </div>
    </div>
    
    <div class="card">
<div class="col-md-12 mt-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-clipboard2-pulse"></i> Medical Records
                        <span class="badge bg-primary float-end" id="last-update-badge">Live</span>
                    </h5>
                </div>
                <div class="card-body" id="medical-records-container">
                    <!-- Records will be loaded here via AJAX -->
                    <?php include 'includes/partials/pet_medical.php'; ?>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>