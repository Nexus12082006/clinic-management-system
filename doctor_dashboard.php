<?php
require_once 'config/database.php';
require_once 'config/auth.php';
require_role('Doctor');

$database = new Database();
$db = $database->getConnection();

if (isset($_GET['complete_id'])) {
    $apt_id = $_GET['complete_id'];
    $stmt = $db->prepare("UPDATE appointments SET status = 'Completed' WHERE id = ?");
    $stmt->execute([$apt_id]);
    header("Location: doctor_dashboard.php");
    exit;
}

$patient_count = $db->query("SELECT COUNT(*) FROM patients")->fetchColumn();
$appointment_count = $db->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
$record_count = $db->query("SELECT COUNT(*) FROM medical_records")->fetchColumn();

$appointments_query = "SELECT a.*, p.name as patient_name FROM appointments a JOIN patients p ON a.patient_id = p.id ORDER BY a.id DESC LIMIT 5";
$appointments_stmt = $db->query($appointments_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Portal - Clinic Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Doctor Portal</a>
            <div class="navbar-nav ms-auto">
                <span class="nav-link text-light me-3">Dr. <?= htmlspecialchars($_SESSION['username']); ?></span>
                <a class="btn btn-outline-light btn-sm px-3" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="mb-4">
            <h2>Doctor Workspace</h2>
            <p class="text-muted">Manage patient diagnoses, view clinical schedules, and file medical notes.</p>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-start border-primary border-4">
                    <div class="card-body">
                        <h6 class="text-muted">Total Registered Patients</h6>
                        <h3 class="fw-bold"><?= $patient_count ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-start border-warning border-4">
                    <div class="card-body">
                        <h6 class="text-muted">Total Appointments</h6>
                        <h3 class="fw-bold"><?= $appointment_count ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-start border-success border-4">
                    <div class="card-body">
                        <h6 class="text-muted">Medical Records Filed</h6>
                        <h3 class="fw-bold"><?= $record_count ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body bg-white rounded-3 p-4">
                <h5 class="card-title fw-bold text-dark mb-3">Clinical Quick Actions</h5>
                <div class="d-flex flex-wrap gap-2">
                    <a href="add_prescription.php" class="btn btn-primary px-4 py-2 shadow-sm">+ Prescribe Medicines</a>
                    <a href="add_medical_record.php" class="btn btn-outline-primary px-4 py-2">+ New Medical Record</a>
                    <a href="patients.php" class="btn btn-outline-secondary px-4 py-2">View Patient Directory</a>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Patient Check-up Queue</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Patient Name</th>
                                <th>Date / Time</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($appointments_stmt->rowCount() > 0): ?>
                                <?php while ($row = $appointments_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                    <tr>
                                        <td><?= $row['id'] ?></td>
                                        <td class="fw-bold"><?= htmlspecialchars($row['patient_name']) ?></td>
                                        <td><?= htmlspecialchars($row['appointment_date'] ?? 'N/A') ?></td>
                                        <td>
                                            <?php if (($row['status'] ?? 'Scheduled') === 'Completed'): ?>
                                                <span class="badge bg-success">Completed</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Scheduled / In Progress</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (($row['status'] ?? 'Scheduled') !== 'Completed'): ?>
                                                <a href="doctor_dashboard.php?complete_id=<?= $row['id'] ?>" class="btn btn-success btn-sm">Mark Done</a>
                                            <?php else: ?>
                                                <span class="text-muted small">Finished</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No appointments found.</td>
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