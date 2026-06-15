<?php $__env->startSection('title', 'Détails du Rendez-vous'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <a href="<?php echo e(route('super-admin.rendezvous.index')); ?>" class="text-decoration-none">Rendez-vous</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item"><?php echo e($rendezvous->titre); ?></span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div class="d-flex align-items-center">
                <div class="me-3 p-3 bg-primary rounded-3 text-white shadow-sm">
                    <i class="bi bi-calendar-event fs-2"></i>
                </div>
                <div>
                    <h1 class="cp-page-title mb-1"><?php echo e($rendezvous->titre); ?></h1>
                    <p class="cp-page-subtitle mb-0">Planifié le <?php echo e($rendezvous->date_heure ? $rendezvous->date_heure->format('d/m/Y à H:i') : 'N/A'); ?></p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="<?php echo e(route('super-admin.rendezvous.edit', $rendezvous->id)); ?>" class="btn btn-primary px-4">
                    <i class="bi bi-pencil me-2"></i>Modifier
                </a>
                <a href="<?php echo e(route('super-admin.rendezvous.index')); ?>" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>


        <div class="row g-4">
            <!-- Colonne Gauche: Détails RDV -->
            <div class="col-lg-8">
                <div class="cp-chart-card mb-4 shadow-sm border-0">
                    <div class="cp-chart-header border-bottom py-3">
                        <h6 class="cp-chart-title mb-0"><i class="bi bi-info-circle me-2"></i>Informations générales</h6>
                    </div>
                    <div class="p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Type de rendez-vous</label>
                                <div>
                                    <?php
                                        $types = ['reunion' => 'Réunion', 'visite' => 'Visite', 'appel' => 'Appel', 'autre' => 'Autre'];
                                    ?>
                                    <span class="badge bg-light text-dark px-3 py-2 fs-6">
                                        <?php echo e($rendezvous->type == 'autre' ? ($rendezvous->type_autre ?? 'Autre') : ($types[$rendezvous->type] ?? $rendezvous->type)); ?>

                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Statut</label>
                                <div>
                                    <?php
                                        $statutClass = ['planifie' => 'bg-info', 'confirme' => 'bg-success', 'termine' => 'bg-secondary', 'annule' => 'bg-danger'];
                                        $statutText = ['planifie' => 'Planifié', 'confirme' => 'Confirmé', 'termine' => 'Terminé', 'annule' => 'Annulé'];
                                    ?>
                                    <span class="badge <?php echo e($statutClass[$rendezvous->statut] ?? 'bg-secondary'); ?> px-3 py-2">
                                        <?php echo e($statutText[$rendezvous->statut] ?? $rendezvous->statut); ?>

                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Date et Heure</label>
                                <p class="fw-medium mb-0">
                                    <i class="bi bi-calendar3 me-2 text-primary"></i>
                                    <?php echo e($rendezvous->date_heure ? $rendezvous->date_heure->format('d/m/Y') : 'N/A'); ?>

                                    <span class="ms-2"><i class="bi bi-clock me-1 text-primary"></i> <?php echo e($rendezvous->date_heure ? $rendezvous->date_heure->format('H:i') : ''); ?></span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Durée estimée</label>
                                <p class="fw-medium mb-0"><i class="bi bi-hourglass-split me-2 text-primary"></i><?php echo e($rendezvous->duree_minutes); ?> minutes</p>
                            </div>
                            <div class="col-12">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Lieu</label>
                                <div class="bg-light p-3 rounded-3 border">
                                    <i class="bi bi-geo-alt-fill me-2 text-danger"></i>
                                    <span class="fw-medium"><?php echo e($rendezvous->lieu ?? 'Non spécifié'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="cp-chart-card shadow-sm border-0">
                    <div class="cp-chart-header border-bottom py-3">
                        <h6 class="cp-chart-title mb-0"><i class="bi bi-justify-left me-2"></i>Notes & Description</h6>
                    </div>
                    <div class="p-4">
                        <div class="bg-light p-4 rounded-3 min-vh-20 border">
                            <?php if($rendezvous->description): ?>
                                <?php echo nl2br(e($rendezvous->description)); ?>

                            <?php else: ?>
                                <p class="text-muted italic mb-0">Aucune description pour ce rendez-vous.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne Droite: Projet -->
            <div class="col-lg-4">
                <!-- Bloc Projet -->
                <div class="cp-chart-card shadow-sm border-0">
                    <div class="cp-chart-header border-bottom py-3">
                        <h6 class="cp-chart-title mb-0"><i class="bi bi-kanban me-2"></i>Projet Lié</h6>
                    </div>
                    <div class="p-4">
                        <?php if($rendezvous->projet): ?>
                            <div class="d-flex align-items-center mb-3">
                                <div class="p-3 bg-success bg-opacity-10 rounded-circle text-success me-3">
                                    <i class="bi bi-briefcase fs-3"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0"><?php echo e($rendezvous->projet->nom); ?></h5>
                                    <p class="text-muted small mb-0">Réf: <?php echo e($rendezvous->projet->reference ?? 'N/A'); ?></p>
                                </div>
                            </div>
                            <div class="d-grid mt-3">
                                <a href="<?php echo e(route('super-admin.projets.show', $rendezvous->projet->id)); ?>" class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-eye me-1"></i>Voir détails projet
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-3">
                                <p class="text-muted mb-0 italic">Aucun projet lié</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Actions de suppression -->
                <div class="mt-4 p-2">
                    <form action="<?php echo e(route('super-admin.rendezvous.destroy', $rendezvous->id)); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-outline-warning w-100" onclick="return confirm('Supprimer définitivement ce rendez-vous ?')">
                            <i class="bi bi-trash me-2"></i>Supprimer ce rendez-vous
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/super-admin/rendezvous/show.blade.php ENDPATH**/ ?>