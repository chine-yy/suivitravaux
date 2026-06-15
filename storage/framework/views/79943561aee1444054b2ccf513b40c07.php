<?php $__env->startSection('title', 'Mes Rapports'); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-dashboard">
    <div class="cp-content">
        <!-- Page Header -->
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title">Mes Rapports</h1>
                <p class="cp-page-subtitle">Consultez les rapports d'avancement qui vous sont destinés.</p>
            </div>
        </div>

        <?php echo $__env->make('partials.alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="row mt-4">
            <div class="col-12">
                <div class="cp-card-elevated shadow-sm border-0 rounded-4">
                    <div class="cp-card-header bg-white border-bottom-0 py-4 px-4">
                        <h4 class="mb-0 fw-bold text-dark">Historique des rapports</h4>
                    </div>
                    <div class="cp-card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead style="background-color: #f8f9fa;">
                                    <tr>
                                        <th class="px-4 py-3 border-0">Date</th>
                                        <th class="py-3 border-0">Titre</th>
                                        <th class="py-3 border-0">Type</th>
                                        <th class="py-3 border-0">Auteur</th>
                                        <th class="text-end px-4 py-3 border-0">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $rapports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rapport): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td class="px-4 py-4"><?php echo e($rapport->created_at->format('d/m/Y')); ?></td>
                                            <td class="py-4">
                                                <div class="fw-bold text-dark"><?php echo e($rapport->titre); ?></div>
                                                <div class="small text-muted text-truncate" style="max-width: 300px;"><?php echo e(Str::limit($rapport->contenu, 60)); ?></div>
                                            </td>
                                            <td class="py-4">
                                                <span class="badge px-3 py-2" style="background-color: #009A4420; color: #009A44; border: 1px solid #009A4440; border-radius: 6px;">
                                                    <?php echo e($rapport->getTypeLabel()); ?>

                                                </span>
                                            </td>
                                            <td class="py-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-light rounded-circle p-2 me-2">
                                                        <i class="bi bi-person text-warning"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold small"><?php echo e($rapport->auteur ? $rapport->auteur->prenom . ' ' . $rapport->auteur->nom : 'Non spécifié'); ?></div>
                                                        <div class="text-muted extra-small" style="font-size: 0.7rem;"><?php echo e($rapport->auteur?->role?->nom ?? 'Auteur'); ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                             <td class="text-end px-4 py-4">
                                                 <div class="d-flex justify-content-end gap-2">
                                                     <button type="button" class="btn btn-sm p-2 d-flex align-items-center justify-content-center" 
                                                             style="border: 2px solid #ffc107; color: #ffc107; background: transparent; width: 38px; height: 38px; border-radius: 8px;" 
                                                             data-bs-toggle="modal" data-bs-target="#reportModal<?php echo e($rapport->id); ?>" title="Voir le rapport">
                                                         <i class="bi bi-eye fs-5"></i>
                                                     </button>
                                                     
                                                     <a href="<?php echo e(route('partenaire.rapports.voir-pdf', $rapport->id)); ?>" target="_blank"
                                                        class="btn btn-sm p-2 d-flex align-items-center justify-content-center" 
                                                        style="border: 2px solid #0d6efd; color: #0d6efd; background: transparent; width: 38px; height: 38px; border-radius: 8px;" 
                                                        title="Voir le PDF">
                                                         <i class="bi bi-file-earmark-pdf fs-5"></i>
                                                     </a>

                                                     <a href="<?php echo e(route('partenaire.rapports.pdf', $rapport->id)); ?>" 
                                                        class="btn btn-sm p-2 d-flex align-items-center justify-content-center" 
                                                        style="border: 2px solid #198754; color: #198754; background: transparent; width: 38px; height: 38px; border-radius: 8px;" 
                                                        title="Télécharger">
                                                         <i class="bi bi-download fs-5"></i>
                                                     </a>
                                                 </div>
                                             </td>
                                        </tr>

                                        <!-- Modal pour voir le rapport -->
                                        <div class="modal fade" id="reportModal<?php echo e($rapport->id); ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content border-0 rounded-4">
                                                    <div class="modal-header border-bottom-0 py-4 px-4">
                                                        <h5 class="modal-title fw-bold">Détails du Rapport</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <div class="row mb-4">
                                                            <div class="col-md-6">
                                                                <h6 class="text-muted small text-uppercase mb-2">Titre du rapport</h6>
                                                                <p class="fw-bold text-dark fs-5"><?php echo e($rapport->titre); ?></p>
                                                            </div>
                                                            <div class="col-md-6 text-md-end">
                                                                <h6 class="text-muted small text-uppercase mb-2">Envoyé le</h6>
                                                                <p class="fw-bold text-dark fs-5"><?php echo e($rapport->created_at->format('d/m/Y à H:i')); ?></p>
                                                            </div>
                                                        </div>

                                                        <div class="bg-light rounded-4 p-4 mb-4">
                                                            <h6 class="fw-bold text-dark mb-3">Contenu principal</h6>
                                                            <div class="text-muted" style="white-space: pre-wrap;"><?php echo e($rapport->contenu); ?></div>
                                                        </div>

                                                        <?php if($rapport->observations): ?>
                                                        <div class="mb-4">
                                                            <h6 class="fw-bold text-dark mb-2">Observations supplémentaires</h6>
                                                            <p class="text-muted"><?php echo e($rapport->observations); ?></p>
                                                        </div>
                                                        <?php endif; ?>

                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="p-3 bg-white border rounded-3 text-center">
                                                                    <div class="text-muted small mb-1">Avancement</div>
                                                                    <div class="fw-bold fs-4 text-warning"><?php echo e($rapport->avancement_constate); ?>%</div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <div class="p-3 bg-white border rounded-3">
                                                                    <div class="text-muted small mb-1">Auteur du rapport</div>
                                                                    <div class="fw-bold"><?php echo e($rapport->auteur ? $rapport->auteur->prenom . ' ' . $rapport->auteur->nom : 'Non spécifié'); ?></div>
                                                                    <div class="text-muted small"><?php echo e($rapport->auteur?->role?->nom ?? 'Utilisateur'); ?> &middot; <?php echo e($rapport->projet->nom); ?></div>
                                                                </div>
                                                                <div class="p-3 bg-white border rounded-3 mt-2">
                                                                    <div class="text-muted small mb-1">Envoyé par</div>
                                                                    <div class="fw-bold"><?php echo e($rapport->envoyePar ? $rapport->envoyePar->prenom . ' ' . $rapport->envoyePar->nom : $rapport->auteur?->prenom . ' ' . $rapport->auteur?->nom); ?></div>
                                                                    <div class="text-muted small"><?php echo e($rapport->envoyePar?->role?->nom ?? ($rapport->auteur?->role?->nom ?? 'Utilisateur')); ?></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top-0 p-4">
                                                        <button type="button" class="btn px-4 py-2" style="background-color: #6c757d; color: white; border-radius: 8px;" data-bs-dismiss="modal">Fermer</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <i class="bi bi-file-earmark-text display-4 mb-3 opacity-25"></i>
                                                <p>Aucun rapport ne vous a encore été envoyé.</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.partenaire', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/partenaire/rapport/rapports.blade.php ENDPATH**/ ?>