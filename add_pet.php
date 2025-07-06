<?php
// add_pet.php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/csrf.php';

// Start session and validate user
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/includes/auth/validate_session.php';

// Get pet owner data
$petOwner = getPetOwnerByPetOwnerId($_SESSION['pet_owner_id']);
if (!$petOwner) {
    session_unset();
    session_destroy();
    header('Location: login.php?reason=invalid_user');
    exit;
}

// Process form submission if POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/includes/handlers/pet_handler.php';
    $result = handlePetFormSubmission($_SESSION['pet_owner_id']);
    
    if ($result['success']) {
        $_SESSION['success_message'] = "Pet added successfully!";
        header("Location: pet_details.php?id=" . $result['pet_id']);
        exit;
    }
    
    $errors = $result['errors'];
    $formData = $result['formData'];
} else {
    $errors = [];
    $formData = [
        'pet_name' => '',
        'species_id' => '',
        'breed_id' => '',
        'sex' => 'Male',
        'neutered' => 0,
        'age_value' => '',
        'age_unit' => 'years',
        'date_of_birth' => '',
        'color' => '',
        'weight' => '',
        'identification_mark' => ''
    ];
}

// Get species for dropdown
try {
    $db = getDatabaseConnection();
    $species = $db->query("SELECT species_id, species_name FROM species ORDER BY species_name")->fetchAll();
    $breeds = [];
    
    if (!empty($formData['species_id'])) {
        $breeds = $db->prepare("SELECT breed_id, breed_name FROM breeds WHERE species_id = ? ORDER BY breed_name")
                    ->execute([$formData['species_id']])
                    ->fetchAll();
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $errors[] = "Database error. Please try again later.";
}

// Include header and display form
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Add New Pet</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="my_pets.php" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to My Pets
                    </a>
                </div>
            </div>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <?php include 'includes/forms/pet_form.php'; ?>
            </form>
        </main>
    </div>
</div>

<?php 
include 'includes/partials/pet_form_js.php';
include 'includes/footer.php'; 
?>