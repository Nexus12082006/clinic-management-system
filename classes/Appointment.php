<?php
class Appointment {
    private $db;
    private $table_name = "appointments";

    public function __construct($db) {
        $this->db = $db;
    }

    public function readAll() {
        $query = "SELECT a.*, p.name as patient_name, p.phone FROM " . $this->table_name . " a 
                  LEFT JOIN patients p ON a.patient_id = p.id 
                  ORDER BY a.appointment_date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>