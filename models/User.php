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
            'reset_expires_at' => "ALTER TABLE users ADD COLUMN reset_expires_at DATETIME NULL",
            'secret_code' => "ALTER TABLE users ADD COLUMN secret_code VARCHAR(255) NULL"
        ];

        foreach ($checks as $column => $sql) {
            $stmt = $this->conn->query("SHOW COLUMNS FROM users LIKE " . $this->conn->quote($column));
            if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->conn->exec($sql);
            }
        }
    }

    public function register($fullname, $email, $password, $secretCode) {
        $check = $this->conn->prepare("SELECT id FROM users WHERE email = :email");
        $check->execute([':email' => $email]);
        if($check->fetch()) {
            return false;
        }
        
        $sql = "INSERT INTO users (fullname, email, password, secret_code, role)
                VALUES (:fullname, :email, :password, :secret_code, 'user')";
        $stmt = $this->conn->prepare($sql);

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $hashedSecretCode = password_hash($secretCode, PASSWORD_DEFAULT);

        return $stmt->execute([
            ':fullname' => $fullname,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':secret_code' => $hashedSecretCode
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

    public function getUsers($search = '', $letter = '', $roleFilter = '', $sortBy = 'id', $sortDir = 'DESC') {
        $allowedSortBy = ['id', 'fullname', 'email', 'role'];
        $allowedSortDir = ['ASC', 'DESC'];

        if (!in_array($sortBy, $allowedSortBy, true)) {
            $sortBy = 'id';
        }
        if (!in_array(strtoupper($sortDir), $allowedSortDir, true)) {
            $sortDir = 'DESC';
        } else {
            $sortDir = strtoupper($sortDir);
        }

        $sql = "SELECT * FROM users WHERE 1=1";
        $params = [];

        if ($search !== '') {
            $sql .= " AND (fullname LIKE :search OR email LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        if ($letter !== '' && preg_match('/^[A-Za-z]$/', $letter)) {
            $sql .= " AND fullname LIKE :letter";
            $params[':letter'] = strtoupper($letter) . '%';
        }

        if ($roleFilter === 'admin' || $roleFilter === 'user') {
            $sql .= " AND role = :role_filter";
            $params[':role_filter'] = $roleFilter;
        }

        if ($sortBy === 'role') {
            if ($sortDir === 'ASC') {
                $sql .= " ORDER BY CASE WHEN role = 'admin' THEN 1 WHEN role = 'user' THEN 2 ELSE 3 END ASC, id DESC";
            } else {
                $sql .= " ORDER BY CASE WHEN role = 'admin' THEN 1 WHEN role = 'user' THEN 2 ELSE 3 END DESC, id DESC";
            }
        } else {
            $sql .= " ORDER BY {$sortBy} {$sortDir}";
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createUserByAdmin($fullname, $email, $password, $secretCode, $role) {
        $check = $this->conn->prepare("SELECT id FROM users WHERE email = :email");
        $check->execute([':email' => $email]);
        if ($check->fetch()) {
            return false;
        }

        $stmt = $this->conn->prepare(
            "INSERT INTO users (fullname, email, password, secret_code, role)
             VALUES (:fullname, :email, :password, :secret_code, :role)"
        );
        return $stmt->execute([
            ':fullname' => $fullname,
            ':email' => $email,
            ':password' => password_hash($password, PASSWORD_DEFAULT),
            ':secret_code' => password_hash($secretCode, PASSWORD_DEFAULT),
            ':role' => $role
        ]);
    }

    public function updateUserByAdmin($id, $fullname, $email, $role, $password = null, $secretCode = null) {
        $check = $this->conn->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
        $check->execute([':email' => $email, ':id' => $id]);
        if ($check->fetch()) {
            return false;
        }

        $fields = [
            'fullname = :fullname',
            'email = :email',
            'role = :role'
        ];
        $params = [
            ':id' => $id,
            ':fullname' => $fullname,
            ':email' => $email,
            ':role' => $role
        ];

        if ($password !== null && $password !== '') {
            $fields[] = 'password = :password';
            $params[':password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($secretCode !== null && $secretCode !== '') {
            $fields[] = 'secret_code = :secret_code';
            $params[':secret_code'] = password_hash($secretCode, PASSWORD_DEFAULT);
        }

        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
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

    public function updatePasswordWithSecretCode($email, $secretCode, $newPassword) {
        $stmt = $this->conn->prepare("SELECT id, secret_code FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || empty($user['secret_code'])) {
            return false;
        }

        if (!password_verify($secretCode, $user['secret_code'])) {
            return false;
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $update = $this->conn->prepare(
            "UPDATE users
             SET password = :password, reset_token = NULL, reset_expires_at = NULL
             WHERE id = :id"
        );
        return $update->execute([
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
