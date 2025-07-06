        <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="staff_dashboard.php">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="appointments.php">
                    <i class="bi bi-calendar-check"></i> Appointments
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="patients.php">
                    <i class="bi bi-heart-pulse"></i> Patients
                </a>
            </li>
            <?php if ($_SESSION['permission_level'] >= 2): ?>
            <li class="nav-item">
                <a class="nav-link" href="medical_records.php">
                    <i class="bi bi-file-medical"></i> Medical Records
                </a>
            </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link" href="registration.php">
                    <i class="bi bi-person-plus"></i> Pet Owner Registration
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="login.php">
                    <i class="bi bi-heart"></i> Pet Registration
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="staff_logout.php">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</nav>