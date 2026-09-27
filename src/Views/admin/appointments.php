<div class="mb-4">
    <h2>Appointments Directory</h2>
    <p class="text-muted">View and manage all scheduled appointments.</p>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Patient Name</th>
                    <th>Contact</th>
                    <th>Date & Time</th>
                    <th>Status</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $appointment): ?>
                    <tr>
                        <td><?= htmlspecialchars($appointment['id']) ?></td>
                        <td><strong><?= htmlspecialchars($appointment['patient_name']) ?></strong></td>
                        <td><?= htmlspecialchars($appointment['phone']) ?></td>
                        <td><?= date('M d, Y - h:i A', strtotime($appointment['appointment_date'])) ?></td>
                        <td>
                            <span class="badge bg-<?= $appointment['status'] === 'Completed' ? 'success' : 'warning text-dark' ?>">
                                <?= htmlspecialchars($appointment['status']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($appointment['notes'] ?? '') ?></td>
                        <td>
                            <?php if ($appointment['status'] !== 'Completed'): ?>
                                <a href="<?= $base_url ?>/admin/appointments/<?= $appointment['id'] ?>/complete" class="btn btn-success btn-sm">
                                    Mark Done
                                </a>
                            <?php else: ?>
                                <span class="text-muted small">Finished</span>
                            <?php endif; ?>
                            <a href="<?= $base_url ?>/admin/appointments/<?= $appointment['id'] ?>/delete" class="btn btn-danger btn-sm ms-1" onclick="return confirm('Delete this appointment?');">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>