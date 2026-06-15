<?php $__env->startSection('title', 'Détails de l\'Enquête'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <a href="<?php echo e(route('super-admin.satisfaction.index')); ?>" class="text-decoration-none">Satisfaction</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Enquête #<?php echo e($satisfaction->id); ?></span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div class="d-flex align-items-center">
                <div class="me-3 p-3 bg-primary rounded-3 text-white shadow-sm">
                    <i class="bi bi-emoji-smile fs-2"></i>
                </div>
                <div>
                    <h1 class="cp-page-title mb-1">Enquête #<?php echo e($satisfaction->id); ?></h1>
                    <p class="cp-page-subtitle mb-0">Retour partenaire</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="<?php echo e(route('super-admin.satisfaction.index')); ?>" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>


        <div class="row g-4">
            <!-- Colonne Principale -->
            <div class="col-lg-8">
                <div class="cp-chart-card mb-4 shadow-sm border-0">
                    <div class="cp-chart-header border-bottom py-3">
                        <h6 class="cp-chart-title mb-0"><i class="bi bi-info-circle me-2"></i>Évaluation</h6>
                        <?php
                            $statutClass = ['envoye' => 'bg-info', 'repondu' => 'bg-success', 'expire' => 'bg-secondary'];
                            $statutText = ['envoye' => 'Envoyé', 'repondu' => 'Répondu', 'expire' => 'Expiré'];
                        ?>
                        <span class="badge <?php echo e($statutClass[$satisfaction->statut] ?? 'bg-secondary'); ?> px-3 py-2">
                            <?php echo e($statutText[$satisfaction->statut] ?? $satisfaction->statut); ?>

                        </span>
                    </div>
                    <div class="p-4">
                        <div class="row g-4 align-items-center text-center">
                            <div class="col-12 border-bottom pb-4 mb-2">
                                <label class="text-muted small text-uppercase fw-bold mb-3">Note de Satisfaction</label>
                                <div class="display-4 mb-2">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                        <i class="bi <?php echo e($i <= $satisfaction->note ? 'bi-star-fill text-warning' : 'bi-star text-muted opacity-25'); ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <h3 class="fw-bold <?php echo e($satisfaction->note >= 4 ? 'text-success' : ($satisfaction->note == 3 ? 'text-warning' : 'text-danger')); ?>"><?php echo e($satisfaction->note); ?>/5</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Commentaire -->
                <div class="cp-chart-card shadow-sm border-0">
                    <div class="cp-chart-header border-bottom py-3">
                        <h6 class="cp-chart-title mb-0"><i class="bi bi-chat-left-text me-2"></i>Commentaire du partenaire</h6>
                    </div>
                    <div class="p-4">
                        <div class="bg-light p-4 rounded-3 min-vh-10 border shadow-sm">
                            <?php if($satisfaction->commentaire): ?>
                                <p class="mb-0 fst-italic fs-5 text-dark">"<?php echo nl2br(e($satisfaction->commentaire)); ?>"</p>
                            <?php else: ?>
                                <p class="text-muted italic mb-0 text-center">Aucun commentaire n'a été laissé par le partenaire.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne Droite: Participants -->
            <div class="col-lg-4">
                <div class="cp-chart-card shadow-sm border-0 sticky-top" style="top: 2rem;">
                    <div class="cp-chart-header border-bottom py-3">
                        <h6 class="cp-chart-title mb-0"><i class="bi bi-people me-2"></i>Intervenants</h6>
                    </div>
                    <div class="p-4">
                        <div class="mb-4">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-2">Partenaire</label>
                            <?php if($satisfaction->partenaire): ?>
                                <div class="d-flex align-items-center bg-light p-3 rounded border">
                                    <div class="me-3 fs-3 text-primary"><i class="bi bi-person-badge"></i></div>
                                    <div>
                                        <h6 class="mb-0 fw-bold"><?php echo e($satisfaction->partenaire->nom); ?></h6>
                                        <small class="text-muted"><?php echo e($satisfaction->partenaire->email ?? 'Aucun email'); ?></small>
                                    </div>
                                </div>
                                <a href="<?php echo e(route('super-admin.partenaires.show', $satisfaction->partenaire->id)); ?>" class="btn btn-sm btn-outline-primary w-100 mt-2">
                                    <i class="bi bi-eye me-1"></i> Fiche Partenaire
                                </a>
                            <?php else: ?>
                                <div class="bg-light p-3 rounded border text-muted italic text-center">Partenaire supprimé ou inconnu</div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-4">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-2">Projet</label>
                            <?php if($satisfaction->projet): ?>
                                <div class="d-flex align-items-center bg-light p-3 rounded border">
                                    <div class="me-3 fs-3 text-success"><i class="bi bi-kanban"></i></div>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-truncate" style="max-width: 200px;" title="<?php echo e($satisfaction->projet->nom); ?>"><?php echo e($satisfaction->projet->nom); ?></h6>
                                        <span class="badge <?php echo e($satisfaction->projet->statut == 'termine' ? 'bg-success' : 'bg-primary'); ?> mt-1">
                                            <?php echo e(ucfirst($satisfaction->projet->statut)); ?>

                                        </span>
                                    </div>
                                </div>
                                <a href="<?php echo e(route('super-admin.projets.show', $satisfaction->projet->id)); ?>" class="btn btn-sm btn-outline-success w-100 mt-2">
                                    <i class="bi bi-eye me-1"></i> Voir Projet
                                </a>
                            <?php else: ?>
                                <div class="bg-light p-3 rounded border text-muted italic text-center">Aucun projet lié</div>
                            <?php endif; ?>
                        </div>

                        <ul class="list-group list-group-flush border-top pt-3">
                            <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center bg-transparent border-0">
                                <span class="text-muted small"><i class="bi bi-send me-2"></i>Envoyé le</span>
                                <span class="fw-medium"><?php echo e($satisfaction->date_envoi ? date('d/m/Y', strtotime($satisfaction->date_envoi)) : 'N/A'); ?></span>
                            </li>
                            <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center bg-transparent border-0">
                                <span class="text-muted small"><i class="bi bi-reply-all me-2"></i>Répondu le</span>
                                <span class="fw-medium"><?php echo e($satisfaction->date_reponse ? date('d/m/Y', strtotime($satisfaction->date_reponse)) : 'N/A'); ?></span>
                            </li>
                        </ul>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/super-admin/satisfaction/show.blade.php ENDPATH**/ ?>