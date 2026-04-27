<?php
require_once __DIR__ . "/../config/database.php";

class User {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
        $this->ensureResetColumns();
    }

    private function ensureResetColumns() {
        $checks = [
            'reset_token' => "ALTER TABLE users ADD COLUMN reset_token VARCHAR(255) NULL",
            'reset_expires_at' => "ALTER TABLE users ADD COLUMN reset_expires_at DATETIME NULL"
        ];

        foreach ($checks as $column => $sql) {
            $stmt = $this->conn->prepare("SHOW COLUMNS FROM users LIKE :column_name");
            $stmt->execute([':column_name' => $column]);
            if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->conn->exec($sql);
            }
        }
    }

    public function register($fullname, $email, $password) {
        $check = $this->conn->prepare("SELECT id FROM users WHERE email = :email");
        $check->execute([':email' => $email]);
        if($check->fetch()) {
            return false;
        }
        
        $sql = "INSERT INTO users (fullname, email, password, role)
                VALUES (:fullname, :email, :password, 'user')";
        $stmt = $this->conn->prepare($sql);

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        return $stmt->execute([
            ':fullname' => $fullname,
            ':email' => $email,
            ':password' => $hashed
        ]);
    }

    public function login($email, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email=:email");
        $stmt->execute([':email' => $email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && isset($user['password']) && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function getUsers() {
        return $this->conn->query("SELECT * FROM users ORDER BY id DESC");
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id=:id");
        return $stmt->execute([':id' => $id]);
    }

    public function createResetToken($email) {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return false;
        }

        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + 3600);

        $update = $this->conn->prepare(
            "UPDATE users SET reset_token = :token, reset_expires_at = :expires_at WHERE id = :id"
        );
        $ok = $update->execute([
            ':token' => hash('sha256', $token),
            ':expires_at' => $expiresAt,
            ':id' => $user['id']
        ]);

        if (!$ok) {
            return false;
        }

        return $token;
    }

    public function validateResetToken($token) {
        $stmt = $this->conn->prepare(
            "SELECT id FROM users WHERE reset_token = :token AND reset_expires_at >= NOW()"
        );
        $stmt->execute([':token' => hash('sha256', $token)]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePasswordWithToken($token, $newPassword) {
        $user = $this->validateResetToken($token);
        if (!$user) {
            return false;
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare(
            "UPDATE users
             SET password = :password, reset_token = NULL, reset_expires_at = NULL
             WHERE id = :id"
        );
        return $stmt->execute([
            ':password' => $hashedPassword,
            ':id' => $user['id']
        ]);
    }

    // NOUVELLE METHODE
    public function changeRole($userId, $role) {
        $sql = "UPDATE users SET role = :role WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':id' => $userId,
            ':role' => $role
        ]);
    }
}
?>