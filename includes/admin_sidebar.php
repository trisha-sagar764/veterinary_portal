<?php
// admin_sidebar.php
?>

<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block sidebar collapse" style="background-color: rgb(35, 96, 176);">
    <div class="position-sticky pt-3">
        <div class="text-center mb-4">
            <h5 class="text-white mt-2">Welcome</h5>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="admin_dashboard.php">
                    <i class="bi bi-speedometer2 me-2"></i>
                    Dashboard
                </a>
            </li>
            
            <!-- Staff Management -->
            <li class="nav-item">
                <a class="nav-link" href="admin_doctors.php">
                    <i class="bi bi-person-badge me-2"></i>
                    Doctors & Staff
                </a>
            </li>
            
            <!-- Facilities -->
            <li class="nav-item">
                <a class="nav-link" href="admin_hospitals.php">
                    <i class="bi bi-hospital me-2"></i>
                    Hospitals/Centers
                </a>
            </li>
            
            <!-- Animal Data -->
            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-toggle="collapse" href="#animalSubmenu">
                    <i class="bi bi-paw me-2"></i>
                    Animal Data
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <div class="collapse" id="animalSubmenu">
                    <ul class="nav flex-column ps-4">
                        <li class="nav-item">
                            <a class="nav-link" href="admin_species.php">
                                <i class="bi bi-tags me-2"></i>
                                Species
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_breeds.php">
                                <i class="bi bi-diagram-3 me-2"></i>
                                Breeds
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- Vaccination Management -->
            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-toggle="collapse" href="#vaccineSubmenu">
                    <i class="bi bi-syringe me-2"></i>
                    Vaccinations
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <div class="collapse" id="vaccineSubmenu">
                    <ul class="nav flex-column ps-4">
                        <li class="nav-item">
                            <a class="nav-link" href="admin_vaccinations.php">
                                <i class="bi bi-list-check me-2"></i>
                                Vaccine Types
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            
            <!-- System -->
            <li class="nav-item">
                <a class="nav-link" href="admin_settings.php">
                    <i class="bi bi-gear me-2"></i>
                    System Settings
                </a>
            </li>
        </ul>
        
        <hr class="border-light">
        
        <div class="text-center text-white small mb-2">
            Logged in as: <strong><?php echo $_SESSION['username'] ?? 'Admin'; ?></strong>
        </div>
        <div class="d-grid gap-2">
            <a href="logout.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
            </a>
        </div>
    </div>
</nav>

<style>
    .sidebar {
        background-color: #212529;
        min-height: 100vh;
    }
    
    .nav-link {
        color: rgba(255, 255, 255, 0.75);
        border-radius: 4px;
        margin-bottom: 2px;
        transition: all 0.3s;
    }
    
    .nav-link:hover, .nav-link.active {
        color: white;
        background-color: rgba(255, 255, 255, 0.1);
    }
    
    .nav-link.active {
        font-weight: 500;
        border-left: 3px solid var(--admin-red);
    }
    
    .nav-link[data-bs-toggle="collapse"].collapsed .bi-chevron-down {
        transform: rotate(-90deg);
        transition: transform 0.3s ease;
    }
    
    .nav-link[data-bs-toggle="collapse"] .bi-chevron-down {
        transform: rotate(0deg);
        transition: transform 0.3s ease;
    }
    
    .nav-item .nav-link {
        padding: 0.5rem 1rem;
    }
    
    .nav-item .nav-link.collapsed {
        background-color: transparent;
    }
</style>
