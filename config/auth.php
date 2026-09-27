<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_role($allowed_roles) {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        header("Location: login.php");
        exit;
    }

    if (is_string($allowed_roles)) {
        $allowed_roles = [$allowed_roles];
    }

    if (!in_array($_SESSION['role'], $allowed_roles)) {
        switch ($_SESSION['role']) {
            case 'Doctor':
                header("Location: doctor_dashboard.php");
                break;
            case 'Receptionist':
                header("Location: receptionist_dashboard.php");
                break;
            case 'Patient':
                header("Location: patient_dashboard.php");
                break;
            default:
                header("Location: dashboard.php");
                break;
        }
        exit;
    }
}
?>