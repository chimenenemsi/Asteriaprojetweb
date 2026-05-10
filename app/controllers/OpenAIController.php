<?php
use Config\Database;

require_once ROOT_PATH . '/controllers/DietAiService.php';

class OpenAIController
{
    /**
     * Propose une recette basée sur le programme choisi
     * Action AJAX: index.php?controller=OpenAI&action=proposerRecette&plan_id=X
     */
    public static function proposerRecette()
    {
        try {
            $plan_id = intval($_GET['plan_id'] ?? 0);
            if ($plan_id <= 0) {
                throw new Exception("ID du programme invalide");
            }

            $pdo = Database::getConnexion();
            $stmt = $pdo->prepare("SELECT title, goal, level FROM diet_plans WHERE id = :id");
            $stmt->execute([':id' => $plan_id]);
            $plan = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$plan) {
                throw new Exception("Programme non trouvé");
            }

            $aiService = new DietAiService();
            $recipe = $aiService->generateRecipe($plan);

            if (!$recipe) {
                throw new Exception("Impossible de générer une recette. Vérifiez votre clé API Gemini.");
            }

            echo json_encode(['success' => true, 'recipe' => $recipe]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
