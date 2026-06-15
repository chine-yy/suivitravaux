<div class="cp-card-body p-0">
    <div class="table-responsive">
        <table class="table table-hover mb-0" id="rapportsTable">
            <thead class="table-light">
                <tr>
                    <th><input type="checkbox" class="form-check-input" id="selectAll"></th>
                    <th>Date</th>
                    <th>Titre</th>
                    <th>Projet</th>
                    <th>Auteur</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rapport): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr data-statut="<?php echo e($rapport->statut); ?>">
                    <td><input type="checkbox" class="form-check-input"></td>
                    <td><?php echo e(\Carbon\Carbon::parse($rapport->created_at)->format('d/m/Y')); ?></td>
                    <td>
                        <strong><?php echo e($rapport->titre); ?></strong>
                        <?php if($rapport->description): ?>
                        <br><small class="text-muted text-truncate" style="max-width: 200px;"><?php echo e(Str::limit($rapport->description, 50)); ?></small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge bg-secondary"><?php echo e($rapport->projet->nom ?? 'N/A'); ?></span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle me-3 bg-light text-green border border-green-subtle shadow-sm">
                                <?php echo e(strtoupper(substr($rapport->auteur->name ?? 'U', 0, 1))); ?>

                            </div>
                            <div class="d-flex flex-column text-start">
                                <span class="fw-bold text-dark"><?php echo e($rapport->auteur->prenom ?? ''); ?> <?php echo e($rapport->auteur->name ?? 'N/A'); ?></span>
                                <small class="text-green fw-medium"><i class="bi bi-shield-lock me-1"></i><?php echo e($rapport->auteur->role->nom ?? 'Auteur'); ?></small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <?php if($rapport->statut === 'valide'): ?>
                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Validé</span>
                        <?php elseif($rapport->statut === 'rejete'): ?>
                        <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Rejeté</span>
                        <?php else: ?>
                        <span class="badge bg-primary"><i class="bi bi-hourglass-split me-1"></i>En attente</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="d-flex gap-2 justify-content-center">
                            <button class="btn btn-sm btn-icon btn-outline-info" title="Voir le détail" data-bs-toggle="modal"
                                data-bs-target="#viewRapportModal<?php echo e($rapport->id); ?>">
                                <i class="bi bi-eye"></i>
                            </button>
                            <?php if($canPermission('exporter-pdf-rapports')): ?>
                            <a href="<?php echo e(route('role-dynamique.export.pdf.direct', ['type' => 'rapport', 'id' => $rapport->id])); ?>" class="btn btn-sm btn-icon btn-outline-primary" title="Telecharger en PDF">
                                <i class="bi bi-file-earmark-pdf"></i>
                            </a>
                            <?php endif; ?>
                            <?php
                                $user = auth()->user();
                                $isAdminOrSuper = $user && ($user->isAdminEntreprise() || $user->isSuperAdmin());
                                $isNonOwnerReport = $rapport->auteur_id != auth()->id();
                                $canQuickStatusEdit = $isAdminOrSuper && $isNonOwnerReport;
                                $currentStatut = $rapport->statut;
                                $approuveStatuts = ['valide', 'approuve'];
                                $soumisStatuts = ['soumis', 'en_revision', 'en_revue'];
                            ?>

                            <?php if($canPermission('edit-rapports')): ?>
                                <?php if($canQuickStatusEdit): ?>
                                <button type="button" class="btn btn-sm btn-icon btn-outline-warning" title="Modifier le statut" data-bs-toggle="modal" data-bs-target="#statusOnlyModal<?php echo e($rapport->id); ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <?php else: ?>
                                <a href="<?php echo e(route('role-dynamique.rapports.edit', $rapport->id)); ?>" class="btn btn-sm btn-icon btn-outline-warning" title="Modifier le rapport">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if($canPermission('delete-rapports')): ?>
                            <form action="<?php echo e(route('role-dynamique.rapports.destroy', $rapport->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Voulez-vous vraiment supprimer ce rapport ?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Supprimer le rapport">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                            <?php if($canPermission('envoyer-partenaire-rapports') && !$rapport->est_envoye): ?>
                            <form action="<?php echo e(route('role-dynamique.rapports.envoyer-partenaire', $rapport->id)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-sm btn-icon btn-outline-success" title="Envoyer au(x) partenaire(s)" onclick="return confirm('Envoyer ce rapport aux partenaires du projet ?')">
                                    <i class="bi bi-send"></i>
                                </button>
                            </form>
                            <?php elseif($canPermission('envoyer-partenaire-rapports') && $rapport->est_envoye): ?>
                            <span class="btn btn-sm btn-icon btn-outline-secondary" title="Deja envoye aux partenaires">
                                <i class="bi bi-send-check-fill text-success"></i>
                            </span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                        <p class="mt-3 text-muted">Aucun rapport trouvé</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rapport): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php
    $user = auth()->user();
    $isAdminOrSuper = $user && ($user->isAdminEntreprise() || $user->isSuperAdmin());
    $isNonOwnerReport = $rapport->auteur_id != auth()->id();
    $canQuickStatusEdit = $isAdminOrSuper && $isNonOwnerReport;
    $currentStatut = $rapport->statut;
    $approuveStatuts = ['valide', 'approuve'];
    $soumisStatuts = ['soumis', 'en_revision', 'en_revue'];
?>

<?php if($canQuickStatusEdit): ?>
<div class="modal fade js-rapport-modal" id="statusOnlyModal<?php echo e($rapport->id); ?>" tabindex="-1" aria-labelledby="statusOnlyModalLabel<?php echo e($rapport->id); ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <form action="<?php echo e(route('role-dynamique.rapports.update', $rapport->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="statusOnlyModalLabel<?php echo e($rapport->id); ?>">
                        Modifier le statut du rapport #<?php echo e($rapport->id); ?>

                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">
                        Ce rapport ne vous appartient pas. Vous pouvez uniquement modifier son statut.
                    </p>

                    <label for="statut_<?php echo e($rapport->id); ?>" class="form-label">Statut du Rapport <span class="text-danger">*</span></label>
                    <select name="statut" id="statut_<?php echo e($rapport->id); ?>" class="form-select" required>
                        <option value="soumis" <?php echo e(in_array($currentStatut, $soumisStatuts) ? 'selected' : ''); ?>>Soumis / En révision</option>
                        <option value="valide" <?php echo e(in_array($currentStatut, $approuveStatuts) ? 'selected' : ''); ?>>Validé</option>
                        <option value="rejete" <?php echo e($currentStatut == 'rejete' ? 'selected' : ''); ?>>Rejeté</option>
                        <option value="brouillon" <?php echo e($currentStatut == 'brouillon' ? 'selected' : ''); ?>>Brouillon</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-green">
                        <i class="bi bi-check-circle me-2"></i>Enregistrer le statut
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="modal fade js-rapport-modal" id="viewRapportModal<?php echo e($rapport->id); ?>" tabindex="-1"
    aria-labelledby="viewRapportModalLabel<?php echo e($rapport->id); ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewRapportModalLabel<?php echo e($rapport->id); ?>">
                    <i class="bi bi-file-earmark-text me-2"></i><?php echo e($rapport->titre); ?>

                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Projet:</strong> <span class="badge bg-light text-dark border"><?php echo e($rapport->projet->nom ?? 'N/A'); ?></span></p>
                        <p><strong>Auteur:</strong> <span class="text-green fw-bold"><?php echo e($rapport->auteur->prenom ?? ''); ?> <?php echo e($rapport->auteur->name ?? 'N/A'); ?></span> <small class="text-muted">(<?php echo e($rapport->auteur->role->nom ?? 'Rôle'); ?>)</small></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Date de création:</strong> <?php echo e(\Carbon\Carbon::parse($rapport->created_at)->format('d/m/Y H:i')); ?></p>
                        <p><strong>Statut:</strong>
                            <?php if($rapport->statut === 'valide'): ?>
                            <span class="badge bg-success">Validé</span>
                            <?php elseif($rapport->statut === 'rejete'): ?>
                            <span class="badge bg-danger">Rejeté</span>
                            <?php else: ?>
                            <span class="badge bg-primary">En attente</span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                <?php if($rapport->description): ?>
                <div class="mb-3">
                    <strong>Description:</strong>
                    <p class="mt-2"><?php echo e($rapport->description); ?></p>
                </div>
                <?php endif; ?>
                <?php if($rapport->contenu): ?>
                <div>
                    <strong>Contenu:</strong>
                    <div class="bg-light p-3 rounded mt-2"><?php echo e($rapport->contenu); ?></div>
                </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer bg-light border-top-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                <?php if($canPermission('exporter-pdf-rapports')): ?>
                <a href="<?php echo e(route('role-dynamique.export.pdf.direct', ['type' => 'rapport', 'id' => $rapport->id])); ?>" class="btn btn-green">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Télécharger PDF
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php if($items instanceof \Illuminate\Contracts\Pagination\Paginator || $items instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator): ?>
<div class="cp-card-footer d-flex justify-content-between align-items-center">
    <div class="text-muted">
        Affichage de <?php echo e($items->firstItem()); ?> à <?php echo e($items->lastItem()); ?> sur <?php echo e($items->total()); ?> résultats
    </div>
    <?php echo e($items->links()); ?>

</div>
<?php endif; ?>
<?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/role-dynamique/rapports/_table.blade.php ENDPATH**/ ?>