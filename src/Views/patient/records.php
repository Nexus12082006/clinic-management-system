<div class="mb-4">
    <h2>My Medical Records</h2>
    <p class="text-muted">View your medical history and records.</p>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Doctor</th>
                    <th>Diagnosis</th>
                    <th>Treatment Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($records)): ?>
                    <?php foreach ($records as $record): ?>
                        <tr>
                            <td><?= $record['id'] ?></td>
                            <td><?= date('M d, Y', strtotime($record['created_at'])) ?></td>
                            <td>Dr. <?= htmlspecialchars($record['doctor_name'] ?? 'Unknown') ?></td>
                            <td><?= htmlspecialchars($record['diagnosis']) ?></td>
                            <td><?= htmlspecialchars($record['treatment_notes'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No medical records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>