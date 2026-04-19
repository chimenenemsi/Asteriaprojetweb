<?php
/**
 * Vue - Résultats de recherche (FrontOffice)
 */
$baseUrl = '/gestion-allergies';
$terme = htmlspecialchars($data['terme'] ?? '');
$pageTitle = 'Résultats: ' . $terme . ' - Gestion des Allergies';
include __DIR__ . '/../layouts/header.php';
?>

<div class="container my-5">
    <a href="index.php" class="btn btn-outline-secondary mb-4">← Retour</a>

    <h2 class="mb-4">🔍 Résultats pour "<strong><?php echo $terme; ?></strong>"</h2>

    <?php if (($data['resultats_count'] ?? 0) > 0): ?>
        <!-- Allergies trouvées -->
        <?php if (!empty($data['allergies'])): ?>
            <h4 class="mt-4 mb-3">Allergies (<?php echo count($data['allergies']); ?>)</h4>
            <div class="row g-4 mb-5">
                <?php foreach ($data['allergies'] as $allergie): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($allergie->getNom()); ?></h5>
                                <span class="badge bg-<?php 
                                    echo match($allergie->getNiveauDanger()) {
                                        'faible' => 'success',
                                        'moyen' => 'warning',
                                        'élevé' => 'danger',
                                        'critique' => 'dark',
                                        default => 'secondary'
                                    };
                                ?>">
                                    <?php echo htmlspecialchars($allergie->getNiveauDanger()); ?>
                                </span>
                                <p class="card-text text-muted small mt-2">
                                    <?php echo htmlspecialchars(substr($allergie->getDescription(), 0, 60)); ?>...
                                </p>
                            </div>
                            <div class="card-footer bg-white">
                                <a href="index.php?action=detail_allergie&id=<?php echo $allergie->getId(); ?>" 
                                   class="btn btn-primary btn-sm w-100">Voir détails</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Traitements trouvés -->
        <?php if (!empty($data['traitements'])): ?>
            <h4 class="mt-4 mb-3">Traitements (<?php echo count($data['traitements']); ?>)</h4>
            <div class="row g-4">
                <?php foreach ($data['traitements'] as $traitement): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($traitement->getNom()); ?></h5>
                                <span class="badge bg-info">
                                    <?php echo htmlspecialchars($traitement->getType()); ?>
                                </span>
                                <p class="card-text small mt-2">
                                    <strong>Dosage:</strong> <?php echo htmlspecialchars($traitement->getDosage()); ?>
                                </p>
                                <p class="card-text small">
                                    <strong>Durée:</strong> <?php echo htmlspecialchars($traitement->getDuree()); ?>
                                </p>
                            </div>
                            <div class="card-footer bg-white">
                                <a href="index.php?action=detail_traitement&id=<?php echo $traitement->getId(); ?>" 
                                   class="btn btn-info btn-sm w-100">Voir détails</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="alert alert-info text-center">
            <h5>Aucun résultat trouvé</h5>
            <p>Essayez avec d'autres termes de recherche.</p>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
