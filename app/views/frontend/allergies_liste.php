<?php
/**
 * Vue - Liste des allergies (FrontOffice)
 */
$baseUrl = '/gestion-allergies';
$pageTitle = 'Allergies - Gestion des Allergies';
include __DIR__ . '/../layouts/header.php';
?>

<div class="container my-5">
    <h2 class="mb-4">📋 Toutes les Allergies (<?php echo $data['total'] ?? 0; ?>)</h2>

    <!-- Formulaire de recherche -->
    <form method="GET" action="index.php" class="row g-3 mb-5">
        <input type="hidden" name="action" value="search">
        <div class="col-md-8">
            <input type="text" name="terme" class="form-control" placeholder="🔍 Rechercher...">
        </div>
        <div class="col-md-auto">
            <button type="submit" class="btn btn-primary">Rechercher</button>
        </div>
    </form>

    <!-- Liste des allergies -->
    <div class="row g-4">
        <?php if (!empty($data['allergies'])): ?>
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
                            <p class="card-text small mt-2">
                                <strong>Type:</strong> <?php echo htmlspecialchars($allergie->getType()); ?>
                            </p>
                            <p class="card-text text-muted small">
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
        <?php else: ?>
            <div class="alert alert-info w-100 text-center">Aucune allergie trouvée</div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if (($data['pages'] ?? 1) > 1): ?>
        <nav aria-label="Pagination" class="mt-5">
            <ul class="pagination justify-content-center">
                <?php if ($data['page'] > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="index.php?action=allergies&page=1">Première</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="index.php?action=allergies&page=<?php echo $data['page'] - 1; ?>">Précédente</a>
                    </li>
                <?php endif; ?>

                <?php for ($i = max(1, $data['page'] - 2); $i <= min($data['pages'], $data['page'] + 2); $i++): ?>
                    <li class="page-item <?php echo $i === $data['page'] ? 'active' : ''; ?>">
                        <a class="page-link" href="index.php?action=allergies&page=<?php echo $i; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <?php if ($data['page'] < $data['pages']): ?>
                    <li class="page-item">
                        <a class="page-link" href="index.php?action=allergies&page=<?php echo $data['page'] + 1; ?>">Suivante</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="index.php?action=allergies&page=<?php echo $data['pages']; ?>">Dernière</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
