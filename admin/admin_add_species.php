<?php
session_start();

// Check if user has admin privileges (you'll need to implement this in your user system)
// if ($_SESSION['user']['role'] !== 'admin') {
//     header("Location: dashboard.php");
//     exit;
// }

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission here
    $species = $_POST['species'] ?? '';
    $breeds = $_POST['breeds'] ?? [];
    
    // Here you would typically save to database
    // For now, we'll just show a success message
    $success = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Species/Breed | Department of Animal Husbandry & Veterinary Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --govt-blue: #0066b3;
            --govt-dark-blue: #004080;
            --govt-light-blue: #e6f2ff;
            --govt-gold: #d4af37;
            --govt-cream: #f5f5f0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--govt-cream);
        }
        
        /* Header styles */
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
            background-color: var(--govt-dark-blue);
        }
        
        .govt-seal {
            max-height: 80px;
        }
        
        /* Admin specific styles */
        .admin-header {
            background-color: var(--govt-blue);
            color: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border-left: 5px solid var(--govt-gold);
        }
        
        .admin-card {
            border: 1px solid #ddd;
            border-top: 3px solid var(--govt-blue);
            transition: all 0.3s ease;
            background-color: white;
        }
        
        .admin-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .admin-menu {
            background-color: var(--govt-dark-blue);
            color: white;
            min-height: calc(100vh - 56px);
            padding: 0;
        }
        
        .admin-menu .nav-link {
            color: rgba(255,255,255,0.9);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding: 12px 20px;
        }
        
        .admin-menu .nav-link:hover {
            color: white;
            background-color: rgba(255,255,255,0.1);
        }
        
        .admin-menu .nav-link.active {
            color: white;
            background-color: var(--govt-blue);
            border-left: 3px solid var(--govt-gold);
        }
        
        .admin-menu .nav-link i {
            margin-right: 10px;
        }
        
        .stat-card {
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            color: white;
            border: none;
        }
        
        .stat-card.blue {
            background-color: var(--govt-blue);
        }
        
        .stat-card.dark-blue {
            background-color: var(--govt-dark-blue);
        }
        
        .stat-card.gold {
            background-color: var(--govt-gold);
            color: #333;
        }
        
        .stat-card.light-blue {
            background-color: var(--govt-light-blue);
            color: #333;
            border: 1px solid #ccc;
        }
        
        .btn-govt {
            background-color: var(--govt-blue);
            color: white;
            border: none;
        }
        
        .btn-govt:hover {
            background-color: var(--govt-dark-blue);
            color: white;
        }
        
        .btn-govt-outline {
            background-color: transparent;
            color: var(--govt-blue);
            border: 1px solid var(--govt-blue);
        }
        
        .btn-govt-outline:hover {
            background-color: var(--govt-blue);
            color: white;
        }
        
        /* Custom styles for breed boxes */
        .breed-box {
            display: none;
            margin-top: 15px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border-left: 3px solid var(--govt-blue);
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
                    <li class="nav-item"><a class="nav-link" href="locate.php"><i class="bi bi-geo"></i> Locate Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="gallery.php"><i class="bi bi-images"></i> Gallery</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php"><i class="bi bi-telephone"></i> Contact</a></li>
                </ul>
                <div class="d-flex gap-2">
                    <a href="admin_logout.php" class="btn btn-danger btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid mt-4" id="main-content">
        <div class="row">
            <!-- Admin Sidebar Menu -->
            <div class="col-md-3 col-lg-2">
                <div class="admin-menu">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="admin_dashboard.php">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_doctors.php">
                                <i class="bi bi-person-badge"></i> Doctors
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_hospitals.php">
                                <i class="bi bi-hospital"></i> Hospitals
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="admin_species.php">
                                <i class="bi bi-tags"></i> Species/Breeds
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_vaccinations.php">
                                <i class="bi bi-syringe"></i> Vaccinations
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_transfers.php">
                                <i class="bi bi-arrow-left-right"></i> Transfers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_reports.php">
                                <i class="bi bi-file-earmark-bar-graph"></i> Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_users.php">
                                <i class="bi bi-people"></i> User Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_settings.php">
                                <i class="bi bi-gear"></i> Settings
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Main Admin Content Area -->
            <div class="col-md-9 col-lg-10">
                <div class="admin-header">
                    <h3><i class="bi bi-tags"></i> Add New Species/Breed</h3>
                    <p class="mb-0">Register new animal species and their breeds in the system</p>
                </div>
                
                <?php if (isset($success) && $success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill"></i> Species and breeds have been successfully registered!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <div class="card admin-card mb-4">
                    <div class="card-header" style="background-color: var(--govt-blue); color: white;">
                        <i class="bi bi-plus-circle"></i> Species & Breed Information
                    </div>
                    <div class="card-body">
                        <form id="speciesForm" method="POST" action="">
                            <div class="mb-3">
                                <label for="species" class="form-label">Species Name</label>
                                <input type="text" class="form-control" id="species" name="species" required placeholder="Enter species name (e.g., Canine, Feline, Bovine)">
                            </div>
                            
                            <div class="mb-3">
                                <label for="breedCount" class="form-label">Number of Breeds</label>
                                <input type="number" class="form-control" id="breedCount" name="breedCount" min="1" max="50" required placeholder="Enter number of breeds for this species">
                                <small class="text-muted">Enter how many breeds you want to add for this species</small>
                            </div>
                            
                            <div class="mb-3">
                                <button type="button" class="btn btn-govt" onclick="generateBreedFields()">
                                    <i class="bi bi-list-check"></i> Generate Breed Fields
                                </button>
                            </div>
                            
                            <div id="breedsContainer">
                                <!-- Breed fields will be generated here -->
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <button type="reset" class="btn btn-outline-secondary me-md-2">
                                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-govt" id="registerBtn" disabled>
                                    <i class="bi bi-save"></i> Register Species & Breeds
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card admin-card">
                    <div class="card-header" style="background-color: var(--govt-blue); color: white;">
                        <i class="bi bi-info-circle"></i> Instructions
                    </div>
                    <div class="card-body">
                        <ol>
                            <li>Enter the species name (e.g., Dog, Cat, Cow)</li>
                            <li>Specify how many breeds you want to add for this species</li>
                            <li>Click "Generate Breed Fields" to create input fields for each breed</li>
                            <li>Fill in all breed names</li>
                            <li>Click "Register Species & Breeds" to save the information</li>
                        </ol>
                        <div class="alert alert-info">
                            <i class="bi bi-lightbulb"></i> <strong>Tip:</strong> You can add more breeds later by editing the species record.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer mt-5 py-4" style="background-color: var(--govt-blue); color: white; border-top: 3px solid var(--govt-gold);">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5><i class="bi bi-building"></i> Department Info</h5>
                    <hr style="border-color: rgba(255,255,255,0.2);">
                    <p class="mb-0">Department of Animal Husbandry & Veterinary Services, Andaman & Nicobar Administration</p>
                </div>
                
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5><i class="bi bi-geo-alt"></i> Headquarters</h5>
                    <hr style="border-color: rgba(255,255,255,0.2);">
                    <address class="mb-0">
                        Haddo, Port Blair<br>
                        Andaman and Nicobar Islands - 744102<br>
                        <i class="bi bi-telephone"></i> 03192-233286<br>
                        <i class="bi bi-envelope"></i> dir-ah@and.nic.in
                    </address>
                </div>
                
                <div class="col-md-4">
                    <h5><i class="bi bi-link-45deg"></i> Important Links</h5>
                    <hr style="border-color: rgba(255,255,255,0.2);">
                    <ul class="list-unstyled">
                        <li><a href="https://andaman.gov.in" class="text-white text-decoration-none"><i class="bi bi-chevron-right"></i> A&N Administration</a></li>
                        <li><a href="https://ahvs.andaman.gov.in" class="text-white text-decoration-none"><i class="bi bi-chevron-right"></i> Department Website</a></li>
                        <li><a href="#" class="text-white text-decoration-none"><i class="bi bi-chevron-right"></i> Policies</a></li>
                        <li><a href="#" class="text-white text-decoration-none"><i class="bi bi-chevron-right"></i> Accessibility</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4" style="border-color: rgba(255,255,255,0.3);">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0">
                        <i class="bi bi-c-circle"></i> <?php echo date('Y'); ?> Department of AH&VS. All Rights Reserved.
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0">
                        <span style="color: var(--govt-gold);">A Government of India Initiative</span>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
        
        // Skip to main content functionality
        document.querySelector('a[href="#main-content"]').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('main-content').setAttribute('tabindex', '-1');
            document.getElementById('main-content').focus();
        });
        
        // Generate breed fields based on the number entered
        function generateBreedFields() {
            const breedCount = parseInt(document.getElementById('breedCount').value);
            const container = document.getElementById('breedsContainer');
            
            if (isNaN(breedCount) || breedCount < 1) {
                alert('Please enter a valid number of breeds (1-50)');
                return;
            }
            
            // Clear previous fields
            container.innerHTML = '';
            
            // Generate new fields
            for (let i = 0; i < breedCount; i++) {
                const breedDiv = document.createElement('div');
                breedDiv.className = 'breed-box';
                breedDiv.style.display = 'block';
                
                breedDiv.innerHTML = `
                    <div class="mb-3">
                        <label for="breed_${i}" class="form-label">Breed #${i + 1}</label>
                        <input type="text" class="form-control" id="breed_${i}" name="breeds[]" required placeholder="Enter breed name (e.g., Labrador, Persian, Holstein)">
                    </div>
                `;
                
                container.appendChild(breedDiv);
            }
            
            // Enable the register button
            document.getElementById('registerBtn').disabled = false;
        }
        
        // Validate form before submission
        document.getElementById('speciesForm').addEventListener('submit', function(e) {
            const breedInputs = document.querySelectorAll('input[name="breeds[]"]');
            let isValid = true;
            
            breedInputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all breed names');
            }
        });
    </script>
</body>
</html>