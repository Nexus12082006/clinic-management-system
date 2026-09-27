<?php
require_once 'config/database.php';
require_once 'config/auth.php';
require_role('Receptionist');

$database = new Database();
$db = $database->getConnection();

if (isset($_GET['complete_id'])) {
    $apt_id = $_GET['complete_id'];
    $stmt = $db->prepare("UPDATE appointments SET status = 'Completed' WHERE id = ?");
    $stmt->execute([$apt_id]);
    header("Location: receptionist_dashboard.php");
    exit;
}

$patient_count = $db->query("SELECT COUNT(*) FROM patients")->fetchColumn();
$appointment_count = $db->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
$doctors_stmt = $db->query("SELECT id, username FROM users WHERE role = 'Doctor'");
$medicines_stmt = $db->query("SELECT * FROM medicines ORDER BY name ASC");
$appointments_query = "SELECT a.*, p.name as patient_name FROM appointments a JOIN patients p ON a.patient_id = p.id ORDER BY a.id DESC LIMIT 5";
$appointments_stmt = $db->query($appointments_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receptionist Portal - Clinic Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Reception Portal</a>
            <div class="navbar-nav ms-auto">
                <span class="nav-link text-light me-3">Staff: <?= htmlspecialchars($_SESSION['username']); ?></span>
                <a class="btn btn-outline-light btn-sm px-3" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="mb-4">
            <h2>Front Desk Workspace</h2>
            <p class="text-muted">Register patients, coordinate appointments, and check doctor/medicine availability.</p>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm border-start border-success border-4">
                    <div class="card-body">
                        <h6 class="text-muted">Total Registered Patients</h6>
                        <h3 class="fw-bold"><?= $patient_count ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm border-start border-primary border-4">
                    <div class="card-body">
                        <h6 class="text-muted">Total Scheduled Appointments</h6>
                        <h3 class="fw-bold"><?= $appointment_count ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body bg-white rounded-3 p-4">
                <h5 class="card-title fw-bold text-dark mb-3">Front Desk Actions</h5>
                <div class="d-flex flex-wrap gap-2">
                    <a href="add_patient.php" class="btn btn-success px-4 py-2 shadow-sm">+ Register New Patient</a>
                    <a href="add_appointment.php" class="btn btn-primary px-4 py-2">+ Schedule Appointment</a>
                    <a href="patients.php" class="btn btn-outline-secondary px-4 py-2">Patient Directory</a>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">🩺 Available Doctors</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr><th>Doctor Name</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                <?php while ($doc = $doctors_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                    <tr>
                                        <td class="fw-bold">Dr. <?= htmlspecialchars($doc['username']) ?></td>
                                        <td><span class="badge bg-success">On Duty</span></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">💊 Medicine Inventory</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr><th>Medicine Name</th><th>Stock</th></tr>
                            </thead>
                            <tbody>
                                <?php while ($med = $medicines_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                    <tr>
                                        <td class="fw-bold"><?= htmlspecialchars($med['name']) ?></td>
                                        <td><span class="badge bg-<?= $med['stock'] > 10 ? 'success' : 'danger' ?>"><?= $med['stock'] ?> units</span></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Appointment Status & Management</h5>
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
                                                <span class="badge bg-warning text-dark">Scheduled</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (($row['status'] ?? 'Scheduled') !== 'Completed'): ?>
                                                <a href="receptionist_dashboard.php?complete_id=<?= $row['id'] ?>" class="btn btn-success btn-sm">Mark Done</a>
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