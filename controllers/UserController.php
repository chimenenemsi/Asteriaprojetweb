<?php
require_once __DIR__ . "/../models/User.php";

class UserController {
    private $user;

    public function __construct() {
        $this->user = new User();
    }

    public function register() {
        if(isset($_POST['submit'])) {
            $secretCode = trim($_POST['secret_code'] ?? '');
            if ($secretCode === '') {
                $_SESSION['register_error'] = "Veuillez saisir un code secret.";
                header("Location: index.php?action=register");
                exit();
            }
            $ok = $this->user->register($_POST['fullname'], $_POST['email'], $_POST['password'], $secretCode);
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

        if (($_POST['submit'] ?? '') === 'ResetBySecretCode') {
            $email = trim($_POST['email'] ?? '');
            $secretCode = trim($_POST['secret_code'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';

            if ($email === '' || $secretCode === '' || $password === '' || $confirm === '') {
                $_SESSION['forgot_error'] = "Veuillez remplir tous les champs pour le reset par code secret.";
                header("Location: index.php?action=forgot_password");
                exit();
            }

            if ($password !== $confirm) {
                $_SESSION['forgot_error'] = "Les mots de passe ne correspondent pas.";
                header("Location: index.php?action=forgot_password");
                exit();
            }

            if (strlen($password) < 6) {
                $_SESSION['forgot_error'] = "Le mot de passe doit contenir au moins 6 caracteres.";
                header("Location: index.php?action=forgot_password");
                exit();
            }

            $ok = $this->user->updatePasswordWithSecretCode($email, $secretCode, $password);
            if ($ok) {
                $_SESSION['reset_success'] = "Mot de passe reinitialise avec le code secret. Vous pouvez vous connecter.";
                header("Location: index.php?action=login");
                exit();
            }

            $_SESSION['forgot_error'] = "Email ou code secret invalide.";
            header("Location: index.php?action=forgot_password");
            exit();
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

            $mailSent = mail($email, $subject, $message, $headers);
            if (!$mailSent) {
                $_SESSION['forgot_error'] = "Le mail n'a pas pu etre envoye depuis ce serveur.";
                $_SESSION['forgot_debug_link'] = $resetLink;
                header("Location: index.php?action=forgot_password");
                exit();
            }
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
        $search = trim($_GET['q'] ?? '');
        $letter = strtoupper(trim($_GET['letter'] ?? ''));
        $roleFilter = trim($_GET['role_filter'] ?? '');
        $sortBy = trim($_GET['sort_by'] ?? 'id');
        $sortDir = strtoupper(trim($_GET['sort_dir'] ?? 'DESC'));

        $users = $this->user->getUsers($search, $letter, $roleFilter, $sortBy, $sortDir);
        $editingUser = null;
        if (isset($_GET['edit']) && ctype_digit((string)$_GET['edit'])) {
            $editingUser = $this->user->getUserById((int)$_GET['edit']);
        }
        include __DIR__ . "/../views/admin.php";
    }

    public function saveUser() {
        if(!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? 'user') !== 'admin') {
            header("Location: index.php?action=login");
            exit();
        }

        if (!isset($_POST['submit'])) {
            header("Location: index.php?action=list");
            exit();
        }

        $id = trim($_POST['id'] ?? '');
        $fullname = trim($_POST['fullname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = ($_POST['role'] ?? 'user') === 'admin' ? 'admin' : 'user';
        $password = $_POST['password'] ?? '';
        $secretCode = trim($_POST['secret_code'] ?? '');

        if ($fullname === '' || $email === '') {
            $_SESSION['error'] = "Nom et email sont obligatoires.";
            header("Location: index.php?action=list" . ($id !== '' ? "&edit=" . urlencode($id) : ""));
            exit();
        }

        if ($id === '') {
            if ($password === '' || $secretCode === '') {
                $_SESSION['error'] = "Mot de passe et code secret sont obligatoires pour creer un utilisateur.";
                header("Location: index.php?action=list");
                exit();
            }

            $ok = $this->user->createUserByAdmin($fullname, $email, $password, $secretCode, $role);
            $_SESSION[$ok ? 'success' : 'error'] = $ok
                ? "Utilisateur cree avec succes."
                : "Creation impossible (email deja utilise ou erreur).";
            header("Location: index.php?action=list");
            exit();
        }

        if (!ctype_digit($id)) {
            $_SESSION['error'] = "ID utilisateur invalide.";
            header("Location: index.php?action=list");
            exit();
        }

        if ((int)$id === (int)$_SESSION['user']['id'] && $role !== 'admin') {
            $_SESSION['error'] = "Vous ne pouvez pas retirer votre propre role admin.";
            header("Location: index.php?action=list&edit=" . urlencode($id));
            exit();
        }

        $ok = $this->user->updateUserByAdmin((int)$id, $fullname, $email, $role, $password, $secretCode);
        $_SESSION[$ok ? 'success' : 'error'] = $ok
            ? "Utilisateur modifie avec succes."
            : "Modification impossible (email deja utilise ou erreur).";
        header("Location: index.php?action=list");
        exit();
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