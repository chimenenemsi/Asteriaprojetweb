<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>NutriFit — Accueil</title>
    <link rel="stylesheet" href="assets/style.css" />
</head>
<body>
    <div class="nf-shell">
        <div class="nf-card" style="width:min(520px,100%);">
            <main class="nf-main" style="text-align:center;">
                <h2 class="nf-h2" style="margin-bottom:8px;">NutriFit</h2>
                <p class="nf-sub" style="margin-top:0;">Choisis une action</p>
                <div class="nf-actions" style="justify-content:center; flex-direction:column;">
                    <a class="nf-btn" style="text-decoration:none; display:inline-block;" href="index.php?action=login">Connexion</a>
                    <a class="nf-btn" style="text-decoration:none; display:inline-block; background:linear-gradient(135deg,#0f766e,#14b8a6); box-shadow:0 10px 26px rgba(15,118,110,.28);" href="index.php?action=register">Inscription</a>
                </div>
            </main>
      </div>
    </div>

    <?php
      $nfChatbotContext = 'guest_login';
      include __DIR__ . '/partials/chatbot_widget.php';
    ?>
  </body>
</html>