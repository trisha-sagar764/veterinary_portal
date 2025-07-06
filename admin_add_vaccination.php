<?php
session_start();

// Database configuration
define('DB_HOST', 'localhost:3307');
define('DB_NAME', 'veterinary_portal');
define('DB_USER', 'root');
define('DB_PASS', '');

// Initialize variables
$errors = [];
$success = '';
$formData = [
    'vaccine_name' => '',
    'vaccine_type' => 'new',
    'description' => '',
    'species' => [],
    'initial_dose_time' => '',
    'initial_dose_unit' => 'days',
    'has_additional_doses' => 0,
    'dose_details' => [],
    'available_hospitals' => []
];

try {
    $db = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if vaccines table exists, if not create it
    $db->exec("CREATE TABLE IF NOT EXISTS vaccines (
        vaccine_id VARCHAR(20) PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        type ENUM('new', 'variation') NOT NULL,
        description TEXT,
        species JSON NOT NULL,
        initial_dose_time INT NOT NULL,
        initial_dose_unit ENUM('days', 'weeks', 'months', 'years') NOT NULL,
        has_additional_doses BOOLEAN NOT NULL DEFAULT 0,
        dose_details JSON,
        available_hospitals JSON,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Fetch hospitals for dropdown
    $hospitals = $db->query("SELECT facility_id, official_name FROM veterinary_facilities WHERE is_active = 1 ORDER BY official_name")->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $errors[] = "Database error: " . $e->getMessage();
    error_log("Database error: " . $e->getMessage());
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic vaccine info
    $formData['vaccine_name'] = trim($_POST['vaccine_name'] ?? '');
    $formData['vaccine_type'] = $_POST['vaccine_type'] ?? 'new';
    $formData['description'] = trim($_POST['description'] ?? '');
    $formData['species'] = $_POST['species'] ?? [];
    $formData['initial_dose_time'] = intval($_POST['initial_dose_time'] ?? 0);
    $formData['initial_dose_unit'] = $_POST['initial_dose_unit'] ?? 'days';
    $formData['has_additional_doses'] = isset($_POST['has_additional_doses']) ? 1 : 0;
    $formData['available_hospitals'] = $_POST['available_hospital'] ?? '';
    
    // Validation
    if (empty($formData['vaccine_name'])) {
        $errors[] = "Vaccine name is required";
    }
    
    if (empty($formData['species'])) {
        $errors[] = "At least one species must be selected";
    }
    
    if ($formData['initial_dose_time'] <= 0) {
        $errors[] = "Initial dose time must be greater than 0";
    }
    
    // Process dose details if additional doses exist
    if ($formData['has_additional_doses']) {
        $doseCount = 0;
        $i = 1;
        while (isset($_POST['dose_time_'.$i])) {
            $doseTime = intval($_POST['dose_time_'.$i] ?? 0);
            $doseUnit = $_POST['dose_unit_'.$i] ?? 'days';
            
            if ($doseTime > 0) {
                $formData['dose_details'][$i] = [
                    'time' => $doseTime,
                    'unit' => $doseUnit,
                    'description' => trim($_POST['dose_description_'.$i] ?? '')
                ];
                $doseCount++;
            }
            $i++;
        }
        
        if ($formData['has_additional_doses'] && $doseCount == 0) {
            $errors[] = "Please add at least one additional dose";
        }
    }
    
    // If no errors, proceed to save
    if (empty($errors)) {
        try {
            // Generate vaccine ID
            $vaccine_id = 'VAC-' . strtoupper(substr(uniqid(), -8));
            
            // Prepare data for database
            $speciesJson = json_encode($formData['species']);
            $doseDetailsJson = $formData['has_additional_doses'] ? json_encode($formData['dose_details']) : json_encode([]);
            $hospitalsJson = json_encode([$formData['available_hospitals']]); 
            
            // Insert into database
            $stmt = $db->prepare("INSERT INTO vaccines (
                vaccine_id, name, type, description, species, initial_dose_time, initial_dose_unit,
                has_additional_doses, dose_details, available_hospitals
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $vaccine_id,
                $formData['vaccine_name'],
                $formData['vaccine_type'],
                $formData['description'],
                $speciesJson,
                $formData['initial_dose_time'],
                $formData['initial_dose_unit'],
                $formData['has_additional_doses'],
                $doseDetailsJson,
                $hospitalsJson
            ]);
            
            $_SESSION['success_message'] = "Vaccine added successfully! Vaccine ID: $vaccine_id";
            header("Location: admin_vaccine_success.php?id=$vaccine_id");
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
            error_log("Database error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Vaccine | Department of Animal Husbandry & Veterinary Services</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Bootstrap Select CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.18/dist/css/bootstrap-select.min.css">
    <style>
        :root {
            --govt-blue: #0066b3;
            --govt-gold: #ffcc00;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 16px;
        }
        
        .header {
            background-color: var(--govt-blue);
            color: white;
            padding: 8px 0;
            border-bottom: 4px solid var(--govt-gold);
        }
        
        .logo-section {
            background: white;
            padding: 15px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .nav-main {
            background-color: var(--govt-blue);
        }
        
        .nav-main .nav-link {
            color: white !important;
            font-weight: 500;
            padding: 10px 20px;
        }
        
        .nav-main .nav-link:hover {
            background-color: #004f8a;
        }
        
        .govt-seal {
            max-height: 80px;
        }
        
        .footer {
            background-color: var(--govt-blue);
            color: white;
            padding: 30px 0 10px;
            margin-top: 30px;
        }
        
        .form-box {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin: 20px auto;
            max-width: 800px;
        }
        
        .required-field::after {
            content: " *";
            color: red;
        }
        
        .form-section {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }
        
        .dose-details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            position: relative;
        }
        
        .remove-dose {
            position: absolute;
            right: 15px;
            bottom: 15px;
        }
        
        /* Bootstrap Select customization */
        .bootstrap-select .dropdown-toggle {
            border: 1px solid #ced4da;
            padding: 0.375rem 0.75rem;
        }
        
        .bootstrap-select.show .dropdown-toggle {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        
        .bootstrap-select .dropdown-menu {
            border-radius: 0.375rem;
        }
    </style>
</head>
<body>
    <!-- Accessibility Bar -->
    <div class="accessibility-bar bg-light py-1">
        <div class="container">
            <div class="d-flex justify-content-end align-items-center gap-3">
                <a href="#main-content" class="text-dark text-decoration-none">SKIP TO MAIN CONTENT</a>
                <span class="text-dark">|</span>
                <a href="#" class="text-dark text-decoration-none">ENGLISH</a>
                <span class="text-dark">|</span>
                <span class="text-dark">Font Size:</span>
                <a href="#" class="text-dark text-decoration-none" onclick="increaseFontSize()">A+</a>
                <a href="#" class="text-dark text-decoration-none" onclick="normalFontSize()">A</a>
                <a href="#" class="text-dark text-decoration-none" onclick="decreaseFontSize()">A-</a>
            </div>
        </div>
    </div>
    
    <!-- Top Header Strip -->
    <div class="header">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <marquee behavior="scroll" direction="left" scrollamount="3">
                        Department of Animal Husbandry & Veterinary Services | Andaman & Nicobar Administration
                    </marquee>
                </div>
                <div class="col-md-6 text-end">
                    <a href="#" class="text-white me-3"><i class="bi bi-telephone"></i> Helpline: 03192-238881</a>
                    <a href="#" class="text-white"><i class="bi bi-envelope"></i> dir-ah[at]and[dot]nic[dot]in</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Logo and Government Identity Section -->
    <div class="logo-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-2 text-center">
                    <img src="https://ahvs.andaman.gov.in/img/logo.png" alt="Government Emblem" class="govt-seal">
                </div>
                <div class="col-md-8 text-center">
                    <h3 style="color: var(--govt-blue); margin-bottom: 0;">डेयरी एवं पशुपालन विभाग</h3>
                    <h3 style="color: var(--govt-blue); margin-bottom: 0;">Department of Animal Husbandry & Veterinary Services</h3>
                    <h4 style="color: var(--govt-blue);">Andaman & Nicobar Administration</h4>
                </div>
                <div class="col-md-2 text-center">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/8/8b/Seal_of_Andaman_and_Nicobar_Islands.svg" alt="Andaman Logo" class="govt-seal">
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="navbar navbar-expand-lg nav-main">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="admin.php"><i class="bi bi-house-door"></i> Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php"><i class="bi bi-info-circle"></i> About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_add_hospital.php"><i class="bi bi-hospital"></i> Veterinary Facilities</a></li>
                    <li class="nav-item"><a class="nav-link active" href="admin_add_vaccination.php"><i class="bi bi-eyedropper"></i> Vaccines</a></li>
                </ul>
                <div class="d-flex gap-2">
                    <a href="admin_logout.php" class="btn btn-danger btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-4" id="main-content">
        <div class="form-box">
            <h3 class="text-center mb-4" style="color: var(--govt-blue);">Add New Vaccine</h3>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <p class="mb-1"><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>
            
            <form method="post">
                <!-- Basic Vaccine Information -->
                <div class="form-section">
                    <h5 class="mb-3">Vaccine Information</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vaccine_name" class="form-label required-field">Vaccine Name</label>
                            <input type="text" class="form-control" id="vaccine_name" name="vaccine_name" 
                                   value="<?= htmlspecialchars($formData['vaccine_name']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vaccine_type" class="form-label required-field">Vaccine Type</label>
                            <select class="form-select" id="vaccine_type" name="vaccine_type" required>
                                <option value="new" <?= $formData['vaccine_type'] == 'new' ? 'selected' : '' ?>>New Vaccine</option>
                                <option value="variation" <?= $formData['vaccine_type'] == 'variation' ? 'selected' : '' ?>>Variation of Existing Vaccine</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description (What it does/improves/helps with)</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($formData['description']) ?></textarea>
                    </div>
                </div>
                
                <!-- Species Information -->
                <div class="form-section">
                    <h5 class="mb-3">Target Species</h5>
                    <div class="mb-3">
                        <label class="form-label required-field">Select applicable species:</label>
                        <select class="selectpicker form-control" name="species[]" multiple required
                                data-live-search="true" 
                                data-actions-box="true"
                                data-selected-text-format="count > 3"
                                title="Choose species...">
                            <option value="Cattle">Cattle</option>
                            <option value="Buffalo">Buffalo</option>
                            <option value="Sheep">Sheep</option>
                            <option value="Goat">Goat</option>
                            <option value="Pig">Pig</option>
                            <option value="Dog">Dog</option>
                            <option value="Cat">Cat</option>
                            <option value="Poultry">Poultry</option>
                            <option value="Horse">Horse</option>
                            <option value="Donkey">Donkey</option>
                            <option value="Rabbit">Rabbit</option>
                            <option value="Other">Other</option>
                        </select>
                        <small class="text-muted">Hold Ctrl/Cmd to select multiple species</small>
                    </div>
                </div>
                
                <!-- Initial Dose Information -->
                <div class="form-section">
                    <h5 class="mb-3">Initial Dose Information</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="initial_dose_time" class="form-label required-field">Time after birth for initial dose</label>
                            <input type="number" class="form-control" id="initial_dose_time" name="initial_dose_time" 
                                   value="<?= htmlspecialchars($formData['initial_dose_time']) ?>" min="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="initial_dose_unit" class="form-label required-field">Time unit</label>
                            <select class="form-select" id="initial_dose_unit" name="initial_dose_unit" required>
                                <option value="days" <?= $formData['initial_dose_unit'] == 'days' ? 'selected' : '' ?>>Days</option>
                                <option value="weeks" <?= $formData['initial_dose_unit'] == 'weeks' ? 'selected' : '' ?>>Weeks</option>
                                <option value="months" <?= $formData['initial_dose_unit'] == 'months' ? 'selected' : '' ?>>Months</option>
                                <option value="years" <?= $formData['initial_dose_unit'] == 'years' ? 'selected' : '' ?>>Years</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Additional Doses -->
                <div class="form-section">
                    <h5 class="mb-3">Additional Doses</h5>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="has_additional_doses" 
                               name="has_additional_doses" value="1"
                               <?= $formData['has_additional_doses'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="has_additional_doses">
                            This vaccine requires additional doses
                        </label>
                    </div>
                    
                    <div id="additional_doses_section" style="display: <?= $formData['has_additional_doses'] ? 'block' : 'none' ?>;">
                        <div id="dose_details_container">
                            <?php if ($formData['has_additional_doses'] && !empty($formData['dose_details'])): ?>
                                <?php foreach ($formData['dose_details'] as $i => $dose): ?>
                                    <div class="dose-details mb-3" id="dose_<?= $i ?>">
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <label class="form-label">Time after previous dose</label>
                                                <input type="number" class="form-control" name="dose_time_<?= $i ?>" 
                                                       value="<?= $dose['time'] ?? '' ?>" min="1">
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label class="form-label">Time unit</label>
                                                <select class="form-select" name="dose_unit_<?= $i ?>">
                                                    <option value="days" <?= ($dose['unit'] ?? '') == 'days' ? 'selected' : '' ?>>Days</option>
                                                    <option value="weeks" <?= ($dose['unit'] ?? '') == 'weeks' ? 'selected' : '' ?>>Weeks</option>
                                                    <option value="months" <?= ($dose['unit'] ?? '') == 'months' ? 'selected' : '' ?>>Months</option>
                                                    <option value="years" <?= ($dose['unit'] ?? '') == 'years' ? 'selected' : '' ?>>Years</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label class="form-label">Description (optional)</label>
                                                <input type="text" class="form-control" name="dose_description_<?= $i ?>" 
                                                       value="<?= htmlspecialchars($dose['description'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-danger btn-sm remove-dose" data-dose="<?= $i ?>">
                                            <i class="bi bi-trash"></i> Remove
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <button type="button" id="add_dose_btn" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus"></i> Add Dose
                        </button>
                    </div>
                </div>
                
                <!-- Available Hospitals -->
                <div class="form-section">
                    <h5 class="mb-3">Availability</h5>
                    <div class="mb-3">
                        <label class="form-label">Select veterinary facility:</label>
                        <select class="form-select" name="available_hospital" required>
                            <option value="">-- Select a facility --</option>
                            <?php foreach ($hospitals as $hospital): ?>
                                <option value="<?= $hospital['facility_id'] ?>"
                                    <?= $formData['available_hospitals'] == $hospital['facility_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($hospital['official_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">Add Vaccine</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
<?php include __DIR__ . '/../includes/footer.php'; ?>
    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.18/dist/js/bootstrap-select.min.js"></script>
    
    <script>
        // Initialize all selectpickers when document is ready
        $(document).ready(function() {
            $('.selectpicker').selectpicker();
            
            // If you need to set selected values from PHP, do it like this:
            // $('.selectpicker').selectpicker('val', <?= json_encode($formData['species']) ?>);
            // $('.selectpicker').selectpicker('refresh');
            
            // Font size adjustment functions
            function increaseFontSize() {
                document.body.style.fontSize = '20px';
            }
            function normalFontSize() {
                document.body.style.fontSize = '16px';
            }
            function decreaseFontSize() {
                document.body.style.fontSize = '14px';
            }
            
            // Toggle additional doses section
            $('#has_additional_doses').change(function() {
                $('#additional_doses_section').toggle(this.checked);
                if (!this.checked) {
                    $('#dose_details_container').empty();
                }
            });
            
            let doseCounter = <?= $formData['has_additional_doses'] ? count($formData['dose_details']) : 0 ?>;
            
            // Add new dose
            $('#add_dose_btn').click(function() {
                doseCounter++;
                const newDose = `
                    <div class="dose-details mb-3" id="dose_${doseCounter}">
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Time after previous dose</label>
                                <input type="number" class="form-control" name="dose_time_${doseCounter}" min="1" required>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Time unit</label>
                                <select class="form-select" name="dose_unit_${doseCounter}" required>
                                    <option value="days">Days</option>
                                    <option value="weeks">Weeks</option>
                                    <option value="months">Months</option>
                                    <option value="years">Years</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Description (optional)</label>
                                <input type="text" class="form-control" name="dose_description_${doseCounter}">
                            </div>
                        </div>
                        <button type="button" class="btn btn-danger btn-sm remove-dose" data-dose="${doseCounter}">
                            <i class="bi bi-trash"></i> Remove
                        </button>
                    </div>
                `;
                $('#dose_details_container').append(newDose);
            });
            
            // Remove dose
            $(document).on('click', '.remove-dose', function() {
                const doseId = $(this).data('dose');
                $(`#dose_${doseId}`).remove();
            });
            
            // Form validation
            $('form').submit(function() {
                let valid = true;
                
                // Validate species selection
                if ($('select[name="species[]"]').val() === null || $('select[name="species[]"]').val().length === 0) {
                    alert('Please select at least one species');
                    valid = false;
                }
                
                // Validate initial dose time
                if ($('#initial_dose_time').val() <= 0) {
                    alert('Initial dose time must be greater than 0');
                    valid = false;
                }
                
                // Validate additional doses if checked
                if ($('#has_additional_doses').is(':checked')) {
                    if ($('#dose_details_container').children().length === 0) {
                        alert('Please add at least one additional dose');
                        valid = false;
                    }
                    
                    // Validate each dose time
                    $('#dose_details_container .dose-details').each(function() {
                        const doseTime = $(this).find('input[type="number"]').val();
                        if (!doseTime || doseTime <= 0) {
                            alert('Please enter a valid time for all doses');
                            valid = false;
                            return false; // break out of loop
                        }
                    });
                }
                
                return valid;
            });
        });
    </script>
</body>
</html>