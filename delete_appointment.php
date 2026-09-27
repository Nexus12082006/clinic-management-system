<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/database.php';

if (isset($_GET['id'])) {
    $db = (new Database())->getConnection();
    $id = (int)$_GET['id'];

    try {
        // Try deleting using 'id'
        $stmt = $db->prepare("DELETE FROM appointments WHERE id = ?");
        $stmt->execute([$id]);
    } catch (PDOException $e) {
        // Fallback to 'appointment_id' if 'id' column doesn't exist
        $stmt = $db->prepare("DELETE FROM appointments WHERE appointment_id = ?");
        $stmt->execute([$id]);
    }
}

header("Location: appointments.php");
exit();
?>