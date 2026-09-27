<?php
require_once 'config/database.php';
require_once 'config/auth.php';
require_role('Doctor');

$database = new Database();
$db = $database->getConnection();
$error = '';
$success = '';

$patients_stmt = $db->query("SELECT id, name FROM patients ORDER BY name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = trim($_POST['patient_id'] ?? '');
    $medicines  = trim($_POST['medicines'] ?? '');
    $notes      = trim($_POST['notes'] ?? '');
    $doctor_name = $_SESSION['username'];

    if (!empty($patient_id) && !empty($medicines)) {
        try {
            $stmt = $db->prepare("INSERT INTO prescriptions (patient_id, doctor_name, medicines, notes) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$patient_id, $doctor_name, $medicines, $notes])) {
                $success = "Prescription list successfully issued to patient!";
            } else {
                $error = "Failed to issue prescription.";
            }
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    } else {
        $error = "Please select a patient and list the medicines to take.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue Prescription - Doctor Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="doctor_dashboard.php">Doctor Portal</a>
            <div class="navbar-nav ms-auto">
                <a class="btn btn-outline-light btn-sm px-3" href="doctor_dashboard.php">Back to Dashboard</a>
            </div>
        </div>
    </nav>

    <div class="container" style="max-width: 600px;">
        <div class="card shadow-sm p-4">
            <h3 class="fw-bold mb-3 text-primary">Prescribe Medicine List</h3>
            <p class="text-muted">Specify the medicines and instructions for the patient to take.</p>

            <?php if ($error): ?>
                <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success py-2"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form action="add_prescription.php" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">Select Patient</label>
                    <select name="patient_id" class="form-select" required>
                        <option value="">-- Choose Patient --</option>
                        <?php while ($p = $patients_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">List of Medicines to Take</label>
                    <textarea name="medicines" class="form-control" rows="4" required placeholder="e.g., 1. Paracetamol 500mg - 1 tablet every 6 hours&#10;2. Amoxicillin 250mg - 1 capsule 3x a day"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Additional Clinical Instructions / Notes</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="e.g., Drink plenty of water, rest for 3 days."></textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2">Save & Issue Prescription</button>
            </form>
        </div>
    </div>

</body>
</html>