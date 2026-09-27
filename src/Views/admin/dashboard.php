<div class="mb-4">
    <h2>System Administration</h2>
    <p class="text-muted">Full-privilege overview of clinic users, roles, system maintenance, and data pipelines.</p>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-start border-dark border-4">
            <div class="card-body">
                <h6 class="text-muted">Total System Users</h6>
                <h3 class="fw-bold"><?= $stats['total'] ?? 0 ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-start border-primary border-4">
            <div class="card-body">
                <h6 class="text-muted">Doctors Active</h6>
                <h3 class="fw-bold"><?= $stats['Doctor'] ?? 0 ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-start border-success border-4">
            <div class="card-body">
                <h6 class="text-muted">Receptionists Active</h6>
                <h3 class="fw-bold"><?= $stats['Receptionist'] ?? 0 ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-start border-warning border-4">
            <div class="card-body">
                <h6 class="text-muted">Appointments Booked</h6>
                <h3 class="fw-bold"><?= $appointmentStats['total'] ?? 0 ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title fw-bold mb-3">System Management Tools</h5>
        <a href="<?= $base_url ?>/register" class="btn btn-dark me-2 mb-2">+ Create New User Account</a>
        <a href="<?= $base_url ?>/admin/patients" class="btn btn-outline-secondary me-2 mb-2">Manage All Patients</a>
        <a href="<?= $base_url ?>/admin/appointments" class="btn btn-outline-secondary me-2 mb-2">Manage All Appointments</a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">User Access & Role Management</h5>
        <span class="badge bg-secondary">Total: <?= $stats['total'] ?? 0 ?> Accounts</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th># ID</th>
                        <th>Username</th>
                        <th>Assigned Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($user['username']) ?></td>
                            <td>
                                <?php 
                                    $badge_color = 'bg-secondary';
                                    if ($user['role'] === 'Administrator') $badge_color = 'bg-dark';
                                    elseif ($user['role'] === 'Doctor') $badge_color = 'bg-primary';
                                    elseif ($user['role'] === 'Receptionist') $badge_color = 'bg-success';
                                    elseif ($user['role'] === 'Patient') $badge_color = 'bg-info text-dark';
                                ?>
                                <span class="badge <?= $badge_color ?>"><?= htmlspecialchars($user['role']) ?></span>
                            </td>
                            <td>
                                <?php if ($user['id'] != $user['id'] ?? 0 && $user['id'] != ($user ?? ['id' => 0])['id']): ?>
                                    <form action="<?= $base_url ?>/admin/users/<?= $user['id'] ?>" method="POST" style="display: inline;">
                                        <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?? '' ?>">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this account?');">
                                            Revoke / Delete
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted small">Current Session</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>