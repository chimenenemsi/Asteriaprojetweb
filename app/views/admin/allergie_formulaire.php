<?php
/**
 * Vue - Formulaire allergie (Admin)
 */
$baseUrl = '/gestion-allergies';
$estEdition = isset($data['allergie']);
$allergie = $data['allergie'] ?? null;
$pageTitle = ($estEdition ? 'Éditer' : 'Ajouter') . ' Allergie - Admin';
include __DIR__ . '/../layouts/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2><?php echo $estEdition ? '✏️ Éditer l\'allergie' : '➕ Ajouter une allergie'; ?></h2>

            <?php if (isset($data['erreurs']) && !empty($data['erreurs'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5>Erreurs de validation</h5>
                    <ul class="mb-0">
                        <?php foreach ($data['erreurs'] as $erreur): ?>
                            <li><?php echo htmlspecialchars($erreur); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="allergiForm" novalidate>
                <div class="mb-3">
                    <label for="nom" class="form-label">Nom *</label>
                    <input type="text" class="form-control" id="nom" name="nom" 
                           value="<?php echo $allergie ? htmlspecialchars($allergie->getNom()) : ''; ?>" 
                           placeholder="Nom de l'allergie" required minlength="3">
                </div>

                <div class="mb-3">
                    <label for="type" class="form-label">Type *</label>
                    <select class="form-select" id="type" name="type" required>
                        <option value="">-- Sélectionnez --</option>
                        <?php foreach (['alimentaire', 'médicament', 'environnemental', 'contact', 'autre'] as $t): ?>
                            <option value="<?php echo $t; ?>" <?php echo ($allergie && $allergie->getType() === $t) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($t); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="niveau_danger" class="form-label">Niveau de danger *</label>
                    <select class="form-select" id="niveau_danger" name="niveau_danger" required>
                        <option value="">-- Sélectionnez --</option>
                        <?php foreach (['faible', 'moyen', 'élevé', 'critique'] as $n): ?>
                            <option value="<?php echo $n; ?>" <?php echo ($allergie && $allergie->getNiveauDanger() === $n) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($n); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description *</label>
                    <textarea class="form-control" id="description" name="description" rows="3" 
                              placeholder="Description détaillée" required minlength="5"><?php 
                        echo $allergie ? htmlspecialchars($allergie->getDescription()) : ''; 
                    ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="symptomes" class="form-label">Symptômes *</label>
                    <textarea class="form-control" id="symptomes" name="symptomes" rows="3" 
                              placeholder="Listez les symptômes" required minlength="5"><?php 
                        echo $allergie ? htmlspecialchars($allergie->getSymptomes()) : ''; 
                    ?></textarea>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <?php echo $estEdition ? '💾 Mettre à jour' : '➕ Ajouter'; ?>
                    </button>
                    <a href="admin.php?action=allergies" class="btn btn-secondary">⬅️ Retour</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?php echo $baseUrl; ?>/app/views/assets/js/validation.js"></script>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
