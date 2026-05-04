<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>NutriFit — Inscription</title>
    <link rel="stylesheet" href="assets/style.css" />
  </head>
  <body>
    <div class="nf-shell">
      <div class="nf-card">
        <div class="nf-grid">
          <aside class="nf-side">
            <div class="nf-brand">
              <div class="nf-logo">
                <img src="assets/logo-placeholder.svg" alt="Logo NutriFit" />
              </div>
              <div>
                <div class="nf-title">NutriFit</div>
                <p class="nf-tagline">Nutrition durable • simple • efficace</p>
              </div>
            </div>

            <div class="nf-hero">
              <div>
                <div style="font-weight:800; font-size:14px;">Emplacement photo</div>
                <small></small>
              </div>
            </div>
          </aside>

          <main class="nf-main">
            <h2 class="nf-h2">Inscription</h2>
            <p class="nf-sub">Crée un compte pour accéder au back-office.</p>

            <?php if (!empty($_SESSION['register_error'])) { ?>
              <p class="nf-err"><?php echo htmlspecialchars($_SESSION['register_error'], ENT_QUOTES, 'UTF-8'); ?></p>
              <?php unset($_SESSION['register_error']); ?>
            <?php } ?>

            <form method="POST" class="nf-form">
              <div class="nf-field">
                <label for="fullname">Nom complet</label>
                <input id="fullname" type="text" name="fullname" autocomplete="name" />
              </div>

              <div class="nf-field">
                <label for="email">Email</label>
                <input id="email" type="text" name="email" autocomplete="email" />
              </div>

              <div class="nf-field">
                <label for="password">Mot de passe</label>
                <input id="password" type="password" name="password" autocomplete="new-password" />
              </div>

              <div class="nf-field">
                <label for="secret_code">Code secret (chiffres)</label>
                <input id="secret_code" type="password" name="secret_code" />
              </div>

              <div class="nf-actions">
                <button class="nf-btn" type="submit" name="submit" value="Register">Créer mon compte</button>
                <a class="nf-link" href="index.php?action=login">J’ai déjà un compte</a>
              </div>
            </form>
          </main>
        </div>
      </div>
    </div>

    <?php
      $nfChatbotContext = 'guest_register';
      include __DIR__ . '/partials/chatbot_widget.php';
    ?>
  </body>
</html>