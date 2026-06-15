<?php $__env->startSection('title', 'Sous-Traitances'); ?>

<?php $__env->startSection('breadcrumb'); ?>
<span class="cp-breadcrumb-item">Sous-Traitances</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-people me-2"></i>Sous-Traitances</h1>
                <p class="cp-page-subtitle">Gérez les interventions de sous-traitance sur vos projets</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-danger" onclick="exportToPdf('sous-traitancesTable', 'Liste des sous-traitances', 'sous-traitances_export')">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Exporter tout
                </button>
                <a href="<?php echo e(route('super-admin.dashboard')); ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>


            <div class="cp-stats-grid mb-4">
                <div class="cp-stat-card">
                    <div class="cp-stat-icon cp-bg-primary"><i class="bi bi-tools"></i></div>
                    <div class="cp-stat-content">
                        <div class="cp-stat-value"><?php echo e($sousTraitances->count()); ?></div>
                        <div class="cp-stat-label">Total</div>
                    </div>
                </div>
                <div class="cp-stat-card">
                    <div class="cp-stat-icon cp-bg-green"><i class="bi bi-hourglass-split"></i></div>
                    <div class="cp-stat-content">
                        <div class="cp-stat-value"><?php echo e($sousTraitances->where('statut', 'en_cours')->count()); ?></div>
                        <div class="cp-stat-label">En cours</div>
                    </div>
                </div>
                <div class="cp-stat-card">
                    <div class="cp-stat-icon cp-bg-success"><i class="bi bi-check-lg"></i></div>
                    <div class="cp-stat-content">
                        <div class="cp-stat-value"><?php echo e($sousTraitances->where('statut', 'terminee')->count()); ?></div>
                        <div class="cp-stat-label">Terminées</div>
                    </div>
                </div>
            </div>

            <!-- Filtre -->
            <div class="cp-chart-card mb-4">
                <div class="cp-chart-header">
                    <h6 class="cp-chart-title"><i class="bi bi-filter me-2"></i>Filtres de recherche</h6>
                </div>
                <div class="p-4">
                    <form action="<?php echo e(route('super-admin.sous-traitances.index')); ?>" method="GET" class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">Entreprise / Contact</label>
                            <input type="text" name="search" class="form-control form-control-sm"
                                placeholder="Rechercher..." value="<?php echo e(request('search')); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Projet</label>
                            <select name="projet_id" class="form-select form-select-sm">
                                <option value="">Tous les projets</option>
                                <?php $__currentLoopData = $projets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $projet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($projet->id); ?>" <?php echo e(request('projet_id')==$projet->id ? 'selected' : ''); ?>>
                                    <?php echo e($projet->nom); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Statut</label>
                            <select name="statut" class="form-select form-select-sm">
                                <option value="">Tous</option>
                                <option value="en_attente" <?php echo e(request('statut')=='en_attente' ? 'selected' : ''); ?>>En
                                    attente</option>
                                <option value="en_cours" <?php echo e(request('statut')=='en_cours' ? 'selected' : ''); ?>>En cours
                                </option>
                                <option value="terminee" <?php echo e(request('statut')=='terminee' ? 'selected' : ''); ?>>Terminée
                                </option>
                                <option value="annule" <?php echo e(request('statut')=='annule' ? 'selected' : ''); ?>>Annulée
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-sm btn-primary w-100">
                                <i class="bi bi-search me-1"></i> Filtrer
                            </button>
                            <a href="<?php echo e(route('super-admin.sous-traitances.index')); ?>"
                                class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="cp-chart-card">
                <div class="cp-chart-header">
                    <h6 class="cp-chart-title"><i class="bi bi-list-ul me-2"></i>Liste des Sous-Traitances</h6>
                    <a href="<?php echo e(route('super-admin.sous-traitances.create')); ?>" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle me-1"></i> Nouvelle sous-traitance
                    </a>
                </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="sous-traitancesTable">
                    <thead>
                        <tr>
                            <th>Entreprise</th>
                            <th>Projet</th>
                            <th>Contact</th>
                            <th>Employés</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $sousTraitances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <strong><?php echo e($st->nom_entreprise); ?></strong>
                                <?php if($st->description_tache): ?>
                                <br><small class="text-muted"><?php echo e(Str::limit($st->description_tache, 50)); ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($st->projet?->nom ?? 'N/A'); ?></td>
                            <td>
                                <?php if($st->contact_nom): ?>
                                <?php echo e($st->contact_prenom); ?> <?php echo e($st->contact_nom); ?>

                                <?php if($st->contact_telephone): ?>
                                <br><small class="text-muted"><i class="bi bi-telephone me-1"></i><?php echo e($st->contact_telephone); ?></small>
                                <?php endif; ?>
                                <?php else: ?>
                                N/A
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info"><?php echo e($st->nombre_employes); ?></span>
                            </td>
                            <td>
                                <?php
                                $statusBadge = [
                                'en_attente' => 'bg-secondary',
                                'en_cours' => 'bg-primary',
                                'terminee' => 'bg-success',
                                'annule' => 'bg-danger'
                                ][$st->statut] ?? 'bg-secondary';
                                $statusText = [
                                'en_attente' => 'En attente',
                                'en_cours' => 'En cours',
                                'terminee' => 'Terminée',
                                'annule' => 'Annulé'
                                ][$st->statut] ?? ucfirst($st->statut ?? 'N/A');
                                ?>
                                <span class="badge <?php echo e($statusBadge); ?>"><?php echo e($statusText); ?></span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="<?php echo e(route('super-admin.sous-traitances.show', $st->id)); ?>"
                                        class="btn btn-sm btn-outline-info" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('super-admin.sous-traitances.edit', $st->id)); ?>"
                                        class="btn btn-sm btn-outline-primary" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php echo $__env->make('partials.row-export', ['id' => $st->id, 'prefix' => 'soustraitance', 'title' => 'Sous-traitance'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                    <form action="<?php echo e(route('super-admin.sous-traitances.destroy', $st->id)); ?>"
                                        method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Supprimer cette sous-traitance ?')"
                                            title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-people display-4"></i>
                                <p class="mt-3">Aucune sous-traitance enregistrée</p>
                                <a href="<?php echo e(route('super-admin.sous-traitances.create')); ?>"
                                    class="btn btn-primary">Ajouter une sous-traitance</a>
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

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/super-admin/sous-traitances/index.blade.php ENDPATH**/ ?>