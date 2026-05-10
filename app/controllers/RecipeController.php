<?php
use Config\Database;

class RecipeController
{
    // ===== READ ALL with Search & Sort =====
    public static function obtenirTous()
    {
        try {
            $pdo = Database::getConnexion();
            
            $search = $_GET['search'] ?? '';
            $sort = $_GET['sort'] ?? 'r.id';
            $order = $_GET['order'] ?? 'DESC';

            // Colonnes autorisées pour le tri
            $allowedSorts = ['r.id', 'r.name', 'diet_plan_title', 'r.meal_type', 'r.day_number', 'r.calories'];
            if (!in_array($sort, $allowedSorts)) $sort = 'r.id';
            $order = (strtoupper($order) === 'ASC') ? 'ASC' : 'DESC';

            $sql = "SELECT r.*, d.title as diet_plan_title 
                    FROM recipes r 
                    LEFT JOIN diet_plans d ON r.diet_plan_id = d.id 
                    WHERE 1=1";
            $params = [];

            if (!empty($search)) {
                $sql .= " AND (r.name LIKE :search OR d.title LIKE :search OR r.meal_type LIKE :search)";
                $params[':search'] = "%$search%";
            }

            $sql .= " ORDER BY $sort $order";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'recipes' => $recipes]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ===== STATS for Charts =====
    public static function obtenirStats()
    {
        try {
            $pdo = Database::getConnexion();
            
            // Stats par type de repas
            $sqlMeal = "SELECT meal_type as label, COUNT(*) as value FROM recipes GROUP BY meal_type";
            $meals = $pdo->query($sqlMeal)->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true, 
                'meals' => $meals
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
            $sql = "INSERT INTO recipes (diet_plan_id, name, meal_type, day_number, calories, proteins, carbs, fats, prep_time_minutes, instructions, image) 
                    VALUES (:diet_plan_id, :name, :meal_type, :day_number, :calories, :proteins, :carbs, :fats, :prep_time_minutes, :instructions, :image)";
            
            $stmt = $pdo->prepare($sql);
            $resultat = $stmt->execute([
                ':diet_plan_id' => intval($data['diet_plan_id'] ?? 0),
                ':name' => htmlspecialchars(trim($data['name'] ?? '')),
                ':meal_type' => $data['meal_type'] ?? 'BREAKFAST',
                ':day_number' => intval($data['day_number'] ?? 1),
                ':calories' => floatval($data['calories'] ?? 0),
                ':proteins' => floatval($data['proteins'] ?? 0),
                ':carbs' => floatval($data['carbs'] ?? 0),
                ':fats' => floatval($data['fats'] ?? 0),
                ':prep_time_minutes' => intval($data['prep_time_minutes'] ?? 0),
                ':instructions' => htmlspecialchars(trim($data['instructions'] ?? '')),
                ':image' => $data['image'] ?? null
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
            $sql = "UPDATE recipes SET diet_plan_id = :diet_plan_id, name = :name, meal_type = :meal_type, 
                    day_number = :day_number, calories = :calories, proteins = :proteins, carbs = :carbs, 
                    fats = :fats, prep_time_minutes = :prep_time_minutes, instructions = :instructions, 
                    image = :image WHERE id = :id";
            
            $stmt = $pdo->prepare($sql);
            $resultat = $stmt->execute([
                ':id' => $id,
                ':diet_plan_id' => intval($data['diet_plan_id'] ?? 0),
                ':name' => htmlspecialchars(trim($data['name'] ?? '')),
                ':meal_type' => $data['meal_type'] ?? 'BREAKFAST',
                ':day_number' => intval($data['day_number'] ?? 1),
                ':calories' => floatval($data['calories'] ?? 0),
                ':proteins' => floatval($data['proteins'] ?? 0),
                ':carbs' => floatval($data['carbs'] ?? 0),
                ':fats' => floatval($data['fats'] ?? 0),
                ':prep_time_minutes' => intval($data['prep_time_minutes'] ?? 0),
                ':instructions' => htmlspecialchars(trim($data['instructions'] ?? '')),
                ':image' => $data['image'] ?? null
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
            $stmt = $pdo->prepare("DELETE FROM recipes WHERE id = :id");
            $resultat = $stmt->execute([':id' => $id]);
            echo json_encode(['success' => $resultat]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
