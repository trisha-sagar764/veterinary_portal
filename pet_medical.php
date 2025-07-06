<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/staff_functions.php';

// Start session and validate staff login
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (!isset($_SESSION['staff_logged_in']) || !$_SESSION['staff_logged_in']) {
    header('Location: staff_login.php');
    exit;
}

// Check if pet_id is provided
if (empty($_GET['pet_id'])) {
    header('Location: staff_dashboard.php?error=no_pet_id');
    exit;
}

$pet_id = $_GET['pet_id'];
$staff_id = $_SESSION['staff_id'];
$facility_id = $_SESSION['facility_id'];

// Handle form submission for new medical record
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_record'])) {
    $record_data = [
        'diagnosis' => trim($_POST['diagnosis'] ?? ''),
        'treatment' => trim($_POST['treatment'] ?? ''),
        'medications' => trim($_POST['medications'] ?? ''),
        'notes' => trim($_POST['notes'] ?? ''),
        'attending_staff' => $_POST['attending_staff'] ?? null,
        'appointment_id' => $_GET['appointment_id'] ?? null
    ];
    if ($record_data['record_type'] !== 'vaccination' && 
        empty($record_data['diagnosis']) && 
        empty($record_data['appointment_id'])) {
        $_SESSION['error_message'] = "Diagnosis is required for non-vaccination records.";
        header("Location: pet_medical.php?pet_id=$pet_id");
        exit;
    }
    if (!empty($record_data['appointment_id'])) {
        try {
            $stmt = $db->prepare("SELECT appointment_type FROM appointments WHERE appointment_id = ?");
            $stmt->execute([$record_data['appointment_id']]);
            $appointment_type = $stmt->fetchColumn();
            $record_data['record_type'] = strtolower($appointment_type);
        } catch (PDOException $e) {
            error_log("Failed to fetch appointment type: " . $e->getMessage());
        }
    } else {
        $record_data['record_type'] = $_POST['record_type'] ?? 'general';
    }
     // Validation - diagnosis only required for non-vaccination records
    if ($record_data['record_type'] !== 'vaccination' && empty($record_data['diagnosis'])) {
        $_SESSION['error_message'] = "Diagnosis is required for non-vaccination records.";
        
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Diagnosis is required']);
            exit;
        }
        
        header("Location: pet_medical.php?pet_id=$pet_id");
        exit;
    }
    
    try {
        $db = getDatabaseConnection();
        
        // Begin transaction
        $db->beginTransaction();
        
        // Add medical record
        $stmt = $db->prepare("
            INSERT INTO medical_records 
            (pet_id, staff_id, attending_staff_id, diagnosis, treatment, medications, notes, record_date, record_type)
            VALUES (?, ?, ?, ?, ?, ?, ?, CURDATE(), ?)
        ");
        $stmt->execute([
            $pet_id,
            $staff_id,
            $record_data['attending_staff'],
            $record_data['diagnosis'],
            $record_data['treatment'],
            $record_data['medications'],
            $record_data['notes'],
            $record_data['record_type']
        ]);
            
            // Update last modified timestamp
            $db->prepare("UPDATE pets SET last_medical_update = NOW() WHERE pet_id = ?")
               ->execute([$pet_id]);
            
            // Commit transaction
            $db->commit();
            
            $_SESSION['success_message'] = "Medical record added successfully!";
            
            // If AJAX request, return JSON response
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                exit;
            }
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Medical record error: " . $e->getMessage());
            $_SESSION['error_message'] = "Failed to add medical record.";
            
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Database error']);
                exit;
            }
        }
    
    }
    
// Get pet details and medical history
try {
    $db = getDatabaseConnection();
    
    // Get pet details
    $stmt = $db->prepare("
        SELECT p.*, s.species_name, b.breed_name, p.age_unit,p.age_value, p.weight, po.name AS owner_name
        FROM pets p
        JOIN species s ON p.species_id = s.species_id
        JOIN breeds b ON p.breed_id = b.breed_id
        JOIN pet_owners po ON p.pet_owner_id = po.pet_owner_id
        WHERE p.pet_id = ?
    ");
    $stmt->execute([$pet_id]);
    $pet = $stmt->fetch();
    
    if (!$pet) {
        header('Location: staff_dashboard.php?error=pet_not_found');
        exit;
    }
    
    // Get medical staff for dropdown
    $staff_stmt = $db->prepare("
        SELECT s.staff_id, s.full_name, r.role_name
        FROM facility_staff s
        JOIN staff_roles r ON s.role_id = r.role_id
        WHERE s.facility_id = ? 
        AND r.role_name IN ('Veterinarian', 'Senior Veterinarian', 'Resident Veterinarian', 'Veterinary Surgeon', 'Veterinary Technician', 'Laboratory Technician')
        AND s.is_active = 1
        ORDER BY r.permission_level DESC, s.full_name ASC
    ");
    $staff_stmt->execute([$facility_id]);
    $medical_staff = $staff_stmt->fetchAll();
    
    // Get medical history
    $medical_history = $db->prepare("
    SELECT mr.*, fs.full_name as staff_name
    FROM medical_records mr
    LEFT JOIN facility_staff fs ON mr.staff_id = fs.staff_id
    WHERE mr.pet_id = ?
    ORDER BY mr.record_date DESC
");
$medical_history->execute([$pet_id]);
$medical_history = $medical_history->fetchAll();
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    header('Location: staff_dashboard.php?error=db_error');
    exit;

}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/staff_sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="bi bi-clipboard2-pulse"></i> Medical Records - <?= htmlspecialchars($pet['pet_name']) ?>
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="staff_dashboard.php" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>

            <!-- Messages -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success"><?= $_SESSION['success_message'] ?></div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['error_message'] ?></div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <!-- Pet Information Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Pet Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> <?= htmlspecialchars($pet['pet_name']) ?></p>
                            <p><strong>Species:</strong> <?= htmlspecialchars($pet['species_name']) ?></p>
                            <p><strong>Breed:</strong> <?= htmlspecialchars($pet['breed_name']) ?></p>
                            <p><strong>Age:</strong> <?= htmlspecialchars($pet['age_value'] . ' ' . $pet['age_unit']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Owner:</strong> <?= htmlspecialchars($pet['owner_name']) ?></p>
                            <p><strong>Sex:</strong> <?= htmlspecialchars($pet['sex']) ?></p>
                            <p><strong>Weight:</strong> <?= htmlspecialchars($pet['weight']) ?> kg</p>
                            <p><strong>Neutered:</strong> <?= $pet['neutered'] ? 'Yes' : 'No' ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add New Medical Record Form -->
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Add New Medical Record</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="pet_medical.php?pet_id=<?= htmlspecialchars($pet_id) ?>" id="medicalRecordForm">
            <div class="row mb-3">
                <div class="col-md-6">
    <label for="record_type" class="form-label">Record Type *</label>
    <select class="form-select" id="record_type" name="record_type" required <?= isset($_GET['appointment_id']) ? 'disabled' : '' ?>>
        <?php if (isset($_GET['appointment_id'])): ?>
            <?php
            // Fetch appointment type from database
            try {
                $stmt = $db->prepare("SELECT appointment_type FROM appointments WHERE appointment_id = ?");
                $stmt->execute([$_GET['appointment_id']]);
                $appointment_type = strtolower($stmt->fetchColumn());
            } catch (PDOException $e) {
                error_log("Error fetching appointment type: " . $e->getMessage());
                $appointment_type = 'general';
            }
            ?>
            <option value="<?= $appointment_type ?>" selected>
                <?= ucfirst($appointment_type) ?> (from appointment)
            </option>
            <input type="hidden" name="record_type" value="<?= $appointment_type ?>">
        <?php else: ?>
            <!-- Regular options when not linked to appointment -->
            <option value="">-- Select Type --</option>
            <option value="general" <?= ($_POST['record_type'] ?? '') == 'general' ? 'selected' : '' ?>>General Consultation</option>
            <option value="vaccination" <?= ($_POST['record_type'] ?? '') == 'vaccination' ? 'selected' : '' ?>>Vaccination</option>
            <option value="surgery" <?= ($_POST['record_type'] ?? '') == 'surgery' ? 'selected' : '' ?>>Surgical Procedure</option>
            <option value="followup" <?= ($_POST['record_type'] ?? '') == 'followup' ? 'selected' : '' ?>>Follow-up Visit</option>
            <option value="emergency" <?= ($_POST['record_type'] ?? '') == 'emergency' ? 'selected' : '' ?>>Emergency Treatment</option>
        <?php endif; ?>
    </select>
</div>
                <div class="col-md-6">
                    <label for="attending_staff" class="form-label">Attending Professional *</label>
                    <select class="form-select" id="attending_staff" name="attending_staff" required>
                        <option value="">Select a professional</option>
                        <?php foreach ($medical_staff as $staff): ?>
                            <option value="<?= $staff['staff_id'] ?>">
                                <?= htmlspecialchars($staff['full_name']) ?> (<?= $staff['role_name'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="mb-3" id="diagnosisField">
                <label for="diagnosis" class="form-label">Diagnosis *</label>
                <input type="text" class="form-control" id="diagnosis" name="diagnosis">
            </div>
            
            <div class="mb-3">
                <label for="treatment" class="form-label">Treatment/Procedure *</label>
                <textarea class="form-control" id="treatment" name="treatment" rows="2" required></textarea>
            </div>
            
            <div class="mb-3" id="medicationsField">
                <label for="medications" class="form-label">Medications Prescribed</label>
                <textarea class="form-control" id="medications" name="medications" rows="2"></textarea>
            </div>
            
            <div class="mb-3">
                <label for="notes" class="form-label">Additional Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
            </div>
            
            <button type="submit" name="add_record" class="btn btn-primary">
                <i class="bi bi-save"></i> Save Record
            </button>
        </form>
    </div>
</div>
<td>
    <?php if (!empty($record['appointment_type'])): ?>
        <small class="text-muted">
            <?= htmlspecialchars(ucfirst($record['appointment_type'])) ?>
            <?php if (!empty($record['appointment_date'])): ?>
                (<?= date('M j, Y', strtotime($record['appointment_date'])) ?>)
            <?php endif; ?>
        </small>
    <?php endif; ?>
</td>
<td>
    <?php if (!empty($record['attending_staff_name'])): ?>
        <small class="text-muted">
            Attended by: <?= htmlspecialchars($record['attending_staff_name']) ?>
        </small>
    <?php endif; ?>
</td>       
            <!-- Space before footer -->
            <div style="margin-bottom: 100px;"></div>
        </main>
    </div>
</div>
<script>

 document.getElementById('record_type').addEventListener('change', function() {
    const recordType = this.value;
    const diagnosisField = document.getElementById('diagnosisField');
    const medicationsField = document.getElementById('medicationsField');
    
    if (recordType === 'vaccination') {
        diagnosisField.style.display = 'none';
        medicationsField.style.display = 'none';
        document.getElementById('diagnosis').required = false;
    } else {
        diagnosisField.style.display = 'block';
        medicationsField.style.display = 'block';
        document.getElementById('diagnosis').required = true;
    }
});

// AJAX form submission
document.getElementById('medicalRecordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Refresh medical records
            loadMedicalRecords();
            // Show success message
            const alert = document.createElement('div');
            alert.className = 'alert alert-success';
            alert.innerHTML = 'Medical record added successfully!';
            document.querySelector('main').prepend(alert);
            // Clear form
            form.reset();
            // Remove alert after 5 seconds
            setTimeout(() => alert.remove(), 5000);
        } else {
            alert(data.error || 'Error adding record');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
});

// Load medical records via AJAX
function loadMedicalRecords() {
    fetch(`api/get_medical_records.php?pet_id=<?= $pet_id ?>`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('medical-records-container').innerHTML = html;
        });
}

// Check for updates every 30 seconds
setInterval(loadMedicalRecords, 30000);
document.addEventListener('DOMContentLoaded', function() {
    const recordType = document.getElementById('record_type');
    const diagnosisField = document.getElementById('diagnosisField');
    const medicationsField = document.getElementById('medicationsField');
    
    function toggleFields() {
        if (recordType.value === 'vaccination') {
            diagnosisField.style.display = 'none';
            medicationsField.style.display = 'none';
            document.getElementById('diagnosis').required = false;
        } else {
            diagnosisField.style.display = 'block';
            medicationsField.style.display = 'block';
            document.getElementById('diagnosis').required = true;
        }
    }
    
    // Initial toggle
    toggleFields();
    
    // Toggle on change (for manual records)
    if (!recordType.disabled) {
        recordType.addEventListener('change', toggleFields);
    }
});

</script>

<?php include 'includes/footer.php'; ?>