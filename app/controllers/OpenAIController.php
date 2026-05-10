<?php
use Config\Database;

class OpenAIController
{
    private $endpoint;
    private $apiKey;
    private $deployment;
    private $apiVersion;

    public function __construct() {
        $this->endpoint = 'https://survive-openai.openai.azure.com/';
        $this->apiKey = '7MK8FIGgJNCSV48Jqliml9dtoqs0cutq2b2e6lNpq0DhAXA238TsJQQJ99CAACfhMk5XJ3w3AAABACOGtcXO';
        $this->deployment = 'gpt-4o';
        $this->apiVersion = '2024-02-15-preview';
    }

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

            $ai = new self();
            $recipe = $ai->demanderAI($plan);

            echo json_encode(['success' => true, 'recipe' => $recipe]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function demanderAI($plan)
    {
        $url = "{$this->endpoint}openai/deployments/{$this->deployment}/chat/completions?api-version={$this->apiVersion}";
        
        $prompt = "Tu es un nutritionniste expert. Propose une recette diététique adaptée au programme suivant :\n" .
                  "Titre : {$plan['title']}\n" .
                  "Objectif : {$plan['goal']}\n" .
                  "Niveau : {$plan['level']}\n\n" .
                  "Réponds uniquement par un objet JSON avec les clés suivantes :\n" .
                  "- name (nom de la recette)\n" .
                  "- meal_type (BREAKFAST, LUNCH, DINNER ou SNACK)\n" .
                  "- calories (nombre entier)\n" .
                  "- proteins (nombre en grammes)\n" .
                  "- carbs (nombre en grammes)\n" .
                  "- fats (nombre en grammes)";

        $data = [
            "messages" => [
                ["role" => "system", "content" => "Tu es un assistant qui répond uniquement en JSON."],
                ["role" => "user", "content" => $prompt]
            ],
            "max_tokens" => 500,
            "temperature" => 0.7
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "api-key: {$this->apiKey}"
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Pour localhost si besoin

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception("Erreur CURL: " . curl_error($ch));
        }
        curl_close($ch);

        $result = json_decode($response, true);
        if (!isset($result['choices'][0]['message']['content'])) {
            throw new Exception("Réponse AI invalide : " . ($result['error']['message'] ?? 'Erreur inconnue'));
        }

        $content = $result['choices'][0]['message']['content'];
        // Nettoyage si l'AI a mis des backticks ```json
        $content = preg_replace('/^```json\s*|\s*```$/i', '', trim($content));
        
        return json_decode($content, true);
    }
}
