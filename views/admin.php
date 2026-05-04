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
            <?php
              $currentSearch = trim($_GET['q'] ?? '');
              $currentLetter = strtoupper(trim($_GET['letter'] ?? ''));
              $currentRoleFilter = trim($_GET['role_filter'] ?? '');
              $currentSortBy = $_GET['sort_by'] ?? 'id';
              $currentSortDir = strtoupper($_GET['sort_dir'] ?? 'DESC');

              function buildAdminUrl($overrides = []) {
                $params = array_merge($_GET, $overrides);
                foreach ($params as $key => $value) {
                  if ($value === '' || $value === null) {
                    unset($params[$key]);
                  }
                }
                $params['action'] = 'list';
                return 'index.php?' . http_build_query($params);
              }

              function sortLink($column, $currentSortBy, $currentSortDir) {
                $nextDir = ($currentSortBy === $column && $currentSortDir === 'ASC') ? 'DESC' : 'ASC';
                return buildAdminUrl(['sort_by' => $column, 'sort_dir' => $nextDir]);
              }
            ?>
            <h2 class="nf-h2">Liste des utilisateurs <span class="nf-pill">Admin</span></h2>
            <p class="nf-sub">Projet "Nutrition durable" — NutriFit.</p>

            <?php if(isset($_SESSION['success'])): ?>
              <p class="nf-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
            <?php endif; ?>

            <?php if(isset($_SESSION['error'])): ?>
              <p class="nf-err"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
            <?php endif; ?>

            <form method="GET" class="nf-form" style="margin-bottom: 10px;">
              <input type="hidden" name="action" value="list" />
              <div class="nf-field">
                <label for="search_admin">Recherche (nom ou email)</label>
                <input id="search_admin" type="text" name="q" value="<?php echo htmlspecialchars($currentSearch, ENT_QUOTES, 'UTF-8'); ?>" />
              </div>
              <div class="nf-field">
                <label for="role_filter_admin">Recherche selon role</label>
                <select id="role_filter_admin" name="role_filter">
                  <option value="" <?php echo $currentRoleFilter === '' ? 'selected' : ''; ?>>Tous les roles</option>
                  <option value="admin" <?php echo $currentRoleFilter === 'admin' ? 'selected' : ''; ?>>Admin</option>
                  <option value="user" <?php echo $currentRoleFilter === 'user' ? 'selected' : ''; ?>>Utilisateur</option>
                </select>
              </div>
              <div class="nf-actions">
                <button class="nf-btn" type="submit">Rechercher</button>
                <a class="nf-link" href="index.php?action=list">Reinitialiser</a>
              </div>
            </form>

            <div style="margin-bottom: 15px;">
              <strong>Filtre alphabetique:</strong>
              <a class="nf-link" href="<?php echo buildAdminUrl(['letter' => '']); ?>">Tous</a>
              <?php foreach (range('A', 'Z') as $alpha): ?>
                &nbsp;
                <?php if ($currentLetter === $alpha): ?>
                  <strong><?php echo $alpha; ?></strong>
                <?php else: ?>
                  <a class="nf-link" href="<?php echo buildAdminUrl(['letter' => $alpha]); ?>"><?php echo $alpha; ?></a>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>

            <div style="margin-bottom:15px; padding:12px; border:1px solid #ddd; border-radius:8px;">
              <h3 style="margin-top:0;">
                <?php echo $editingUser ? "Modifier utilisateur #" . (int)$editingUser['id'] : "Ajouter un utilisateur"; ?>
              </h3>
              <form method="POST" action="index.php?action=save_user" class="nf-form">
                <input type="hidden" name="id" value="<?php echo $editingUser ? (int)$editingUser['id'] : ''; ?>">
                <div class="nf-field">
                  <label for="admin_fullname">Nom complet</label>
                  <input id="admin_fullname" type="text" name="fullname" value="<?php echo htmlspecialchars($editingUser['fullname'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" />
                </div>
                <div class="nf-field">
                  <label for="admin_email">Email</label>
                  <input id="admin_email" type="text" name="email" value="<?php echo htmlspecialchars($editingUser['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" />
                </div>
                <div class="nf-field">
                  <label for="admin_password"><?php echo $editingUser ? "Nouveau mot de passe (optionnel)" : "Mot de passe"; ?></label>
                  <input id="admin_password" type="password" name="password" />
                </div>
                <div class="nf-field">
                  <label for="admin_secret_code"><?php echo $editingUser ? "Nouveau code secret (optionnel)" : "Code secret"; ?></label>
                  <input id="admin_secret_code" type="password" name="secret_code" />
                </div>
                <div class="nf-field">
                  <label for="admin_role">Role</label>
                  <select id="admin_role" name="role">
                    <option value="user" <?php echo (($editingUser['role'] ?? 'user') === 'user') ? 'selected' : ''; ?>>Utilisateur</option>
                    <option value="admin" <?php echo (($editingUser['role'] ?? 'user') === 'admin') ? 'selected' : ''; ?>>Admin</option>
                  </select>
                </div>
                <div class="nf-actions">
                  <button class="nf-btn" type="submit" name="submit" value="SaveUser"><?php echo $editingUser ? "Mettre a jour" : "Ajouter"; ?></button>
                  <?php if ($editingUser): ?>
                    <a class="nf-link" href="index.php?action=list">Annuler</a>
                  <?php endif; ?>
                </div>
              </form>
            </div>

            <table class="nf-table">
              <thead>
                <tr>
                  <th><a class="nf-link" href="<?php echo sortLink('id', $currentSortBy, $currentSortDir); ?>">ID</a></th>
                  <th><a class="nf-link" href="<?php echo sortLink('fullname', $currentSortBy, $currentSortDir); ?>">Nom</a></th>
                  <th><a class="nf-link" href="<?php echo sortLink('email', $currentSortBy, $currentSortDir); ?>">Email</a></th>
                  <th><a class="nf-link" href="<?php echo sortLink('role', $currentSortBy, $currentSortDir); ?>">Rôle</a></th>
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
                      <a class="nf-link" href="index.php?action=list&edit=<?php echo $row['id']; ?>">Modifier</a>
                      &nbsp;|&nbsp;
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