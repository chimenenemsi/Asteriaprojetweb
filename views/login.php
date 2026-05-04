<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>NutriFit — Connexion</title>
    <link rel="stylesheet" href="assets/style.css" />
  </head>
  <body>
    <div class="nf-shell">
      <div class="nf-card">
        <div class="nf-grid">
          <aside class="nf-side">
            <div class="nf-brand">
              <div class="nf-logo">
                <!-- Remplace ce fichier plus tard par ton vrai logo -->
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
                <small>Tu me donneras l’image et je la branche ici</small>
              </div>
            </div>
          </aside>

          <main class="nf-main">
            <?php
              $action = $_GET['action'] ?? 'login';
              $isForgot = ($action === 'forgot_password');
              $isReset = ($action === 'reset_password');
            ?>
            <h2 class="nf-h2">
              <?php
                if ($isForgot) {
                  echo "Mot de passe oublie";
                } elseif ($isReset) {
                  echo "Nouveau mot de passe";
                } else {
                  echo "Connexion";
                }
              ?>
            </h2>
            <p class="nf-sub">
              <?php
                if ($isForgot) {
                  echo "Entrez votre email pour recevoir un lien de reinitialisation.";
                } elseif ($isReset) {
                  echo "Definissez un nouveau mot de passe.";
                } else {
                  echo "Accede au back-office apres connexion.";
                }
              ?>
            </p>

            <?php if (!empty($_SESSION['login_error'])) { ?>
              <p class="nf-err"><?php echo htmlspecialchars($_SESSION['login_error'], ENT_QUOTES, 'UTF-8'); ?></p>
              <?php unset($_SESSION['login_error']); ?>
            <?php } ?>

            <?php if (!empty($_SESSION['forgot_error'])) { ?>
              <p class="nf-err"><?php echo htmlspecialchars($_SESSION['forgot_error'], ENT_QUOTES, 'UTF-8'); ?></p>
              <?php unset($_SESSION['forgot_error']); ?>
            <?php } ?>

            <?php if (!empty($_SESSION['reset_error'])) { ?>
              <p class="nf-err"><?php echo htmlspecialchars($_SESSION['reset_error'], ENT_QUOTES, 'UTF-8'); ?></p>
              <?php unset($_SESSION['reset_error']); ?>
            <?php } ?>

            <?php if (!empty($_SESSION['forgot_success'])) { ?>
              <p class="nf-ok"><?php echo htmlspecialchars($_SESSION['forgot_success'], ENT_QUOTES, 'UTF-8'); ?></p>
              <?php unset($_SESSION['forgot_success']); ?>
            <?php } ?>

            <?php if (!empty($_SESSION['forgot_debug_link'])) { ?>
              <p class="nf-ok">
                Lien direct de reinitialisation:
                <a class="nf-link" href="<?php echo htmlspecialchars($_SESSION['forgot_debug_link'], ENT_QUOTES, 'UTF-8'); ?>">
                  Ouvrir le lien
                </a>
              </p>
              <?php unset($_SESSION['forgot_debug_link']); ?>
            <?php } ?>

            <?php if (!empty($_SESSION['reset_success'])) { ?>
              <p class="nf-ok"><?php echo htmlspecialchars($_SESSION['reset_success'], ENT_QUOTES, 'UTF-8'); ?></p>
              <?php unset($_SESSION['reset_success']); ?>
            <?php } ?>

            <?php if ($isForgot) { ?>
              <form method="POST" class="nf-form">
                <div class="nf-field">
                  <label for="email">Email</label>
                  <input id="email" type="text" name="email" />
                </div>

                <div class="nf-actions">
                  <button class="nf-btn" type="submit" name="submit" value="ForgotPassword">Envoyer le lien</button>
                  <a class="nf-link" href="index.php?action=login">Retour connexion</a>
                </div>
              </form>

              <hr />

              <form method="POST" class="nf-form">
                <div class="nf-field">
                  <label for="email_secret_reset">Email</label>
                  <input id="email_secret_reset" type="text" name="email" />
                </div>

                <div class="nf-field">
                  <label for="secret_code">Code secret</label>
                  <input id="secret_code" type="password" name="secret_code" />
                </div>

                <div class="nf-field">
                  <label for="password_secret_reset">Nouveau mot de passe</label>
                  <input id="password_secret_reset" type="password" name="password" />
                </div>

                <div class="nf-field">
                  <label for="confirm_password_secret_reset">Confirmer le mot de passe</label>
                  <input id="confirm_password_secret_reset" type="password" name="confirm_password" />
                </div>

                <div class="nf-actions">
                  <button class="nf-btn" type="submit" name="submit" value="ResetBySecretCode">Reinitialiser avec code secret</button>
                </div>
              </form>
            <?php } elseif ($isReset) { ?>
              <form method="POST" class="nf-form">
                <div class="nf-field">
                  <label for="password">Nouveau mot de passe</label>
                  <input id="password" type="password" name="password" />
                </div>

                <div class="nf-field">
                  <label for="confirm_password">Confirmer le mot de passe</label>
                  <input id="confirm_password" type="password" name="confirm_password" />
                </div>

                <div class="nf-actions">
                  <button class="nf-btn" type="submit" name="submit" value="ResetPassword">Mettre a jour</button>
                  <a class="nf-link" href="index.php?action=login">Retour connexion</a>
                </div>
              </form>
            <?php } else { ?>
              <form method="POST" class="nf-form">
                <div class="nf-field">
                  <label for="email">Email</label>
                  <input id="email" type="text" name="email" autocomplete="email" />
                </div>

                <div class="nf-field">
                  <label for="password">Mot de passe</label>
                  <input id="password" type="password" name="password" autocomplete="current-password" />
                </div>

                <div class="nf-actions">
                  <button class="nf-btn" type="submit" name="submit" value="Login">Se connecter</button>
                  <a class="nf-link" href="index.php?action=forgot_password">Mot de passe oublie ?</a>
                </div>

                <div class="nf-actions">
                  <a class="nf-link" href="index.php?action=register">Creer un compte</a>
                </div>
              </form>
            <?php } ?>
          </main>
        </div>
      </div>
    </div>

    <?php
      $nfChatbotContext = 'guest_login';
      include __DIR__ . '/partials/chatbot_widget.php';
    ?>
  </body>
</html>
