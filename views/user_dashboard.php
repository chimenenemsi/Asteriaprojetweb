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
    <title>NutriFit — Bienvenue</title>
    <link rel="stylesheet" href="assets/style.css" />
</head>
<body>
    <div class="nf-shell">
        <div class="nf-card" style="width:min(520px,100%);">
            <main class="nf-main" style="text-align:center;">
                <h2 class="nf-h2" style="margin-bottom:8px;">Bienvenue chez NutriFit !</h2>
                <p class="nf-sub" style="margin-top:0;">Bonjour <?php echo htmlspecialchars($_SESSION['user']['fullname']); ?> 👋</p>
                
                <div style="margin: 30px 0;">
                    <p>🌱 Content de vous revoir</p>
                    <p>🥗 Découvrez nos conseils nutritionnels</p>
                    <p>♻️ Mangez durable, vivez mieux</p>
                </div>

                <div class="nf-actions" style="justify-content:center; flex-direction:column;">
                    <a class="nf-btn" style="text-decoration:none; display:inline-block;" href="index.php?action=logout">Se déconnecter</a>
                </div>
            </main>
        </div>
    </div>
</body>
</html>