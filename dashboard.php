<?php
// Absolute paths for includes
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// Start session if not already started
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Validate session
if (empty($_SESSION['pet_owner_id']) || empty($_SESSION['logged_in'])) {
    header('Location: login.php?reason=not_logged_in');
    exit;
}

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Get and validate pet owner
$petOwner = getPetOwnerByPetOwnerId($_SESSION['pet_owner_id']);
if (!$petOwner) {
    session_unset();
    session_destroy();
    header('Location: login.php?reason=invalid_user');
    exit;
}

// Initialize empty arrays for data
$pets = $appointments = $vaccinations = [];

// Fetch pets data
try {
    $db = getDatabaseConnection();
    
    // Get pets (limited to 3) - Fixed version
    $stmt = $db->prepare("
    SELECT p.pet_id, p.pet_name 
    FROM pets p
    WHERE p.pet_owner_id = ?
    ORDER BY p.pet_name ASC  /* Changed to alphabetical order */
    LIMIT 3
");
$stmt->execute([$_SESSION['pet_owner_id']]);
    $pets = $stmt->fetchAll();

    // Get upcoming appointments (limited to 3)
    $stmt = $db->prepare("
        SELECT 
            a.appointment_id,
            a.preferred_date AS appointment_date,
            a.preferred_time,
            a.appointment_type AS reason,
            a.status,
            a.token_number,
            p.pet_name,
            vf.official_name AS facility_name
        FROM appointments a
        JOIN pets p ON a.pet_id = p.pet_id
        JOIN veterinary_facilities vf ON a.facility_id = vf.facility_id
        WHERE p.pet_owner_id = ?
        AND a.status IN ('Pending', 'Confirmed')
        AND CONCAT(a.preferred_date, ' ', a.preferred_time) >= NOW()
        ORDER BY a.preferred_date ASC, a.preferred_time ASC
        LIMIT 3
    ");
    $stmt->execute([$_SESSION['pet_owner_id']]);
    $appointments = $stmt->fetchAll();
    
    if ($stmt === false) {
        throw new Exception("Failed to prepare pets query");
    }
    
    $executed = $stmt->execute([$_SESSION['pet_owner_id']]);
    if ($executed === false) {
        throw new Exception("Failed to execute pets query");
    }

$stmt = $db->prepare("
    SELECT 
        v.vaccination_id,
        v.pet_id,
        v.date_administered,
        v.administered_by,
        p.pet_name,
        vt.vaccine_name
    FROM vaccinations v
    JOIN pets p ON v.pet_id = p.pet_id
    JOIN vaccine_types vt ON v.vaccine_type_id = vt.vaccine_id
    WHERE p.pet_owner_id = ?
    ORDER BY v.date_administered DESC
    LIMIT 3
");
$stmt->execute([$_SESSION['pet_owner_id']]);
$vaccinations = $stmt->fetchAll();

// Get announcements (limited to 3)
$stmt = $db->prepare("
    SELECT 
        announcement_id,
        title,
        content,
        start_date,
        end_date,
        created_at
    FROM announcements
    WHERE start_date <= NOW() AND end_date >= NOW()
    ORDER BY created_at DESC
    LIMIT 3
");
$stmt->execute();
$announcements = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Dashboard data error: " . $e->getMessage());
    // Continue execution with empty arrays
} catch (Exception $e) {
    error_log("Dashboard general error: " . $e->getMessage());
    // Continue execution with empty arrays
}

// Include header template
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php';?>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="add_pet.php" class="btn btn-sm btn-primary me-2">
                        <i class="bi bi-plus-circle"></i> Add Pet
                    </a>
                    <a href="book_appointment.php" class="btn btn-sm btn-success">
                        <i class="bi bi-calendar-plus"></i> Book Appointment
                    </a>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title">My Pets</h5>
                                    <h2 class="mb-0"><?= count($pets) ?></h2>
                                </div>
                                <i class="bi bi-heart" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="my_pets.php" class="text-white">View All <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title">Appointments</h5>
                                    <h2 class="mb-0"><?= count($appointments) ?></h2>
                                </div>
                                <i class="bi bi-calendar-check" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="appointments.php" class="text-white">View All <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card text-white bg-warning mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title">Vaccination Records</h5>
                                    <h2 class="mb-0"><?= count($vaccinations) ?></h2>
                                </div>
                                <i class="bi bi-shield-plus" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="vaccinations.php" class="text-white">View All <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- What's New Section -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-megaphone"></i> What's New?</h5>
                        <a href="announcements.php" class="btn btn-sm btn-light">View All Announcements</a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($announcements)): ?>
                        <div class="list-group">
                            <?php foreach ($announcements as $announcement): ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1 text-primary"><?= htmlspecialchars($announcement['title']) ?></h5>
                                        <small>Posted: <?= date('M j, Y', strtotime($announcement['created_at'])) ?></small>
                                    </div>
                                    <p class="mb-1"><?= htmlspecialchars($announcement['content']) ?></p>
                                    <small class="text-muted">
                                        Valid until: <?= date('M j, Y', strtotime($announcement['end_date'])) ?>
                                    </small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            No current announcements. Check back later for updates!
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Appointments -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Upcoming Appointments</h5>
                        <a href="appointments.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($appointments)): ?>
                        <div class="list-group">
                            <?php foreach ($appointments as $appt): ?>
                                <a href="view_appointment.php?id=<?= $appt['appointment_id'] ?>" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">
                                            <?= htmlspecialchars($appt['pet_name']) ?> - <?= htmlspecialchars($appt['reason']) ?>
                                            <span class="badge bg-primary">Token: <?= str_pad($appt['token_number'], 3, '0', STR_PAD_LEFT) ?></span>
                                        </h6>
                                        <small><?= date('M j, Y', strtotime($appt['appointment_date'])) ?> at <?= date('g:i A', strtotime($appt['preferred_time'])) ?></small>
                                    </div>
                                    <p class="mb-1">
                                        At <?= htmlspecialchars($appt['facility_name']) ?>
                                    </p>
                                    <small class="badge bg-<?= $appt['status'] === 'Confirmed' ? 'success' : 'warning' ?>">
                                        <?= ucfirst(htmlspecialchars($appt['status'])) ?>
                                    </small>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            No upcoming appointments. <a href="book_appointment.php" class="alert-link">Book an appointment</a>.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- My Pets Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">My Pets</h5>
                        <a href="my_pets.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($pets)): ?>
                        <div class="list-group">
                            <?php foreach ($pets as $pet): ?>
                                <a href="pet_details.php?id=<?= urlencode($pet['pet_id']) ?>" 
                                   class="list-group-item list-group-item-action">
                                    <?= htmlspecialchars($pet['pet_name']) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            You haven't registered any pets yet. <a href="add_pet.php" class="alert-link">Add your first pet</a>.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
<!-- Vaccination Records -->
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Vaccination Records</h5>
            <div>
                <a href="vaccinations.php" class="btn btn-sm btn-outline-primary me-2">View All</a>
                <a href="book_appointment.php" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle"></i> Book Vaccination
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <?php if (!empty($vaccinations)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Pet</th>
                            <th>Vaccine</th>
                            <th>Date Administered</th>
                            <th>Administered By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vaccinations as $vax): ?>
                            <tr>
                                <td><?= htmlspecialchars($vax['pet_name']) ?></td>
                                <td><?= htmlspecialchars($vax['vaccine_name']) ?></td>
                                <td><?= date('M j, Y', strtotime($vax['date_administered'])) ?></td>
                                <td><?= htmlspecialchars($vax['administered_by']) ?></td>
                                <td>
                                    <a href="vaccination_details.php?id=<?= $vax['vaccination_id'] ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                No vaccination records found. 
                <a href="book_appointment.php" class="alert-link">Schedule a vaccination</a>.
            </div>
        <?php endif; ?>
    </div>
</div>
        </main>
    </div>
</div>
<div style="margin-bottom: 50px;"></div>
<?php 
// Include footer
include 'includes/footer.php'; 
?>