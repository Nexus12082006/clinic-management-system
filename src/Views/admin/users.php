<div class="mb-4">
    <h2>User Management</h2>
    <p class="text-muted">Manage all system users and their roles.</p>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <a href="<?= $base_url ?>/register" class="btn btn-dark mb-3">+ Create New User Account</a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">All Users</h5>
        <span class="badge bg-secondary">Total: <?= count($users) ?> Accounts</span>
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
                                <?php if ($user['id'] != ($user ?? ['id' => 0])['id']): ?>
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