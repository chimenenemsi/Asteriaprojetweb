<?php
use Config\Database;

class DietPlanController
{
    // ===== READ ALL =====
    public static function obtenirTous()
    {
        try {
            $pdo = Database::getConnexion();
            $stmt = $pdo->query("SELECT * FROM diet_plans ORDER BY id DESC");
            $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'plans' => $plans]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ===== CREATE =====
    public static function creer()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $pdo = Database::getConnexion();
            $sql = "INSERT INTO diet_plans (title, goal, duration_days, description, target_calories_per_day, level, status) 
                    VALUES (:title, :goal, :duration_days, :description, :target_calories_per_day, :level, :status)";
            
            $stmt = $pdo->prepare($sql);
            $resultat = $stmt->execute([
                ':title' => htmlspecialchars(trim($data['title'] ?? '')),
                ':goal' => htmlspecialchars(trim($data['goal'] ?? '')),
                ':duration_days' => intval($data['duration_days'] ?? 30),
                ':description' => htmlspecialchars(trim($data['description'] ?? '')),
                ':target_calories_per_day' => floatval($data['target_calories_per_day'] ?? 0),
                ':level' => $data['level'] ?? 'BEGINNER',
                ':status' => $data['status'] ?? 'ACTIVE'
            ]);

            echo json_encode(['success' => $resultat, 'id' => $pdo->lastInsertId()]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ===== UPDATE =====
    public static function mettre_a_jour()
    {
        try {
            $id = intval($_GET['id'] ?? 0);
            $data = json_decode(file_get_contents('php://input'), true);
            $pdo = Database::getConnexion();
            $sql = "UPDATE diet_plans SET title = :title, goal = :goal, duration_days = :duration_days, 
                    description = :description, target_calories_per_day = :target_calories_per_day, 
                    level = :level, status = :status WHERE id = :id";
            
            $stmt = $pdo->prepare($sql);
            $resultat = $stmt->execute([
                ':id' => $id,
                ':title' => htmlspecialchars(trim($data['title'] ?? '')),
                ':goal' => htmlspecialchars(trim($data['goal'] ?? '')),
                ':duration_days' => intval($data['duration_days'] ?? 30),
                ':description' => htmlspecialchars(trim($data['description'] ?? '')),
                ':target_calories_per_day' => floatval($data['target_calories_per_day'] ?? 0),
                ':level' => $data['level'] ?? 'BEGINNER',
                ':status' => $data['status'] ?? 'ACTIVE'
            ]);

            echo json_encode(['success' => $resultat]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ===== DELETE =====
    public static function supprimer()
    {
        try {
            $id = intval($_GET['id'] ?? 0);
            $pdo = Database::getConnexion();
            $stmt = $pdo->prepare("DELETE FROM diet_plans WHERE id = :id");
            $resultat = $stmt->execute([':id' => $id]);
            echo json_encode(['success' => $resultat]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
