<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <div class="sidebar-header text-center mb-4">
            <h4>Pet Owner Portal</h4>
            <hr>
            <div class="user-info">
                <p class="mb-1">Welcome, <strong><?= htmlspecialchars($_SESSION['user']['name'] ?? 'User') ?></strong></p>
                <small class="text-muted">ID: <?= htmlspecialchars($_SESSION['pet_owner_id'] ?? '') ?></small>
            </div>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="my_pets.php">
                    <i class="bi bi-heart"></i> My Pets
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="appointments.php">
                    <i class="bi bi-calendar-check"></i> Appointments
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="medical_records.php">
                    <i class="bi bi-file-medical"></i> Medical Records
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="profile.php">
                    <i class="bi bi-person"></i> Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</nav>