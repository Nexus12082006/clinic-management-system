<div class="mb-4">
    <h2>My Medical Workspace</h2>
    <p class="text-muted">Track your check-up status updates and view prescribed medicines from your doctor.</p>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white py-3">
        <h5 class="mb-0 fw-bold"><i class="bi bi-capsule"></i> Prescribed Medicines & Instructions</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Prescribed By</th>
                        <th>Medicines to Take</th>
                        <th>Notes / Instructions</th>
                        <th>Date Issued</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($prescriptions)): ?>
                        <?php foreach ($prescriptions as $rx): ?>
                            <tr>
                                <td class="fw-bold">Dr. <?= htmlspecialchars($rx['doctor_name'] ?? 'Unknown') ?></td>
                                <td><pre class="mb-0" style="font-family: inherit; white-space: pre-wrap;"><?= htmlspecialchars($rx['medicines']) ?></pre></td>
                                <td><?= htmlspecialchars($rx['notes'] ?? 'None') ?></td>
                                <td><?= htmlspecialchars($rx['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No prescriptions issued yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">My Check-up Status History</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th># Reference</th>
                        <th>Appointment Date / Time</th>
                        <th>Current Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($appointments)): ?>
                        <?php foreach ($appointments as $apt): ?>
                            <tr>
                                <td><?= $apt['id'] ?></td>
                                <td><?= htmlspecialchars($apt['appointment_date'] ?? 'N/A') ?></td>
                                <td>
                                    <?php if (($apt['status'] ?? 'Scheduled') === 'Completed'): ?>
                                        <span class="badge bg-success p-2">Check-up Completed / Done</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark p-2">Scheduled / Pending</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">You have no scheduled appointments yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>