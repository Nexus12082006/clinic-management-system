<?php
require_once 'config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if (!empty($name) && !empty($phone)) {
        $email_val   = !empty($email) ? $email : '';
        $address_val = !empty($address) ? $address : '';

        $query = "INSERT INTO patients (name, email, phone, address) VALUES (:name, :email, :phone, :address)";
        $stmt  = $db->prepare($query);

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email_val);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address_val);

        if ($stmt->execute()) {
            header("Location: patients.php?status=success");
            exit;
        } else {
            $message = "Failed to add patient. Please try again.";
        }
    } else {
        $message = "Please fill in the required Name and Phone fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Patient - Clinic Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body class="bg-light">

    <div class="container mt-5" style="max-width: 600px;">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Add New Patient</h4>
            </div>
            <div class="card-body">
                <?php if ($message): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

                <form action="add_patient.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address <span class="text-muted">(Optional)</span></label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address <span class="text-muted">(Optional)</span></label>
                        <input type="text" name="address" class="form-control" placeholder="Enter residential address">
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="patients.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Patient</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>