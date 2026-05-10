<?php
declare(strict_types=1);

class HomeController extends BaseController
{
    public function index(): void
    {
        $this->render('home', [
            'pageTitle' => 'Home',
            'area' => 'frontoffice',
            'currentSection' => 'home',
        ], 'frontoffice');
    }

    public function notFound(): void
    {
        $this->renderNotFound();
    }
}
