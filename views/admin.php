<?php
if(!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? 'user') !== 'admin') {
    header("Location: index.php?action=login");
    exit();
}
?>
<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>NutriFit — Back-office</title>
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
                <p class="nf-tagline">Back-office • gestion</p>
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
            <h2 class="nf-h2">Liste des utilisateurs <span class="nf-pill">Admin</span></h2>
            <p class="nf-sub">Projet "Nutrition durable" — NutriFit.</p>

            <?php if(isset($_SESSION['success'])): ?>
              <p class="nf-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
            <?php endif; ?>

            <?php if(isset($_SESSION['error'])): ?>
              <p class="nf-err"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
            <?php endif; ?>

            <table class="nf-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nom</th>
                  <th>Email</th>
                  <th>Rôle</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php while($row = $users->fetch(PDO::FETCH_ASSOC)) { ?>
                  <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td>
                      <form method="POST" action="index.php?action=change_role" style="display:flex; gap:5px;">
                        <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                        <select name="role">
                          <option value="user" <?php echo ($row['role'] ?? 'user') == 'user' ? 'selected' : ''; ?>>Utilisateur</option>
                          <option value="admin" <?php echo ($row['role'] ?? 'user') == 'admin' ? 'selected' : ''; ?>>Admin</option>
                        </select>
                        <button type="submit" class="nf-btn-small">Changer</button>
                      </form>
                    </td>
                    <td>
                      <a class="nf-link" href="index.php?action=delete&id=<?php echo $row['id']; ?>" onclick="return confirm('Supprimer cet utilisateur ?')">Supprimer</a>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
            
            <div style="margin-top: 20px;">
              <a class="nf-link" href="index.php?action=logout">Se déconnecter</a>
            </div>
          </main>
        </div>
      </div>
    </div>
  </body>
</html>