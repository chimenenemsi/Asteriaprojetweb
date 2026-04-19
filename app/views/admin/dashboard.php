<?php
/**
 * Vue - Dashboard Admin (BackOffice)
 */
$baseUrl = '/gestion-allergies';
$pageTitle = 'Dashboard Admin - Gestion des Allergies';
include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid my-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>👨‍💼 Dashboard Admin</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="admin.php?action=allergies" class="btn btn-primary me-2">Gérer Allergies</a>
            <a href="admin.php?action=traitements" class="btn btn-info">Gérer Traitements</a>
        </div>
    </div>

    <?php if (isset($message) && $message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Statistiques -->
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Allergies</h5>
                    <p class="h2" style="color: #667eea;">
                        <?php echo $data['total_allergies'] ?? 0; ?>
                    </p>
                    <a href="admin.php?action=allergies" class="btn btn-sm btn-outline-primary">Gérer</a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Traitements</h5>
                    <p class="h2" style="color: #764ba2;">
                        <?php echo $data['total_traitements'] ?? 0; ?>
                    </p>
                    <a href="admin.php?action=traitements" class="btn btn-sm btn-outline-primary">Gérer</a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">Allergies Critiques</h5>
                    <p class="h2" style="color: #ef4444;">
                        <?php echo $data['allergies_critiques'] ?? 0; ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">Actions Rapides</h5>
                    <div class="d-grid gap-2">
                        <a href="admin.php?action=ajouter_allergie" class="btn btn-sm btn-primary">+ Allergie</a>
                        <a href="admin.php?action=ajouter_traitement" class="btn btn-sm btn-info">+ Traitement</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dernières allergies -->
    <section class="mt-5">
        <h3 class="mb-3">📋 Dernières Allergies</h3>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Type</th>
                        <th>Danger</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['dernieres_allergies'])): ?>
                        <?php foreach ($data['dernieres_allergies'] as $allergie): ?>
                            <tr>
                                <td><?php echo $allergie->getId(); ?></td>
                                <td><?php echo htmlspecialchars($allergie->getNom()); ?></td>
                                <td><?php echo htmlspecialchars($allergie->getType()); ?></td>
                                <td>
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
                                </td>
                                <td>
                                    <a href="admin.php?action=editer_allergie&id=<?php echo $allergie->getId(); ?>" class="btn btn-sm btn-warning">✏️</a>
                                    <a href="admin.php?action=supprimer_allergie&id=<?php echo $allergie->getId(); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr?')">🗑️</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">Aucune allergie</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
