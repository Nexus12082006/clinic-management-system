<?php
class Patient {
    private $db;
    private $table_name = "patients";

    public function __construct($db) {
        $this->db = $db;
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function create($name, $email, $phone, $address) {
        $query = "INSERT INTO " . $this->table_name . " (name, email, phone, address) VALUES (:name, :email, :phone, :address)";
        $stmt = $this->db->prepare($query);

        $email_val = !empty($email) ? $email : '';
        $address_val = !empty($address) ? $address : '';

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email_val);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address_val);

        return $stmt->execute();
    }
}
?>