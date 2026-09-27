<?php
require_once 'config/database.php';
session_start();

$database = new Database();
$db = $database->getConnection();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role     = trim($_POST['role'] ?? '');

    if (!empty($username) && !empty($password) && !empty($role)) {
        try {
            $check = $db->prepare("SELECT id FROM users WHERE username = ?");
            $check->execute([$username]);

            if ($check->rowCount() > 0) {
                $error = "Username is already taken. Choose another one.";
            } else {
                $stmt = $db->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
                if ($stmt->execute([$username, $password, $role])) {
                    $success = "Account registered successfully! You can now login.";
                } else {
                    $error = "Failed to execute insert statement.";
                }
            }
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Clinic Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">

    <div class="card shadow-sm p-4" style="width: 100%; max-width: 400px;">
        <h3 class="text-center mb-4 fw-bold text-success">Create Account</h3>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success py-2"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Select Role</label>
                <select name="role" class="form-select" required>
                    <option value="">-- Choose Role --</option>
                    <option value="Administrator">Administrator</option>
                    <option value="Doctor">Doctor</option>
                    <option value="Receptionist">Receptionist</option>
                    <option value="Patient">Patient</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required placeholder="Choose a username">
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required placeholder="Choose a password">
            </div>
            <button type="submit" class="btn btn-success w-100 py-2 mb-3">Register Account</button>
            <div class="text-center">
                <a href="login.php" class="text-decoration-none">Already have an account? Login</a>
            </div>
        </form>
    </div>

</body>
</html>