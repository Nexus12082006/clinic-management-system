<?php
require_once __DIR__ . '/User.php';

class Administrator extends User {

    // Fetch system-wide summary metrics for the dashboard
    public function getDashboardStats() {
        $stats = [];

        // Count Total Patients
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM patients");
        $stats['total_patients'] = $stmt->fetch()['total'] ?? 0;

        // Count Total Appointments
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM appointments");
        $stats['total_appointments'] = $stmt->fetch()['total'] ?? 0;

        // Count Pending Appointments
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM appointments WHERE status = 'Pending'");
        $stats['pending_appointments'] = $stmt->fetch()['total'] ?? 0;

        // Count Total Staff
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM users WHERE role != 'Administrator'");
        $stats['total_staff'] = $stmt->fetch()['total'] ?? 0;

        return $stats;
    }
}
?>