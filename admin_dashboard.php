<?php
session_start();


?>
<?php include __DIR__ . '/includes/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include __DIR__ . '/includes/admin_sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Administrator Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                    </div>
                </div>
            </div>

            <style>
    /* Add this CSS to your stylesheet or in a style tag in the head */
    .stat-card {
        border-radius: 8px;
        color: white;
        transition: transform 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .stat-card.blue {
        background:rgb(35, 96, 176); /* Government blue color */
        border-left: 4px solidrgb(12, 49, 105);
    }
    
    .stat-card.gold {
        background:rgb(228, 188, 59); /* Gold color */
        border-left: 4px solid #b5942e;
        color: #333; /* Dark text for gold background */
    }
    
    .stat-card.gold a {
        color: #333 !important;
    }
    
    .stat-card h6 {
        font-size: 14px;
        opacity: 0.9;
    }
    
    .stat-card h2 {
        font-weight: 700;
        margin: 5px 0;
    }
    
    .stat-card i {
        align-self: center;
    }
</style>

<!-- Quick Stats Row - Government Blue Cards -->
<div class="row g-3 mb-2">
    <div class="col-md-4">
        <div class="stat-card blue p-3">
            <div class="d-flex justify-content-between">
                <div>
                    <h6>Total Doctors</h6>
                    <h2>24</h2>
                </div>
                <i class="bi bi-person-badge" style="font-size: 2.5rem; opacity: 0.5;"></i>
            </div>
            <a href="admin_doctors.php" class="text-white">View All <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card blue p-3">
            <div class="d-flex justify-content-between">
                <div>
                    <h6>Hospitals/Centers</h6>
                    <h2>8</h2>
                </div>
                <i class="bi bi-hospital" style="font-size: 2.5rem; opacity: 0.5;"></i>
            </div>
            <a href="admin_hospitals.php" class="text-white">Manage <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card blue p-3">
            <div class="d-flex justify-content-between">
                <div>
                    <h6>Hospitals providing Vaccine</h6>
                    <h2>24</h2>
                </div>
                <i class="bi bi-person-badge" style="font-size: 2.5rem; opacity: 0.5;"></i>
            </div>
            <a href="admin_doctors.php" class="text-white">View All <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
</div>

<!-- Quick Stats Row - Gold Cards -->
<div class="row g-3 mb-2">
    <div class="col-md-4">
        <div class="stat-card gold p-3">
            <div class="d-flex justify-content-between">
                <div>
                    <h6>Registered Species</h6>
                    <h2>12</h2>
                </div>
                <i class="bi bi-tags" style="font-size: 2.5rem; opacity: 0.5;"></i>
            </div>
            <a href="admin_species.php" class="text-dark">Configure <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card gold p-3">
            <div class="d-flex justify-content-between">
                <div>
                    <h6>Registered Breeds</h6>
                    <h2>24</h2>
                </div>
                <i class="bi bi-person-badge" style="font-size: 2.5rem; opacity: 0.5;"></i>
            </div>
            <a href="admin_doctors.php" class="text-dark">View All <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card gold p-3">
            <div class="d-flex justify-content-between">
                <div>
                    <h6>Vaccination Types</h6>
                    <h2>18</h2>
                </div>
                <i class="bi bi-syringe" style="font-size: 2.5rem; opacity: 0.5;"></i>
            </div>
            <a href="admin_vaccinations.php" class="text-dark">Manage <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
</div>
                <!-- Quick Actions -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card admin-card">
                            <div class="card-header" style="background-color: var(--admin-red); color: white;">
                                <i class="bi bi-lightning"></i> Quick Actions
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <a href="add_staff.php" class="btn btn-outline-danger w-100">
                                            <i class="bi bi-person-plus"></i> Add New Doctor/Staff
                                        </a>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <a href="admin_add_hospital.php" class="btn btn-outline-danger w-100">
                                            <i class="bi bi-hospital"></i> Add Hospital/Center
                                        </a>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <a href="admin_add_species.php" class="btn btn-outline-danger w-100">
                                            <i class="bi bi-tag"></i> Add Species/Breed
                                        </a>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <a href="admin_add_vaccination.php" class="btn btn-outline-danger w-100">
                                            <i class="bi bi-plus-circle"></i> Add Vaccination
                                        </a>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <a href="admin_process_transfer.php" class="btn btn-outline-danger w-100">
                                            <i class="bi bi-arrow-left-right"></i> Process Transfer
                                        </a>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <a href="admin_generate_report.php" class="btn btn-outline-danger w-100">
                                            <i class="bi bi-file-earmark-bar-graph"></i> Generate Report
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activity and System Alerts -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card admin-card mb-4">
                            <div class="card-header" style="background-color: var(--admin-red); color: white;">
                                <i class="bi bi-clock-history"></i> Recent Activity
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1"><i class="bi bi-person-plus text-primary"></i> New Doctor Added</h6>
                                            <small class="text-muted">2 hours ago</small>
                                        </div>
                                        <p>Dr. Priya Sharma was added to the system</p>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1"><i class="bi bi-hospital text-success"></i> Hospital Updated</h6>
                                            <small class="text-muted">1 day ago</small>
                                        </div>
                                        <p>Port Blair Veterinary Hospital details were updated</p>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1"><i class="bi bi-syringe text-danger"></i> New Vaccination</h6>
                                            <small class="text-muted">3 days ago</small>
                                        </div>
                                        <p>Canine Distemper vaccine was added to the system</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card admin-card mb-4">
                            <div class="card-header" style="background-color: var(--admin-red); color: white;">
                                <i class="bi bi-exclamation-triangle"></i> System Alerts
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle-fill"></i> <strong>Low Stock Alert:</strong> Rabies vaccine is running low at Diglipur Center
                                </div>
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle-fill"></i> <strong>Pending Approval:</strong> 3 new doctor registrations require review
                                </div>
                                <div class="alert alert-danger">
                                    <i class="bi bi-x-circle-fill"></i> <strong>System Update:</strong> Database backup scheduled for tonight at 2 AM
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

<?php include __DIR__ . '/includes/footer.php'; ?>
