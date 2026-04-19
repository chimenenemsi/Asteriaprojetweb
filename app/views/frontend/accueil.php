<?php
/**
 * Vue - Accueil (FrontOffice)
 */
$baseUrl = '/gestion-allergies';
$pageTitle = 'Accueil - Gestion des Allergies';
include __DIR__ . '/../layouts/header.php';
?>

<section class="hero py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
    <div class="container text-center">
        <h1 class="display-4">⚠️ Gestion des Allergies</h1>
        <p class="lead">Informations complètes sur les allergies et leurs traitements</p>
        <a href="index.php?action=allergies" class="btn btn-light btn-lg mt-3">Découvrir →</a>
    </div>
</section>

<div class="container my-5">
    <?php if (isset($message) && $message): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Formulaire de recherche -->
    <section class="mb-5">
        <form method="GET" action="index.php" class="row g-3 justify-content-center">
            <input type="hidden" name="action" value="search">
            <div class="col-md-6">
                <input type="text" name="terme" class="form-control form-control-lg" 
                       placeholder="🔍 Rechercher une allergie ou un traitement..." required>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-lg">Rechercher</button>
            </div>
        </form>
    </section>

    <!-- Statistiques -->
    <section class="mb-5">
        <h2 class="text-center mb-4">📊 Statistiques</h2>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Total Allergies</h5>
                        <p class="h2" style="color: #667eea;"><?php echo $data['total_allergies'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Total Traitements</h5>
                        <p class="h2" style="color: #764ba2;"><?php echo $data['total_traitements'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Dernières allergies -->
    <section>
        <h2 class="mb-4">🏠 Dernières Allergies</h2>
        <div class="row g-4">
            <?php if (!empty($data['allergies'])): ?>
                <?php foreach (array_slice($data['allergies'], 0, 3) as $allergie): ?>
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($allergie->getNom()); ?></h5>
                                <p class="card-text text-muted">
                                    <?php echo htmlspecialchars(substr($allergie->getDescription(), 0, 80)); ?>...
                                </p>
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
                            </div>
                            <div class="card-footer bg-white">
                                <a href="index.php?action=detail_allergie&id=<?php echo $allergie->getId(); ?>" 
                                   class="btn btn-primary btn-sm w-100">Voir détails</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info w-100 text-center">Aucune allergie disponible</div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
