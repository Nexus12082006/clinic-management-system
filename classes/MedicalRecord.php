<?php
class MedicalRecord {
    private $db;
    private $table_name = "medical_records";

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($patient_id, $diagnosis, $treatment_notes) {
        $query = "INSERT INTO " . $this->table_name . " (patient_id, diagnosis, treatment_notes) VALUES (:patient_id, :diagnosis, :treatment_notes)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->bindParam(':diagnosis', $diagnosis);
        $stmt->bindParam(':treatment_notes', $treatment_notes);
        return $stmt->execute();
    }

    public function getByPatient($patient_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE patient_id = :patient_id ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->execute();
        return $stmt;
    }
}
?>