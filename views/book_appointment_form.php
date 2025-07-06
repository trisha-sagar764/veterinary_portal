<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Book Appointment</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="appointments.php" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Appointments
        </a>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <h5><i class="bi bi-exclamation-triangle-fill"></i> Please fix the following errors:</h5>
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" action="book_appointment.php">
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
            
            <!-- Pet Selection -->
            <div class="mb-4">
                <h5 class="mb-3"><i class="bi bi-heart"></i> Select Pet</h5>
                <?php if (!empty($pets)): ?>
                    <select class="form-select" name="pet_id" required>
                        <option value="">-- Select your pet --</option>
                        <?php foreach ($pets as $pet): ?>
                            <option value="<?= $pet['pet_id'] ?>" 
                                <?= ($selectedPetId === $pet['pet_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($pet['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <div class="alert alert-warning">
                        No pets found. <a href="add_pet.php">Add a pet</a> to book an appointment.
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Veterinary Facilities -->
            <div class="mb-4">
                <h5 class="mb-3"><i class="bi bi-building"></i> Select Veterinary Facility</h5>
                <?php if (!empty($facilities)): ?>
                    <select class="form-select" name="facility_id" required>
                        <option value="">-- Select a facility --</option>
                        <?php foreach ($facilities as $facility): ?>
                            <option value="<?= $facility['facility_id'] ?>"
                                <?= ($selectedFacilityId === $facility['facility_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($facility['official_name']) ?> | 
                                <?= htmlspecialchars($facility['address_line2']) ?> | 
                                <?= htmlspecialchars($facility['district']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted">Showing only active facilities</small>
                <?php else: ?>
                    <div class="alert alert-warning">
                        No veterinary facilities available at this time.
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Appointment Details -->
            <div class="mb-4">
                <h5 class="mb-3"><i class="bi bi-calendar-event"></i> Appointment Details</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="appointment_date" class="form-label">Date & Time</label>
                        <input type="datetime-local" class="form-control" id="appointment_date" 
                            name="appointment_date" min="<?= date('Y-m-d\TH:i') ?>"
                            value="<?= isset($_POST['appointment_date']) ? htmlspecialchars($_POST['appointment_date']) : '' ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="reason" class="form-label">Reason for Visit</label>
                        <input type="text" class="form-control" id="reason" name="reason" 
                            value="<?= isset($_POST['reason']) ? htmlspecialchars($_POST['reason']) : htmlspecialchars($prefilledReason) ?>" required>
                    </div>
                </div>
            </div>
            
            <!-- Problem Description -->
            <div class="mb-4">
                <h5 class="mb-3"><i class="bi bi-clipboard2-pulse"></i> Describe the Problem</h5>
                <div class="form-floating">
                    <textarea class="form-control" id="problem_description" name="problem_description" 
                        style="height: 150px" placeholder="Describe your pet's symptoms or issues"><?= isset($_POST['problem_description']) ? htmlspecialchars($_POST['problem_description']) : '' ?></textarea>
                    <label for="problem_description">Describe your pet's symptoms or issues</label>
                </div>
                <small class="text-muted">Provide details about what's concerning you about your pet's health</small>
            </div>
            
            <!-- Additional Notes -->
            <div class="mb-4">
                <h5 class="mb-3"><i class="bi bi-pencil"></i> Additional Notes</h5>
                <div class="form-floating">
                    <textarea class="form-control" id="notes" name="notes" 
                        style="height: 100px" placeholder="Any other information you'd like to share"><?= isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : '' ?></textarea>
                    <label for="notes">Any other information you'd like to share</label>
                </div>
                <small class="text-muted">Optional information that might help the veterinarian</small>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-calendar-plus"></i> Book Appointment
                </button>
            </div>
        </form>
    </div>
</div>