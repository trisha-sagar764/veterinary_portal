<?php
session_start();

// Check admin privileges
// if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
//     header("Location: admin_login.php");
//     exit;
// }

// Database connection
$db = new mysqli('localhost', 'root', '', 'veterinary_portal', 3307);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Generate username and password
    $username = strtolower(str_replace(' ', '', $_POST['full_name'])) . rand(100, 999);
    $temp_password = substr(md5(time()), 0, 8);
    $hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);
    
    // Set default values for additional fields
    $is_active = 1; // Default to active
    $auto_generated = 1; // Since we're auto-generating credentials
    
    // Insert staff member with all fields
    $stmt = $db->prepare("INSERT INTO facility_staff (
        facility_id, role_id, username, password, full_name, email, phone, 
        is_active, auto_generated, initial_password, credentials_sent_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    
    $stmt->bind_param(
        "iisssssiis",
        $_POST['facility_id'],
        $_POST['role_id'],
        $username,
        $hashed_password,
        $_POST['full_name'],
        $_POST['email'],
        $_POST['phone'],
        $is_active,
        $auto_generated,
        $temp_password
    );
    
    if ($stmt->execute()) {
        $success_message = "Staff member registered successfully!<br>
                          <strong>Username:</strong> $username<br>
                          <strong>Temporary Password:</strong> $temp_password<br>
                          <strong>Status:</strong> Active";
    } else {
        $error_message = "Error: " . $db->error;
    }
}

include __DIR__ . '/../includes/header.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department of Animal Husbandry & Veterinary Services | A&N Administration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        /* (Keep all your existing styles from add_doc.php) */
        .role-description {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: -5px;
            margin-bottom: 10px;
        }
        .permission-badge {
            font-size: 0.7rem;
            background-color: #0074c1;
            color: white;
            padding: 2px 5px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <!-- (Keep all your existing header/navigation from add_doc.php) -->

    <!-- Main Content -->
    <div class="container-fluid mt-4" id="main-content">
        <div class="row">
            <!-- Admin Sidebar Menu -->
            <div class="col-md-3 col-lg-2">
                <div class="admin-menu" style="background-color: var(--govt-dark-blue); color: white; padding: 0;">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="admin_dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="add_staff.php"><i class="bi bi-person-plus"></i> Add Staff</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_staff.php"><i class="bi bi-people"></i> Manage Staff</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_facilities.php"><i class="bi bi-hospital"></i> Veterinary Facilities</a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Main Admin Content Area -->
            <div class="col-md-9 col-lg-10">
                <div class="admin-header">
                    <h3><i class="bi bi-person-plus"></i> Register New Staff Member</h3>
                    <p class="mb-0">Add a new staff member to the veterinary facility</p>
                </div>
                
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success">
                        <?php echo $success_message; ?>
                        <div class="mt-3">
                            <a href="add_staff.php" class="btn btn-govt me-2">Add Another Staff</a>
                            <a href="manage_staff.php" class="btn btn-outline-secondary">View All Staff</a>
                        </div>
                    </div>
                <?php else: ?>
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    
                    <div class="card admin-card">
                        <div class="card-header" style="background-color: var(--govt-blue); color: white;">
                            <i class="bi bi-person-badge"></i> Staff Registration Form
                        </div>
                        <div class="card-body">
                            <form method="POST" action="add_staff.php" class="needs-validation" novalidate>
                                <!-- Personal Information Section -->
                                <div class="form-section">
                                    <h4 style="color: var(--govt-blue);">Personal Information</h4>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label required-field">Full Name</label>
                                            <input type="text" class="form-control" name="full_name" value="<?= isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : '' ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email Address</label>
                                            <input type="email" class="form-control" name="email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Phone Number</label>
                                            <input type="tel" class="form-control" name="phone" value="<?= isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '' ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Facility and Role Assignment -->
                                <div class="form-section">
                                    <h4 style="color: var(--govt-blue);">Facility & Role Assignment</h4>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label required-field">Facility</label>
                                            <select class="form-select" name="facility_id" required>
                                                <option value="">Select Facility</option>
                                                <?php
                                                $facilities = $db->query("
                                                    SELECT f.facility_id, f.official_name, d.district_name 
                                                    FROM veterinary_facilities f
                                                    JOIN districts d ON f.district = d.district_code
                                                    ORDER BY d.district_name, f.official_name
                                                ");
                                                
                                                if ($facilities && $facilities->num_rows > 0) {
                                                    while ($facility = $facilities->fetch_assoc()) {
                                                        echo "<option value='{$facility['facility_id']}'>
                                                            [{$facility['district_name']}] {$facility['official_name']}
                                                        </option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label required-field">Staff Role</label>
                                            <select class="form-select" name="role_id" required id="roleSelect">
                                                <option value="">Select Role</option>
                                                <?php
                                                $roles = $db->query("SELECT * FROM staff_roles ORDER BY permission_level DESC");
                                                if ($roles && $roles->num_rows > 0) {
                                                    while ($role = $roles->fetch_assoc()) {
                                                        echo "<option value='{$role['role_id']}' 
                                                            data-description='{$role['description']}'
                                                            data-permission='{$role['permission_level']}'>
                                                            {$role['role_name']}
                                                        </option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                            <div id="roleDescription" class="role-description">
                                                Select a role to see description
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Account Information -->
                                <div class="form-section">
                                    <h4 style="color: var(--govt-blue);">Account Information</h4>
                                    
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle-fill"></i> System will automatically generate:
                                        <ul class="mb-0">
                                            <li>Unique username based on staff name</li>
                                            <li>Secure temporary password</li>
                                            <li>Account will be set to active status</li>
                                            <li>Credentials will be marked as auto-generated</li>
                                            <li>Initial password will be stored temporarily</li>
                                            <li>Credential sent timestamp will be recorded</li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="submit" class="btn btn-govt me-md-2">
                                        <i class="bi bi-person-plus"></i> Register Staff
                                    </button>
                                    <a href="manage_staff.php" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle"></i> Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show role description when selected
        document.getElementById('roleSelect').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const description = selectedOption.getAttribute('data-description') || 'No description available';
            const permissionLevel = selectedOption.getAttribute('data-permission') || '0';
            
            const descriptionElement = document.getElementById('roleDescription');
            descriptionElement.innerHTML = description + 
                ' <span class="permission-badge">Permission Level: ' + permissionLevel + '</span>';
        });

        // Form validation
        (function() {
            'use strict';
            
            var forms = document.querySelectorAll('.needs-validation');
            
            Array.prototype.slice.call(forms)
                .forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        
                        form.classList.add('was-validated');
                    }, false);
                });
        })();
    </script>
</body>
</html>