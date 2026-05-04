<?php
declare(strict_types=1);

class HomeController extends BaseController
{
    public function index(): void
    {
        $page = $this->getFrontofficePage('home');

        $this->render('home/index', [
            'pageTitle' => $page['title'],
            'page' => $page,
            'navigation' => $this->getFrontofficeNavigation(),
            'currentRoute' => 'home',
        ], 'frontoffice');
    }

    public function notFound(): void
    {
        $this->renderNotFound();
    }
}
