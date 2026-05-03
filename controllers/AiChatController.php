<?php
declare(strict_types=1);

require_once ROOT_PATH . '/controllers/BaseController.php';
require_once ROOT_PATH . '/controllers/GeminiProductAssistantService.php';

class AiChatController extends BaseController
{
    public function chat(): void
    {
        header('Content-Type: application/json; charset=UTF-8');

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed.']);
            return;
        }

        $body = json_decode((string) file_get_contents('php://input'), true);
        if (!is_array($body)) {
            $body = [];
        }

        $message = trim((string) ($body['message'] ?? ''));
        $history = is_array($body['history'] ?? null) ? $body['history'] : [];

        if ($message === '') {
            http_response_code(422);
            echo json_encode(['error' => 'Please type a product question.']);
            return;
        }

        try {
            $catalog = $this->buildProductContext();
            $reply = (new GeminiProductAssistantService())->chat($message, $history, $catalog);
            echo json_encode(['reply' => $reply], JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            http_response_code(503);
            echo json_encode(['error' => 'Asteria product assistant is temporarily unavailable.']);
        }
    }

    private function buildProductContext(): string
    {
        try {
            $statement = $this->connection()->query(
                'SELECT p.name, p.sku, p.price, p.stock_quantity, p.status, p.description, c.name AS category_name
                 FROM products p
                 INNER JOIN product_categories c ON c.id = p.product_category_id
                 ORDER BY c.name ASC, p.name ASC
                 LIMIT 80'
            );
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable) {
            return 'Product catalog temporarily unavailable.';
        }

        if ($rows === []) {
            return 'No products are currently available.';
        }

        $lines = [];
        foreach ($rows as $row) {
            $stock = (int) ($row['stock_quantity'] ?? 0);
            $stockLabel = $stock > 0 ? 'In stock: ' . $stock : 'Out of stock';
            $description = trim((string) ($row['description'] ?? ''));
            $description = $description !== '' ? ' - ' . mb_substr($description, 0, 120) : '';
            $lines[] = sprintf(
                '- [%s] %s | SKU %s | %.2f DT | %s | Status: %s%s',
                (string) ($row['category_name'] ?? 'General'),
                (string) ($row['name'] ?? 'Product'),
                (string) ($row['sku'] ?? '-'),
                (float) ($row['price'] ?? 0),
                $stockLabel,
                (string) ($row['status'] ?? 'ACTIVE'),
                $description
            );
        }

        return implode("\n", $lines);
    }
}
