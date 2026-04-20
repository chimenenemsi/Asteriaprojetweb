<?php
declare(strict_types=1);

class BaseController
{
    public function render(string $view, array $data = [], string $area = 'frontoffice'): void
    {
        extract($data, EXTR_SKIP);

        ob_start();
        require ROOT_PATH . '/views/' . $view . '.php';
        $content = (string) ob_get_clean();

        require ROOT_PATH . '/views/layouts/' . $area . '.php';
    }

    public function renderNotFound(): void
    {
        http_response_code(404);
        $this->render('errors/not-found', [
            'pageTitle' => 'Not Found',
            'area' => 'frontoffice',
            'currentSection' => '',
        ], 'frontoffice');
    }

    protected function redirect(string $route, array $params = []): void
    {
        header('Location: ' . route_url($route, $params));
        exit;
    }

    protected function connection(): PDO
    {
        return Database::connection();
    }
}
