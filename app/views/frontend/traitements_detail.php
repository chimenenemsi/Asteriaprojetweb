<?php
/**
 * Frontend - Détail d'un traitement
 * app/views/frontend/traitements_detail.php
 */

$traitement = $data['traitement'] ?? null;

if (!$traitement) {
    echo '<div class="alert alert-warning">Traitement non trouvé.</div>';
    return;
}
?>

<?php include '../app/views/layouts/header.php'; ?>

<main class="container my-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
            <li class="breadcrumb-item"><a href="index.php?action=traitements">Traitements</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($traitement->getNom()); ?></li>
        </ol>
    </nav>

    <!-- Détail du traitement -->
    <div class="row">
        <div class="col-md-8">
            <article class="card border-left-primary h-100">
                <div class="card-body">
                    <!-- Titre -->
                    <h1 class="card-title"><?php echo htmlspecialchars($traitement->getNom()); ?></h1>

                    <!-- Badges -->
                    <div class="mb-3">
                        <span class="badge bg-info"><?php echo htmlspecialchars($traitement->getType()); ?></span>
                    </div>

                    <!-- Contenu -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-box">
                                <h5 class="text-primary">Dosage</h5>
                                <p class="lead"><?php echo htmlspecialchars($traitement->getDosage()); ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <h5 class="text-primary">Durée du traitement</h5>
                                <p class="lead"><?php echo htmlspecialchars($traitement->getDuree()); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Effets secondaires -->
                    <div class="mt-4">
                        <h5 class="text-danger">⚠️ Effets secondaires</h5>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($traitement->getEffetsSecondaires())); ?></p>
                    </div>

                    <!-- Recommandations -->
                    <div class="alert alert-info mt-4">
                        <strong>💡 Recommandation:</strong><br>
                        Pour plus d'informations sur ce traitement, veuillez consulter un professionnel de santé.
                    </div>

                    <!-- Boutons d'action -->
                    <div class="mt-4">
                        <a href="index.php?action=traitements" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left"></i> Retour aux traitements
                        </a>
                    </div>
                </div>
            </article>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">📋 Informations</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <strong>Type:</strong><br>
                            <span class="badge bg-secondary"><?php echo htmlspecialchars($traitement->getType()); ?></span>
                        </li>
                        <li class="mb-2">
                            <strong>Disponibilité:</strong><br>
                            <span class="badge bg-success">Disponible</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Autres traitements -->
            <div class="card mt-3">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Autres traitements</h5>
                </div>
                <div class="card-body">
                    <a href="index.php?action=traitements" class="btn btn-sm btn-outline-secondary w-100">
                        Voir tous les traitements
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../app/views/layouts/footer.php'; ?>
