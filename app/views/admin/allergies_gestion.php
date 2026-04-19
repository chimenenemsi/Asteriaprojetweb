<?php
/**
 * Vue - Gestion des allergies (Admin)
 * Liste avec CRUD
 */
$baseUrl = '/gestion-allergies';
$pageTitle = 'Gestion Allergies - Admin';
include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid my-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>📋 Gestion des Allergies</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="admin.php?action=ajouter_allergie" class="btn btn-primary">➕ Ajouter</a>
            <a href="admin.php" class="btn btn-secondary">Retour</a>
        </div>
    </div>

    <?php if (isset($message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Type</th>
                    <th>Danger</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['allergies'])): ?>
                    <?php foreach ($data['allergies'] as $allergie): ?>
                        <tr>
                            <td><?php echo $allergie->getId(); ?></td>
                            <td><strong><?php echo htmlspecialchars($allergie->getNom()); ?></strong></td>
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
                                <small><?php echo htmlspecialchars(substr($allergie->getDescription(), 0, 40)); ?>...</small>
                            </td>
                            <td>
                                <a href="admin.php?action=editer_allergie&id=<?php echo $allergie->getId(); ?>" 
                                   class="btn btn-sm btn-warning">✏️ Éditer</a>
                                <a href="admin.php?action=supprimer_allergie&id=<?php echo $allergie->getId(); ?>" 
                                   class="btn btn-sm btn-danger" onclick="return confirm('Confirmer la suppression?')">🗑️ Suppr</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">
                            Aucune allergie trouvée. <a href="admin.php?action=ajouter_allergie">Ajouter une</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if (($data['pages'] ?? 1) > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php if ($data['page'] > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="admin.php?action=allergies&page=1">Première</a>
                    </li>
                <?php endif; ?>

                <?php for ($i = max(1, $data['page'] - 2); $i <= min($data['pages'], $data['page'] + 2); $i++): ?>
                    <li class="page-item <?php echo $i === $data['page'] ? 'active' : ''; ?>">
                        <a class="page-link" href="admin.php?action=allergies&page=<?php echo $i; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <?php if ($data['page'] < $data['pages']): ?>
                    <li class="page-item">
                        <a class="page-link" href="admin.php?action=allergies&page=<?php echo $data['pages']; ?>">Dernière</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
