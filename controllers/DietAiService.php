<?php
declare(strict_types=1);

class DietAiService
{
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        require_once ROOT_PATH . '/config/services.php';
        $config = services();
        $this->apiKey = (string)($config['gemini_api_key'] ?? '');
        $this->model = (string)($config['gemini_model'] ?? 'gemini-1.5-flash');
    }

    public function generateRecipe(array $plan): ?array
    {
        if (empty($this->apiKey)) {
            return null;
        }

        $prompt = "Tu es un nutritionniste expert pour l'application Asteria. Propose une recette diététique adaptée au programme suivant :\n" .
                  "Titre : {$plan['title']}\n" .
                  "Objectif : {$plan['goal']}\n" .
                  "Niveau : {$plan['level']}\n\n" .
                  "Réponds uniquement par un objet JSON valide avec les clés suivantes :\n" .
                  "- name (nom de la recette)\n" .
                  "- meal_type (BREAKFAST, LUNCH, DINNER ou SNACK)\n" .
                  "- calories (nombre entier)\n" .
                  "- proteins (nombre en grammes)\n" .
                  "- carbs (nombre en grammes)\n" .
                  "- fats (nombre en grammes)\n" .
                  "- instructions (courte description de la préparation)\n" .
                  "N'ajoute aucun texte avant ou après le JSON.";

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

        $data = [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ],
            'generationConfig' => [
                'response_mime_type' => 'application/json'
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode((string)$response, true);
        $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if ($text) {
            // Nettoyage des backticks markdown si présents
            $text = preg_replace('/^```json\s*|\s*```$/i', '', trim($text));
            return json_decode($text, true);
        }

        return null;
    }
}
