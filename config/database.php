<?php
class Database {
    private $host = "localhost";
    private $db_name = "gestion_users";
    private $username = "root";
    private $password = "";

    public function connect() {
        try {
            return new PDO(
                "mysql:host=$this->host;dbname=$this->db_name",
                $this->username,
                $this->password
            );
        } catch(PDOException $e) {
            die("Erreur DB: " . $e->getMessage());
        }
    }
}