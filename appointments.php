<?php
require_once 'config/database.php';
require_once 'classes/Appointment.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();

$appointment = new Appointment($db);
$stmt = $appointment->readAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments - Clinic Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">Clinic Management</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="dashboard.php">Dashboard</a>
                <a class="nav-link" href="patients.php">Patients</a>
                <a class="nav-link active" href="appointments.php">Appointments</a>
                <a class="nav-link text-warning" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="brand-title">Appointments Directory</h2>
            <div>
                <a href="add_appointment.php" class="btn btn-primary me-2"><i class="bi bi-calendar-plus"></i> New Appointment</a>
                <a href="patients.php" class="btn btn-outline-secondary"><i class="bi bi-people"></i> View Patients</a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Patient Name</th>
                            <th>Contact</th>
                            <th>Date & Time</th>
                            <th>Status</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($stmt && $stmt->rowCount() > 0): ?>
                            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['id']) ?></td>
                                    <td><strong><?= htmlspecialchars($row['patient_name']) ?></strong></td>
                                    <td><?= htmlspecialchars($row['phone']) ?></td>
                                    <td><?= date('M d, Y - h:i A', strtotime($row['appointment_date'])) ?></td>
                                    <td>
                                        <span class="badge bg-warning text-dark"><?= htmlspecialchars($row['status']) ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($row['notes']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">No appointments scheduled.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>assets/js/app.js"></script>
</body>
</html>