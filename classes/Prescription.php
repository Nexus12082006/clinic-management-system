<?php
class Prescription {
    private $db;
    private $table_name = "prescriptions";

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($patient_id, $medication_list, $dosage_duration, $issue_date) {
        $query = "INSERT INTO " . $this->table_name . " (patient_id, medication_list, dosage_duration, issue_date) VALUES (:patient_id, :medication_list, :dosage_duration, :issue_date)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->bindParam(':medication_list', $medication_list);
        $stmt->bindParam(':dosage_duration', $dosage_duration);
        $stmt->bindParam(':issue_date', $issue_date);
        return $stmt->execute();
    }

    public function getByPatient($patient_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE patient_id = :patient_id ORDER BY issue_date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->execute();
        return $stmt;
    }
}
?>