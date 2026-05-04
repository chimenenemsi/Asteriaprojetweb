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
        <div class="nf-card" style="width:min(560px,100%);">
            <main class="nf-main">
                <h2 class="nf-h2" style="margin-bottom:8px; text-align:center;">Bienvenue chez NutriFit !</h2>
                <p class="nf-sub" style="margin-top:0; text-align:center;">Bonjour <?php echo htmlspecialchars($_SESSION['user']['fullname']); ?> 👋</p>

                <div style="margin: 24px 0; text-align:center;">
                    <p>🌱 Content de vous revoir</p>
                    <p>🥗 Découvrez nos conseils nutritionnels avec l’assistant en bas à droite</p>
                    <p>♻️ Mangez durable, vivez mieux</p>
                </div>

                <div class="nf-actions" style="justify-content:center; flex-wrap:wrap;">
                    <a class="nf-btn" style="text-decoration:none; display:inline-block;" href="index.php?action=logout">Se déconnecter</a>
                </div>
            </main>
        </div>
    </div>

    <?php
      $nfChatbotContext = 'user';
      include __DIR__ . '/partials/chatbot_widget.php';
    ?>
</body>
</html>
