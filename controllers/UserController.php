<?php
require_once __DIR__ . "/../models/User.php";

class UserController {
    private $user;

    public function __construct() {
        $this->user = new User();
    }

    public function register() {
        if(isset($_POST['submit'])) {
            $ok = $this->user->register($_POST['fullname'], $_POST['email'], $_POST['password']);
            if ($ok) {
                header("Location: index.php?action=login");
                exit();
            }
            $_SESSION['register_error'] = "Inscription impossible (email déjà utilisé ou erreur).";
            header("Location: index.php?action=register");
            exit();
        }
    }

    public function login() {
        if(isset($_POST['submit'])) {
            $user = $this->user->login($_POST['email'], $_POST['password']);

            if($user) {
                if (session_status() !== PHP_SESSION_ACTIVE) {
                    session_start();
                }
                session_regenerate_id(true);
                $_SESSION['user'] = $user;
                $role = $user['role'] ?? 'user';
                
                if ($role === 'admin') {
                    header("Location: index.php?action=list");
                } else {
                    header("Location: index.php?action=user_dashboard");
                }
                exit();
            } else {
                $_SESSION['login_error'] = "Email ou mot de passe incorrect.";
                header("Location: index.php?action=login");
                exit();
            }
        }
    }

    public function forgotPassword() {
        if (!isset($_POST['submit'])) {
            return;
        }

        $email = trim($_POST['email'] ?? '');
        if ($email === '') {
            $_SESSION['forgot_error'] = "Veuillez saisir votre email.";
            header("Location: index.php?action=forgot_password");
            exit();
        }

        $token = $this->user->createResetToken($email);
        if ($token) {
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $path = dirname($_SERVER['PHP_SELF'] ?? '/index.php');
            $path = rtrim(str_replace("\\", "/", $path), '/');
            $baseUrl = $scheme . "://" . $host . ($path ? $path : '');
            $resetLink = $baseUrl . "/index.php?action=reset_password&token=" . urlencode($token);

            $subject = "Reinitialisation de votre mot de passe";
            $message = "Bonjour,\n\nCliquez sur ce lien pour reinitialiser votre mot de passe :\n"
                . $resetLink
                . "\n\nCe lien expire dans 1 heure.\n\nSi vous n'avez pas demande cette action, ignorez ce message.";
            $headers = "From: no-reply@nutrifit.local\r\n";

            @mail($email, $subject, $message, $headers);
        }

        $_SESSION['forgot_success'] = "Si cet email existe, un lien de reinitialisation a ete envoye.";
        header("Location: index.php?action=forgot_password");
        exit();
    }

    public function resetPassword() {
        $token = $_GET['token'] ?? '';
        if ($token === '') {
            $_SESSION['reset_error'] = "Lien de reinitialisation invalide.";
            header("Location: index.php?action=forgot_password");
            exit();
        }

        $tokenIsValid = $this->user->validateResetToken($token);
        if (!$tokenIsValid) {
            $_SESSION['reset_error'] = "Le lien est invalide ou expire.";
            header("Location: index.php?action=forgot_password");
            exit();
        }

        if (isset($_POST['submit'])) {
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';

            if ($password === '' || $confirm === '') {
                $_SESSION['reset_error'] = "Veuillez remplir tous les champs.";
                header("Location: index.php?action=reset_password&token=" . urlencode($token));
                exit();
            }

            if ($password !== $confirm) {
                $_SESSION['reset_error'] = "Les mots de passe ne correspondent pas.";
                header("Location: index.php?action=reset_password&token=" . urlencode($token));
                exit();
            }

            if (strlen($password) < 6) {
                $_SESSION['reset_error'] = "Le mot de passe doit contenir au moins 6 caracteres.";
                header("Location: index.php?action=reset_password&token=" . urlencode($token));
                exit();
            }

            if ($this->user->updatePasswordWithToken($token, $password)) {
                $_SESSION['login_error'] = null;
                $_SESSION['forgot_success'] = null;
                $_SESSION['reset_success'] = "Votre mot de passe a ete reinitialise. Vous pouvez vous connecter.";
                header("Location: index.php?action=login");
                exit();
            }

            $_SESSION['reset_error'] = "Impossible de reinitialiser le mot de passe.";
            header("Location: index.php?action=reset_password&token=" . urlencode($token));
            exit();
        }
    }

    public function admin() {
        $users = $this->user->getUsers();
        include __DIR__ . "/../views/admin.php";
    }

    public function delete() {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?action=login");
            exit();
        }
        $role = $_SESSION['user']['role'] ?? 'user';
        if ($role !== 'admin') {
            header("Location: index.php?action=user_dashboard");
            exit();
        }
        if(isset($_GET['id'])) {
            $this->user->delete($_GET['id']);
            header("Location: index.php?action=list");
            exit();
        }
    }

    // NOUVELLE METHODE
    public function changeRole() {
        if(!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? 'user') !== 'admin') {
            header("Location: index.php?action=login");
            exit();
        }
        
        if(isset($_POST['user_id']) && isset($_POST['role'])) {
            $userId = $_POST['user_id'];
            $role = $_POST['role'];
            
            if($userId == $_SESSION['user']['id'] && $role !== 'admin') {
                $_SESSION['error'] = "Vous ne pouvez pas changer votre propre rôle !";
                header("Location: index.php?action=list");
                exit();
            }
            
            if($this->user->changeRole($userId, $role)) {
                $_SESSION['success'] = "Rôle modifié avec succès !";
            } else {
                $_SESSION['error'] = "Erreur lors de la modification du rôle.";
            }
        }
        
        header("Location: index.php?action=list");
        exit();
    }
}
?>