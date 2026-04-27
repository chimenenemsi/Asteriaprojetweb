<?php
if(!isset($_SESSION['user'])) {
    header("Location: index.php?action=login");
    exit();
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>NutriFit — Modifier mon profil</title>
    <link rel="stylesheet" href="assets/style.css" />
</head>
<body>
    <div class="nf-shell">
        <div class="nf-card">
            <div class="nf-grid">
                <aside class="nf-side">
                    <div class="nf-brand">
                        <div class="nf-logo">
                            <img src="assets/logo-placeholder.svg" alt="Logo NutriFit