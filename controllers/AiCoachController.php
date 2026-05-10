<?php
declare(strict_types=1);

require_once ROOT_PATH . '/controllers/GeminiCoachService.php';

class AiCoachController extends BaseController
{
    private GeminiCoachService $gemini;

    public function __construct()
    {
        $this->gemini = new GeminiCoachService();
    }

    public function chat(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->renderNotFound();
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $message = $input['message'] ?? '';

        if (empty($message)) {
            echo json_encode(['error' => 'Message is required']);
            return;
        }

        $response = $this->gemini->getCoachingResponse($message);
        echo json_encode(['reply' => $response]);
    }

    public function summarize(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->renderNotFound();
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $content = $input['content'] ?? '';

        if (empty($content)) {
            echo json_encode(['error' => 'Content is required']);
            return;
        }

        $response = $this->gemini->getSummary($content);
        echo json_encode(['summary' => $response]);
    }
}
