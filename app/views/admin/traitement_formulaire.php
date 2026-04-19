<?php
/**
 * Vue - Formulaire traitement (Admin)
 */
$baseUrl = '/gestion-allergies';
$estEdition = isset($data['traitement']);
$traitement = $data['traitement'] ?? null;
$pageTitle = ($estEdition ? 'Éditer' : 'Ajouter') . ' Traitement - Admin';
include __DIR__ . '/../layouts/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2><?php echo $estEdition ? '✏️ Éditer le traitement' : '➕ Ajouter un traitement'; ?></h2>

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

            <form method="POST" action="" id="traitementForm" novalidate>
                <div class="mb-3">
                    <label for="nom" class="form-label">Nom *</label>
                    <input type="text" class="form-control" id="nom" name="nom" 
                           value="<?php echo $traitement ? htmlspecialchars($traitement->getNom()) : ''; ?>" 
                           placeholder="Nom du traitement" required minlength="3">
                </div>

                <div class="mb-3">
                    <label for="type_traitement" class="form-label">Type *</label>
                    <select class="form-select" id="type_traitement" name="type_traitement" required>
                        <option value="">-- Sélectionnez --</option>
                        <?php foreach (['antihistaminique', 'anti-inflammatoire', 'corticoïde', 'bronchodilatateur', 'urgence', 'autre'] as $t): ?>
                            <option value="<?php echo $t; ?>" <?php echo ($traitement && $traitement->getType() === $t) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($t); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="dosage" class="form-label">Dosage *</label>
                    <input type="text" class="form-control" id="dosage" name="dosage" 
                           value="<?php echo $traitement ? htmlspecialchars($traitement->getDosage()) : ''; ?>" 
                           placeholder="Ex: 500mg par jour" required minlength="3">
                </div>

                <div class="mb-3">
                    <label for="duree" class="form-label">Durée *</label>
                    <input type="text" class="form-control" id="duree" name="duree" 
                           value="<?php echo $traitement ? htmlspecialchars($traitement->getDuree()) : ''; ?>" 
                           placeholder="Ex: 7 jours, 2 semaines" required minlength="3">
                </div>

                <div class="mb-3">
                    <label for="effets_secondaires" class="form-label">Effets secondaires *</label>
                    <textarea class="form-control" id="effets_secondaires" name="effets_secondaires" rows="3" 
                              placeholder="Listez les effets secondaires" required minlength="5"><?php 
                        echo $traitement ? htmlspecialchars($traitement->getEffetsSecondaires()) : ''; 
                    ?></textarea>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <?php echo $estEdition ? '💾 Mettre à jour' : '➕ Ajouter'; ?>
                    </button>
                    <a href="admin.php?action=traitements" class="btn btn-secondary">⬅️ Retour</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?php echo $baseUrl; ?>/app/views/assets/js/validation.js"></script>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
