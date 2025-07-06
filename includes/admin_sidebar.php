<style>
    /* Sidebar Blue Color Scheme */
    .admin-menu {
        background-color: #1a4b8c; /* Government blue */
        border-radius: 8px;
        padding: 15px 0;
        height: 100%;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .admin-menu .nav-link {
        color: rgba(255,255,255,0.8);
        padding: 10px 20px;
        margin: 2px 0;
        border-left: 3px solid transparent;
        transition: all 0.3s ease;
    }
    
    .admin-menu .nav-link:hover {
        background-color: rgba(255,255,255,0.1);
        color: white;
        border-left: 3px solid #d4af37; /* Gold accent */
    }
    
    .admin-menu .nav-link.active {
        background-color: rgba(255,255,255,0.15);
        color: white;
        border-left: 3px solid #d4af37; /* Gold accent */
        font-weight: 500;
    }
    
    .admin-menu .nav-link i {
        margin-right: 10px;
        font-size: 1.1rem;
    }
    
    .admin-menu .nav-item:last-child {
        margin-top: 10px;
        border-top: 1px solid rgba(255,255,255,0.1);
        padding-top: 10px;
    }
</style>

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