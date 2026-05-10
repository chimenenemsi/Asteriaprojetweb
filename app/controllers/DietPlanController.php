<?php
use Config\Database;

class DietPlanController
{
    // ===== READ ALL with Search & Sort =====
    public static function obtenirTous()
    {
        try {
            $pdo = Database::getConnexion();
            
            $search = $_GET['search'] ?? '';
            $sort = $_GET['sort'] ?? 'id';
            $order = $_GET['order'] ?? 'DESC';

            // Colonnes autorisées pour le tri
            $allowedSorts = ['id', 'title', 'goal', 'duration_days', 'target_calories_per_day', 'level', 'status'];
            if (!in_array($sort, $allowedSorts)) $sort = 'id';
            $order = (strtoupper($order) === 'ASC') ? 'ASC' : 'DESC';

            $sql = "SELECT * FROM diet_plans WHERE 1=1";
            $params = [];

            if (!empty($search)) {
                $sql .= " AND (title LIKE :search OR goal LIKE :search OR level LIKE :search OR status LIKE :search)";
                $params[':search'] = "%$search%";
            }

            $sql .= " ORDER BY $sort $order";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'plans' => $plans]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ===== STATS for Charts =====
    public static function obtenirStats()
    {
        try {
            $pdo = Database::getConnexion();
            
            // Stats par niveau
            $sqlLevel = "SELECT level as label, COUNT(*) as value FROM diet_plans GROUP BY level";
            $levels = $pdo->query($sqlLevel)->fetchAll(PDO::FETCH_ASSOC);

            // Stats par statut
            $sqlStatus = "SELECT status as label, COUNT(*) as value FROM diet_plans GROUP BY status";
            $statuses = $pdo->query($sqlStatus)->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true, 
                'levels' => $levels,
                'statuses' => $statuses
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }


    // ===== CALENDAR DATA =====
    public static function obtenirCalendrier()
    {
        try {
            $id = intval($_GET['id'] ?? 0);
            $pdo = Database::getConnexion();
            
            // 1. Get plan info
            $stmtPlan = $pdo->prepare("SELECT * FROM diet_plans WHERE id = :id");
            $stmtPlan->execute([':id' => $id]);
            $plan = $stmtPlan->fetch(PDO::FETCH_ASSOC);
            
            if (!$plan) {
                echo json_encode(['success' => false, 'message' => "Programme non trouvé"]);
                return;
            }

            // 2. Get all recipes for this plan
            $stmtRecipes = $pdo->prepare("SELECT * FROM recipes WHERE diet_plan_id = :id ORDER BY day_number ASC, meal_type ASC");
            $stmtRecipes->execute([':id' => $id]);
            $recipes = $stmtRecipes->fetchAll(PDO::FETCH_ASSOC);

            // 3. Group by day
            $calendar = [];
            for ($i = 1; $i <= $plan['duration_days']; $i++) {
                $calendar[$i] = [
                    'day' => $i,
                    'recipes' => array_values(array_filter($recipes, function($r) use ($i) {
                        return intval($r['day_number']) === $i;
                    }))
                ];
            }

            echo json_encode([
                'success' => true, 
                'plan' => $plan,
                'calendar' => array_values($calendar)
            ]);
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
