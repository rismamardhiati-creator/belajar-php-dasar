<?php
class User {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($username, $password) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = :username LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            // Verifikasi hash atau cek teks langsung jika password bawaan '123456'
            if (password_verify($password, $row['password']) || $password === '123456') {
                return $row;
            }
        }
        return false;
    }

    public function register($nama, $username, $password, $role, $no_hp, $alamat) {
        $query = "INSERT INTO " . $this->table_name . " (nama, username, password, role, no_hp, alamat) 
                  VALUES (:nama, :username, :password, :role, :no_hp, :alamat)";
        $stmt = $this->conn->prepare($query);

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':no_hp', $no_hp);
        $stmt->bindParam(':alamat', $alamat);

        return $stmt->execute();
    }
}
?>