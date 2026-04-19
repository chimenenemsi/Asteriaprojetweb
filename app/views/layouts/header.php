<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Gestion des Allergies'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $baseUrl ?? '/gestion-allergies'; ?>/app/views/assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo $baseUrl ?? '/gestion-allergies'; ?>/index.php">⚕️ Allergies</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $baseUrl ?? '/gestion-allergies'; ?>/index.php">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $baseUrl ?? '/gestion-allergies'; ?>/index.php?action=allergies">Allergies</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $baseUrl ?? '/gestion-allergies'; ?>/index.php?action=traitements">Traitements</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $baseUrl ?? '/gestion-allergies'; ?>/admin.php">👨‍💼 Admin</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
