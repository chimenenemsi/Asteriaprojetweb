<?php
/**
 * Vue - Gestion des traitements (Admin)
 * Liste avec CRUD
 */
$baseUrl = '/gestion-allergies';
$pageTitle = 'Gestion Traitements - Admin';
include __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid my-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>💊 Gestion des Traitements</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="admin.php?action=ajouter_traitement" class="btn btn-primary">➕ Ajouter</a>
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
                    <th>Dosage</th>
                    <th>Durée</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['traitements'])): ?>
                    <?php foreach ($data['traitements'] as $traitement): ?>
                        <tr>
                            <td><?php echo $traitement->getId(); ?></td>
                            <td><strong><?php echo htmlspecialchars($traitement->getNom()); ?></strong></td>
                            <td><?php echo htmlspecialchars($traitement->getType()); ?></td>
                            <td><?php echo htmlspecialchars($traitement->getDosage()); ?></td>
                            <td><?php echo htmlspecialchars($traitement->getDuree()); ?></td>
                            <td>
                                <a href="admin.php?action=editer_traitement&id=<?php echo $traitement->getId(); ?>" 
                                   class="btn btn-sm btn-warning">✏️ Éditer</a>
                                <a href="admin.php?action=supprimer_traitement&id=<?php echo $traitement->getId(); ?>" 
                                   class="btn btn-sm btn-danger" onclick="return confirm('Confirmer la suppression?')">🗑️ Suppr</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">
                            Aucun traitement trouvé. <a href="admin.php?action=ajouter_traitement">Ajouter un</a>
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
                        <a class="page-link" href="admin.php?action=traitements&page=1">Première</a>
                    </li>
                <?php endif; ?>

                <?php for ($i = max(1, $data['page'] - 2); $i <= min($data['pages'], $data['page'] + 2); $i++): ?>
                    <li class="page-item <?php echo $i === $data['page'] ? 'active' : ''; ?>">
                        <a class="page-link" href="admin.php?action=traitements&page=<?php echo $i; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <?php if ($data['page'] < $data['pages']): ?>
                    <li class="page-item">
                        <a class="page-link" href="admin.php?action=traitements&page=<?php echo $data['pages']; ?>">Dernière</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
