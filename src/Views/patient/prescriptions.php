<div class="mb-4">
    <h2>My Prescriptions</h2>
    <p class="text-muted">View all your prescribed medications.</p>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Date Issued</th>
                    <th>Prescribed By</th>
                    <th>Medicines</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($prescriptions)): ?>
                    <?php foreach ($prescriptions as $rx): ?>
                        <tr>
                            <td><?= $rx['id'] ?></td>
                            <td><?= date('M d, Y', strtotime($rx['created_at'])) ?></td>
                            <td>Dr. <?= htmlspecialchars($rx['doctor_name'] ?? 'Unknown') ?></td>
                            <td><pre class="mb-0" style="font-family: inherit; white-space: pre-wrap;"><?= htmlspecialchars($rx['medicines']) ?></pre></td>
                            <td><?= htmlspecialchars($rx['notes'] ?? 'None') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No prescriptions found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>