<?php
// includes/partials/pet_medical_records.php
// This file displays a pet's medical history records

// Ensure we have the medical history data
if (!isset($medical_history)) {
    $medical_history = [];
}

if (!empty($medical_history)): ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Diagnosis</th>
                    <th>Treatment</th>
                    <th>Staff</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($medical_history as $record): ?>
                    <tr>
                        <td><?= date('M j, Y', strtotime($record['record_date'])) ?></td>
                        <td>
                            <span class="badge bg-<?= 
                                $record['record_type'] === 'vaccination' ? 'success' : 
                                ($record['record_type'] === 'surgery' ? 'danger' : 'primary')
                            ?>">
                                <?= htmlspecialchars(ucfirst($record['record_type'])) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($record['diagnosis']) ?></td>
                        <td><?= nl2br(htmlspecialchars($record['treatment'])) ?></td>
                        <td>
                            <?php if (!empty($record['staff_name'])): ?>
                                <small><?= htmlspecialchars($record['staff_name']) ?></small>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php if (!empty($record['medications']) || !empty($record['notes'])): ?>
                    <tr class="bg-light">
                        <td colspan="5">
                            <?php if (!empty($record['medications'])): ?>
                                <p class="mb-1"><strong>Medications:</strong> <?= nl2br(htmlspecialchars($record['medications'])) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($record['notes'])): ?>
                                <p class="mb-0"><strong>Notes:</strong> <?= nl2br(htmlspecialchars($record['notes'])) ?></p>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="text-center py-4 text-muted">
        <i class="bi bi-clipboard-x fs-1"></i>
        <p class="mt-2">No medical records found for this pet</p>
    </div>
<?php endif; ?>