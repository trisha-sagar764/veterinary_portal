     <?php
// Verify doctor is logged in
if (!isset($_SESSION['doctor_logged_in']) || !$_SESSION['doctor_logged_in']) {
    header('Location: doctor_login.php');
    exit;
}

// Get doctor's name - checking multiple possible session locations
$doctor_name = $_SESSION['full_name'] ?? $_SESSION['doctor']['full_name'] ?? 'Doctor';
?>
<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <!-- Welcome Section -->
        <div class="text-center mb-4 p-3 bg-primary text-white rounded">
            <h5 class="mb-1">Welcome!</h5>
            <h4> <?= htmlspecialchars($doctor_name) ?></h4>
        </div>  
       
       
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
            <li  class="nav-item">
    <form method="POST" action="doctor_logout.php" class="d-inline">
        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
        <button type="submit" class="nav-link btn btn-link" style="cursor: pointer;">
            <i class="bi bi-box-arrow-right"></i> Logout
        </button>
    </form>
            </li>
        </ul>
    </div>
</nav>