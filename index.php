<?php
/**
 * Routeur principal - Diet Management System
 */

require_once 'config/Database.php';

// Autoload simple pour les contrôleurs
spl_autoload_register(function ($class) {
    $file = 'app/controllers/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

$controller = $_GET['controller'] ?? 'DietPlan';
$action = $_GET['action'] ?? 'obtenirTous';

$controllerClass = $controller . 'Controller';

if (class_exists($controllerClass)) {
    if (method_exists($controllerClass, $action)) {
        $controllerClass::$action();
    } else {
        echo json_encode(['success' => false, 'message' => "Action '$action' non trouvée"]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Contrôleur '$controller' non trouvé"]);
}
