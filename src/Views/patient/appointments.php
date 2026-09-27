<div class="mb-4">
    <h2>My Appointments</h2>
    <p class="text-muted">View all your scheduled and past appointments.</p>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Date & Time</th>
                    <th>Status</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($appointments)): ?>
                    <?php foreach ($appointments as $apt): ?>
                        <tr>
                            <td><?= $apt['id'] ?></td>
                            <td><?= date('M d, Y - h:i A', strtotime($apt['appointment_date'])) ?></td>
                            <td>
                                <?php if (($apt['status'] ?? 'Scheduled') === 'Completed'): ?>
                                    <span class="badge bg-success">Completed</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Scheduled</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($apt['notes'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">No appointments found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>