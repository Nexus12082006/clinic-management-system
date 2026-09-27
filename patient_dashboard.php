<?php
require_once 'config/database.php';
require_once 'config/auth.php';
require_role('Patient');

$database = new Database();
$db = $database->getConnection();
$username = $_SESSION['username'];

$pat_stmt = $db->prepare("SELECT id FROM patients WHERE name = ? LIMIT 1");
$pat_stmt->execute([$username]);
$patient = $pat_stmt->fetch(PDO::FETCH_ASSOC);
$patient_id = $patient['id'] ?? 0;

$appointments_stmt = $db->prepare("SELECT * FROM appointments WHERE patient_id = ? ORDER BY id DESC");
$appointments_stmt->execute([$patient_id]);

$prescriptions_stmt = $db->prepare("SELECT * FROM prescriptions WHERE patient_id = ? ORDER BY id DESC");
$prescriptions_stmt->execute([$patient_id]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Portal - Clinic Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-info shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold text-dark" href="#">Patient Portal</a>
            <div class="navbar-nav ms-auto">
                <span class="nav-link text-dark me-3">Welcome, <?= htmlspecialchars($username); ?></span>
                <a class="btn btn-outline-dark btn-sm px-3" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="mb-4">
            <h2>My Medical Workspace</h2>
            <p class="text-muted">Track your check-up status updates and view prescribed medicines from your doctor.</p>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0 fw-bold">💊 Prescribed Medicines & Instructions</h5>
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
                            <?php if ($prescriptions_stmt->rowCount() > 0): ?>
                                <?php while ($rx = $prescriptions_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                    <tr>
                                        <td class="fw-bold">Dr. <?= htmlspecialchars($rx['doctor_name']) ?></td>
                                        <td><pre class="mb-0" style="font-family: inherit; white-space: pre-wrap;"><?= htmlspecialchars($rx['medicines']) ?></pre></td>
                                        <td><?= htmlspecialchars($rx['notes'] ?? 'None') ?></td>
                                        <td><?= htmlspecialchars($rx['created_at']) ?></td>
                                    </tr>
                                <?php endwhile; ?>
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
                            <?php if ($appointments_stmt->rowCount() > 0): ?>
                                <?php while ($apt = $appointments_stmt->fetch(PDO::FETCH_ASSOC)): ?>
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
                                <?php endwhile; ?>
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
    </div>

</body>
</html>