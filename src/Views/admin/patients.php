<div class="mb-4">
    <h2>Patient Directory</h2>
    <p class="text-muted">View and manage all registered patients.</p>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Appointments</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($patients as $patient): ?>
                    <tr>
                        <td><?= htmlspecialchars($patient['id']) ?></td>
                        <td><?= htmlspecialchars($patient['name']) ?></td>
                        <td><?= htmlspecialchars($patient['email'] !== '' ? $patient['email'] : 'N/A') ?></td>
                        <td><?= htmlspecialchars($patient['phone']) ?></td>
                        <td><?= htmlspecialchars(!empty($patient['address']) ? $patient['address'] : 'N/A') ?></td>
                        <td><?= $patient['appointment_count'] ?? 0 ?></td>
                        <td>
                            <a href="<?= $base_url ?>/admin/patients/<?= $patient['id'] ?>/delete" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this patient?');">
                                <i class="bi bi-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>