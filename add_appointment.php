<?php
require_once 'config/database.php';
require_once 'classes/Patient.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();

$patientObj = new Patient($db);
$patients_stmt = $patientObj->readAll();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id       = trim($_POST['patient_id'] ?? '');
    $appointment_date = trim($_POST['appointment_date'] ?? '');
    $notes            = trim($_POST['notes'] ?? '');

    if (!empty($patient_id) && !empty($appointment_date)) {
        $query = "INSERT INTO appointments (patient_id, appointment_date, status, notes) VALUES (:patient_id, :appointment_date, 'Pending', :notes)";
        $stmt  = $db->prepare($query);

        $stmt->bindParam(':patient_id', $patient_id, PDO::PARAM_INT);
        $stmt->bindParam(':appointment_date', $appointment_date);
        $stmt->bindParam(':notes', $notes);

        if ($stmt->execute()) {
            header("Location: appointments.php?status=success");
            exit;
        } else {
            $message = "Failed to schedule appointment. Please try again.";
        }
    } else {
        $message = "Please select a patient and provide a date/time.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Appointment - SPCT Clinic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body class="bg-light">

    <div class="container mt-5" style="max-width: 600px;">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Schedule New Appointment</h4>
            </div>
            <div class="card-body">
                <?php if ($message): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

                <form action="add_appointment.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Select Patient</label>
                        <select name="patient_id" class="form-select" required>
                            <option value="">-- Choose Patient --</option>
                            <?php while ($p = $patients_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?> (<?= htmlspecialchars($p['phone']) ?>)</option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Appointment Date & Time</label>
                        <input type="datetime-local" name="appointment_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes / Symptoms</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Enter reason for visit..."></textarea>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="appointments.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Appointment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>