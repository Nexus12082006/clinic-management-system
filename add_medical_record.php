<?php
require_once 'config/database.php';
require_once 'classes/MedicalRecord.php';
require_once 'classes/Prescription.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();

$message = '';
$selected_patient_id = $_GET['patient_id'] ?? '';

// Fetch patient list for dropdown selection
$patients_stmt = $db->query("SELECT id, name FROM patients ORDER BY name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id      = $_POST['patient_id'] ?? '';
    $diagnosis       = trim($_POST['diagnosis'] ?? '');
    $treatment_notes = trim($_POST['treatment_notes'] ?? '');
    
    // Prescription fields
    $medication_list = trim($_POST['medication_list'] ?? '');
    $dosage_duration = trim($_POST['dosage_duration'] ?? '');
    $issue_date      = date('Y-m-d');

    if (!empty($patient_id) && !empty($diagnosis)) {
        // Save Medical Record
        $medicalRecord = new MedicalRecord($db);
        $recordSaved = $medicalRecord->create($patient_id, $diagnosis, $treatment_notes);

        // Save Prescription if provided
        $prescriptionSaved = true;
        if (!empty($medication_list)) {
            $prescription = new Prescription($db);
            $prescriptionSaved = $prescription->create($patient_id, $medication_list, $dosage_duration, $issue_date);
        }

        if ($recordSaved && $prescriptionSaved) {
            header("Location: doctor_dashboard.php?status=success");
            exit;
        } else {
            $message = "Failed to save medical record. Please try again.";
        }
    } else {
        $message = "Please choose a patient and fill in the diagnosis.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Medical Record - Clinic Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body class="bg-light">

    <div class="container mt-5" style="max-width: 700px;">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">Issue Medical Record & Prescription</h4>
            </div>
            <div class="card-body">
                <?php if ($message): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

                <form action="add_medical_record.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Select Patient <span class="text-danger">*</span></label>
                        <select name="patient_id" class="form-select" required>
                            <option value="">-- Choose Patient --</option>
                            <?php while ($p = $patients_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                <option value="<?= $p['id'] ?>" <?= ($selected_patient_id == $p['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($p['name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <hr class="my-4">
                    <h5 class="text-success mb-3">Medical Record Details</h5>

                    <div class="mb-3">
                        <label class="form-label">Diagnosis <span class="text-danger">*</span></label>
                        <textarea name="diagnosis" class="form-control" rows="3" required placeholder="Enter primary diagnosis..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Treatment Notes</label>
                        <textarea name="treatment_notes" class="form-control" rows="3" placeholder="Enter instructions or examination comments..."></textarea>
                    </div>

                    <hr class="my-4">
                    <h5 class="text-success mb-3">Prescription Details (Optional)</h5>

                    <div class="mb-3">
                        <label class="form-label">Medication List</label>
                        <input type="text" name="medication_list" class="form-control" placeholder="e.g., Paracetamol 500mg, Amoxicillin">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dosage & Duration</label>
                        <input type="text" name="dosage_duration" class="form-control" placeholder="e.g., 1 tablet every 8 hours for 7 days">
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="doctor_dashboard.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-success">Save Clinical Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>