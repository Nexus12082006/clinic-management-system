<?php
class User {
    protected $db;
    protected $id;
    protected $username;
    protected $role;

    public function __construct($db) {
        $this->db = $db;
    }

    public function login($username, $password) {
        $query = "SELECT * FROM users WHERE username = :username LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Plain-text verification per your current system setup
            if($password === $row['password']) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                return true;
            }
        }
        return false;
    }
}
?>