<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php'; // For basic functions
require_once __DIR__ . '/includes/appointment_functions.php'; // For appointment-specific functions

// Start session
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Validate session
$petOwner = validatePetOwnerSession();
if (!$petOwner) {
    header('Location: login.php?reason=not_logged_in');
    exit;
}

// Get appointment ID from URL
$appointmentId = $_GET['id'] ?? '';
if (empty($appointmentId)) {
    header('Location: appointments.php?error=no_appointment_id');
    exit;
}

// Get appointment details
try {
    $appointment = getAppointmentById($appointmentId, $petOwner['pet_owner_id']);
    
    if (!$appointment) {
        header('Location: appointments.php?error=appointment_not_found');
        exit;
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    header('Location: appointments.php?error=database_error');
    exit;
}

$pageTitle = "Appointment Details";
include __DIR__ . '/includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include __DIR__ . '/includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Appointment Details</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="appointments.php" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Appointments
                    </a>
                </div>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <?php
                    $errorMessages = [
                        'not_owner' => 'You can only view your own appointments.',
                        'invalid_status' => 'Invalid appointment status for this action.'
                    ];
                    echo htmlspecialchars($errorMessages[$_GET['error']] ?? 'An error occurred.');
                    ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-calendar-event"></i> Appointment Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <dl class="row">
                                <dt class="col-sm-3">Appointment ID</dt>
                                <dd class="col-sm-9"><?= htmlspecialchars($appointment['appointment_id']) ?></dd>

                                <dt class="col-sm-3">Date & Time</dt>
                                <dd class="col-sm-9">
                                    <?= date('l, F j, Y \a\t g:i A', strtotime($appointment['appointment_date'])) ?>
                                    <small class="text-muted">(<?= timeAgo($appointment['appointment_date']) ?>)</small>
                                </dd>

                                <dt class="col-sm-3">Status</dt>
                                <dd class="col-sm-9">
                                    <span class="badge bg-<?= 
                                        $appointment['status'] === 'scheduled' ? 'primary' : 
                                        ($appointment['status'] === 'completed' ? 'success' : 
                                        ($appointment['status'] === 'cancelled' ? 'secondary' : 'warning'))
                                    ?>">
                                        <?= ucfirst($appointment['status']) ?>
                                    </span>
                                </dd>

                                <dt class="col-sm-3">Reason</dt>
                                <dd class="col-sm-9"><?= htmlspecialchars($appointment['reason']) ?></dd>

                                <dt class="col-sm-3">Problem Description</dt>
                                <dd class="col-sm-9">
                                    <?= !empty($appointment['problem_description']) ? nl2br(htmlspecialchars($appointment['problem_description'])) : '<span class="text-muted">None provided</span>' ?>
                                </dd>

                                <dt class="col-sm-3">Additional Notes</dt>
                                <dd class="col-sm-9">
                                    <?= !empty($appointment['notes']) ? nl2br(htmlspecialchars($appointment['notes'])) : '<span class="text-muted">None provided</span>' ?>
                                </dd>
                            </dl>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-clipboard2-pulse"></i> Medical Notes
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($appointment['medical_notes'])): ?>
                                <?= nl2br(htmlspecialchars($appointment['medical_notes'])) ?>
                            <?php else: ?>
                                <p class="text-muted">No medical notes have been added yet.</p>
                                <?php if ($appointment['status'] === 'completed'): ?>
                                    <p>Please contact the veterinary facility if you believe this is an error.</p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-heart"></i> Pet Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <img src="<?= htmlspecialchars($appointment['pet_image'] ?? 'assets/images/default-pet.jpg') ?>" 
                                     class="img-fluid rounded-circle" 
                                     style="width: 150px; height: 150px; object-fit: cover;" 
                                     alt="<?= htmlspecialchars($appointment['pet_name']) ?>">
                            </div>
                            <dl class="row">
                                <dt class="col-sm-4">Name</dt>
                                <dd class="col-sm-8"><?= htmlspecialchars($appointment['pet_name']) ?></dd>

                                <dt class="col-sm-4">Species</dt>
                                <dd class="col-sm-8"><?= htmlspecialchars($appointment['species_name']) ?></dd>

                                <dt class="col-sm-4">Breed</dt>
                                <dd class="col-sm-8"><?= htmlspecialchars($appointment['breed_name']) ?></dd>

                                <dt class="col-sm-4">Age</dt>
                                <dd class="col-sm-8"><?= calculatePetAge($appointment['date_of_birth']) ?></dd>

                                <dt class="col-sm-4">Weight</dt>
                                <dd class="col-sm-8"><?= htmlspecialchars($appointment['weight']) ?> kg</dd>
                            </dl>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-building"></i> Facility Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <h6><?= htmlspecialchars($appointment['facility_name']) ?></h6>
                            <p class="mb-1">
                                <?= htmlspecialchars($appointment['address_line1']) ?><br>
                                <?php if (!empty($appointment['address_line2'])): ?>
                                    <?= htmlspecialchars($appointment['address_line2']) ?><br>
                                <?php endif; ?>
                                <?= htmlspecialchars($appointment['district_name']) ?><br>
                                <?= htmlspecialchars($appointment['pincode']) ?>
                            </p>
                            <hr>
                            <p class="mb-1">
                                <strong>Facility Type:</strong> <?= htmlspecialchars($appointment['facility_type_name']) ?>
                            </p>
                            <?php if (!empty($appointment['facility_phone'])): ?>
                                <p class="mb-1">
                                    <strong>Phone:</strong> <?= htmlspecialchars($appointment['facility_phone']) ?>
                                </p>
                            <?php endif; ?>
                            <hr>
                            <a href="facility_details.php?id=<?= $appointment['facility_id'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-info-circle"></i> View Facility Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($appointment['status'] === 'scheduled'): ?>
                <div class="card border-warning mt-4">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-exclamation-triangle"></i> Appointment Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="button" class="btn btn-outline-secondary me-md-2" data-bs-toggle="modal" data-bs-target="#rescheduleModal">
                                <i class="bi bi-calendar2-event"></i> Reschedule
                            </button>
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                <i class="bi bi-calendar-x"></i> Cancel Appointment
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Reschedule Modal -->
                <div class="modal fade" id="rescheduleModal" tabindex="-1" aria-labelledby="rescheduleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="rescheduleModalLabel">Reschedule Appointment</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="process_reschedule.php" method="POST">
                                <input type="hidden" name="appointment_id" value="<?= $appointment['appointment_id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="new_date" class="form-label">New Date & Time</label>
                                        <input type="datetime-local" class="form-control" id="new_date" name="new_date" 
                                               min="<?= date('Y-m-d\TH:i') ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="reschedule_reason" class="form-label">Reason for Rescheduling</label>
                                        <textarea class="form-control" id="reschedule_reason" name="reschedule_reason" rows="3" required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Request Reschedule</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Cancel Modal -->
                <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="cancelModalLabel">Cancel Appointment</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="process_cancel.php" method="POST">
                                <input type="hidden" name="appointment_id" value="<?= $appointment['appointment_id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                
                                <div class="modal-body">
                                    <p>Are you sure you want to cancel this appointment?</p>
                                    <div class="mb-3">
                                        <label for="cancel_reason" class="form-label">Reason for Cancellation</label>
                                        <textarea class="form-control" id="cancel_reason" name="cancel_reason" rows="3" required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-danger">Confirm Cancellation</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>