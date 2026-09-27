<?php
require_once 'config/database.php';
require_once 'config/auth.php';
require_role('Administrator'); // Locks this page strictly to Administrators

$database = new Database();
$db = $database->getConnection();

// Handle user deletion if requested by admin
if (isset($_GET['delete_user'])) {
    $del_id = $_GET['delete_user'];
    // Prevent admin from deleting themselves
    if ($del_id != $_SESSION['user_id']) {
        $del_stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $del_stmt->execute([$del_id]);
    }
    header("Location: dashboard.php");
    exit;
}

// Fetch system overview metrics
$total_users = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_doctors = $db->query("SELECT COUNT(*) FROM users WHERE role = 'Doctor'")->fetchColumn();
$total_receptionists = $db->query("SELECT COUNT(*) FROM users WHERE role = 'Receptionist'")->fetchColumn();
$total_patients = $db->query("SELECT COUNT(*) FROM users WHERE role = 'Patient'")->fetchColumn();
$total_appointments = $db->query("SELECT COUNT(*) FROM appointments")->fetchColumn();

// Fetch all system users for management
$users_stmt = $db->query("SELECT id, username, role FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Control Panel - Clinic Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">⚙️ Admin Master Control</a>
            <div class="navbar-nav ms-auto">
                <span class="nav-link text-warning me-3">Admin: <?= htmlspecialchars($_SESSION['username']); ?></span>
                <a class="btn btn-outline-light btn-sm px-3" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Welcome Section -->
        <div class="mb-4">
            <h2>System Administration & Upgrades</h2>
            <p class="text-muted">Full-privilege overview of clinic users, roles, system maintenance, and data pipelines.</p>
        </div>

        <!-- Metrics Row -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm border-start border-dark border-4">
                    <div class="card-body">
                        <h6 class="text-muted">Total System Users</h6>
                        <h3 class="fw-bold"><?= $total_users ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-start border-primary border-4">
                    <div class="card-body">
                        <h6 class="text-muted">Doctors Active</h6>
                        <h3 class="fw-bold"><?= $total_doctors ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-start border-success border-4">
                    <div class="card-body">
                        <h6 class="text-muted">Receptionists Active</h6>
                        <h3 class="fw-bold"><?= $total_receptionists ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-start border-warning border-4">
                    <div class="card-body">
                        <h6 class="text-muted">Appointments Booked</h6>
                        <h3 class="fw-bold"><?= $total_appointments ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Management Shortcuts -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-3">System Management Tools</h5>
                <a href="register.php" class="btn btn-dark me-2 mb-2">+ Create New User Account</a>
                <a href="patients.php" class="btn btn-outline-secondary me-2 mb-2">Manage All Patients</a>
                <a href="appointments.php" class="btn btn-outline-secondary me-2 mb-2">Manage All Appointments</a>
            </div>
        </div>

        <!-- User Accounts Management Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">User Access & Role Management</h5>
                <span class="badge bg-secondary">Total: <?= $total_users ?> Accounts</span>
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
                            <?php while ($user = $users_stmt->fetch(PDO::FETCH_ASSOC)): ?>
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
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <a href="dashboard.php?delete_user=<?= $user['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this account?');">Revoke / Delete</a>
                                        <?php else: ?>
                                            <span class="text-muted small">Current Session</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</body>
</html>