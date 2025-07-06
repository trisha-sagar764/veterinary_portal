<?php
function displayPetDetailsPage($petData) {
    ?>
    <div class="container-fluid">
        <div class="row">
            <?php include __DIR__ . '/../sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <?php displayPetPageHeader($petData['pet']); ?>
                
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">
                        <?= getSuccessMessage($_GET['success']) ?>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-4 mb-4">
                        <?php include __DIR__ . '/../partials/pet_profile_card.php'; ?>
                        
                        <?php if ($petData['pet']['identification_mark']): ?>
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h6>Identification Marks</h6>
                                </div>
                                <div class="card-body">
                                    <p class="card-text"><?= nl2br(htmlspecialchars($petData['pet']['identification_mark'])) ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Right Column -->
                    <div class="col-md-8">
                        <?php include __DIR__ . '/../partials/pet_appointments.php'; ?>
                        <?php include __DIR__ . '/../partials/pet_vaccinations.php'; ?>
                        <?php include __DIR__ . '/../partials/pet_medical_records.php'; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <?php
}

function displayPetPageHeader($pet) {
    ?>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Pet Details: <?= htmlspecialchars($pet['name']) ?></h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="edit_pet.php?id=<?= $pet['pet_id'] ?>" class="btn btn-sm btn-outline-primary me-2">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="my_pets.php" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to My Pets
            </a>
        </div>
    </div>
    <?php
}

function getSuccessMessage($type) {
    switch ($type) {
        case 'added': return 'Pet added successfully!';
        case 'updated': return 'Pet updated successfully!';
        default: return 'Operation completed successfully!';
    }
}