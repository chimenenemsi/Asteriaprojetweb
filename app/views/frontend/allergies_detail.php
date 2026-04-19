<?php
/**
 * Vue - Détail d'une allergie (FrontOffice)
 */
$baseUrl = '/gestion-allergies';
$allergie = $data['allergie'] ?? null;
if (!$allergie) {
    header('Location: index.php');
    exit;
}
$pageTitle = htmlspecialchars($allergie->getNom()) . ' - Gestion des Allergies';
include __DIR__ . '/../layouts/header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-2">
            <a href="index.php?action=allergies" class="btn btn-outline-secondary">← Retour</a>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-8">
            <h1><?php echo htmlspecialchars($allergie->getNom()); ?></h1>
            
            <div class="card mb-4">
                <div class="card-header">
                    <span class="badge bg-<?php 
                        echo match($allergie->getNiveauDanger()) {
                            'faible' => 'success',
                            'moyen' => 'warning',
                            'élevé' => 'danger',
                            'critique' => 'dark',
                            default => 'secondary'
                        };
                    ?>">
                        Niveau: <?php echo htmlspecialchars($allergie->getNiveauDanger()); ?>
                    </span>
                    <span class="badge bg-info ms-2">Type: <?php echo htmlspecialchars($allergie->getType()); ?></span>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Description</h5>
                    <p class="card-text"><?php echo htmlspecialchars($allergie->getDescription()); ?></p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">⚠️ Symptômes</h5>
                </div>
                <div class="card-body">
                    <p><?php echo htmlspecialchars($allergie->getSymptomes()); ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informations</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <strong>ID:</strong> <?php echo $allergie->getId(); ?>
                        </li>
                        <li class="mb-2">
                            <strong>Nom:</strong> <?php echo htmlspecialchars($allergie->getNom()); ?>
                        </li>
                        <li class="mb-2">
                            <strong>Type:</strong> <?php echo htmlspecialchars($allergie->getType()); ?>
                        </li>
                        <li>
                            <strong>Danger:</strong> 
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
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
