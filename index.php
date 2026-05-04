<?php
session_start();

require_once __DIR__ . "/controllers/UserController.php";

$controller = new UserController();

$action = isset($_GET['action']) ? $_GET['action'] : 'home';

switch($action) {

    case 'home':
        include __DIR__ . "/views/home.php";
        break;

    case 'admin':
        if(!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? 'user') !== 'admin') {
            header("Location: index.php?action=login");
            exit();
        }
        header("Location: index.php?action=list");
        exit();
        break;

    case 'register':
        $controller->register();
        include __DIR__ . "/views/register.php";
        break;

    case 'login':
        $controller->login();
        include __DIR__ . "/views/login.php";
        break;

    case 'forgot_password':
        $controller->forgotPassword();
        include __DIR__ . "/views/login.php";
        break;

    case 'reset_password':
        $controller->resetPassword();
        include __DIR__ . "/views/login.php";
        break;

    case 'list':
        if(!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? 'user') !== 'admin') {
            header("Location: index.php?action=login");
            exit();
        }
        $controller->admin();
        break;

    case 'delete':
        $controller->delete();
        break;

    case 'save_user':
        $controller->saveUser();
        break;

    case 'user_dashboard':
        if(!isset($_SESSION['user'])) {
            header("Location: index.php?action=login");
            exit();
        }
        include __DIR__ . "/views/user_dashboard.php";
        break;

    case 'logout':
        session_destroy();
        header("Location: index.php?action=home");
        exit();
        break;

    // NOUVEAU CASE
    case 'change_role':
        $controller->changeRole();
        break;

    default:
        header("Location: index.php?action=home");
        exit();
}
?>