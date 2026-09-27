<div class="mb-4">
    <h2>All Appointments</h2>
    <p class="text-muted">View and manage all patient appointments.</p>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Patient Name</th>
                    <th>Date / Time</th>
                    <th>Status</th>
                    <th>Notes</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($appointments)): ?>
                    <?php foreach ($appointments as $row): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($row['patient_name']) ?></td>
                            <td><?= htmlspecialchars($row['appointment_date'] ?? 'N/A') ?></td>
                            <td>
                                <?php if (($row['status'] ?? 'Scheduled') === 'Completed'): ?>
                                    <span class="badge bg-success">Completed</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Scheduled / In Progress</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['notes'] ?? '') ?></td>
                            <td>
                                <?php if (($row['status'] ?? 'Scheduled') !== 'Completed'): ?>
                                    <a href="<?= $base_url ?>/doctor/appointments/<?= $row['id'] ?>/complete" class="btn btn-success btn-sm">Mark Done</a>
                                <?php else: ?>
                                    <span class="text-muted small">Finished</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No appointments found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>