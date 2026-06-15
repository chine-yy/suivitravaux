<?php $__env->startSection('title', 'Gestion des Fournisseurs'); ?>

<?php $__env->startSection('breadcrumb'); ?>
<span class="app-breadcrumb-item">Fournisseurs</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-dashboard">
    <div class="cp-page-header">
        <div>
            <h1 class="cp-page-title"><i class="bi bi-truck me-2"></i>Gestion des Fournisseurs</h1>
            <p class="cp-page-subtitle">Gérez vos fournisseurs</p>
        </div>
        <div class="d-flex gap-2">
            <?php if($has('exporter-pdf-fournisseurs')): ?>
            <a href="<?php echo e(route('role-dynamique.fournisseurs.export')); ?>" class="btn btn-outline-danger">
                <i class="bi bi-file-earmark-pdf me-2"></i> Exporter
            </a>
            <?php endif; ?>
            <?php if($has('create-fournisseurs')): ?>
            <a href="<?php echo e(route('role-dynamique.fournisseurs.create')); ?>" class="btn btn-primary px-4">
                <i class="bi bi-plus-circle me-2"></i>Nouveau Fournisseur
            </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="cp-chart-card mb-4">
        <div class="cp-chart-header">
            <h6 class="cp-chart-title"><i class="bi bi-filter me-2"></i>Filtres de recherche</h6>
        </div>
        <div class="p-4">
            <form action="<?php echo e(route('role-dynamique.fournisseurs.index')); ?>" method="GET" class="row g-3">
                <div class="col-md-5">
                    <label class="form-label small fw-bold">Nom / Email / Contact</label>
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Rechercher..." value="<?php echo e(request('search')); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Catégorie</label>
                    <select name="categorie" class="form-select form-select-sm">
                        <option value="">Toutes les catégories</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cat); ?>" <?php echo e(request('categorie') == $cat ? 'selected' : ''); ?>><?php echo e($cat); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Statut</label>
                    <select name="statut" class="form-select form-select-sm">
                        <option value="">Tous</option>
                        <option value="actif" <?php echo e(request('statut') == 'actif' ? 'selected' : ''); ?>>Actif</option>
                        <option value="inactif" <?php echo e(request('statut') == 'inactif' ? 'selected' : ''); ?>>Inactif</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Filtrer
                    </button>
                    <a href="<?php echo e(route('role-dynamique.fournisseurs.index')); ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="cp-chart-card">
        <div class="cp-chart-header">
            <h6 class="cp-chart-title"><i class="bi bi-list-ul me-2"></i>Liste des Fournisseurs</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr style="background: rgba(99,102,241,.08);">
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Catégorie</th>
                        <th>Contact</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $fournisseurs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fournisseur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><strong><?php echo e($fournisseur->nom); ?></strong></td>
                        <td><?php echo e($fournisseur->email ?? 'N/A'); ?></td>
                        <td><?php echo e($fournisseur->telephone ?? 'N/A'); ?></td>
                        <td><?php echo e($fournisseur->categorie ?? 'N/A'); ?></td>
                        <td><?php echo e($fournisseur->contact_nom ?? 'N/A'); ?></td>
                        <td>
                            <span class="badge <?php echo e($fournisseur->statut == 'actif' ? 'bg-success' : 'bg-danger'); ?>">
                                <?php echo e(ucfirst($fournisseur->statut)); ?>

                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <?php if($has('view-fournisseurs')): ?>
                                <a href="<?php echo e(route('role-dynamique.fournisseurs.show', $fournisseur->id)); ?>" class="btn btn-sm btn-outline-info" title="Voir">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <?php endif; ?>
                                <?php if($has('edit-fournisseurs')): ?>
                                <a href="<?php echo e(route('role-dynamique.fournisseurs.edit', $fournisseur->id)); ?>" class="btn btn-sm btn-outline-primary" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php endif; ?>
                                <?php if($has('exporter-pdf-fournisseurs')): ?>
                                <a href="<?php echo e(route('role-dynamique.export.pdf.direct', ['type' => 'fournisseur_list', 'id' => $fournisseur->id])); ?>" class="btn btn-sm btn-outline-secondary" title="Exporter">
                                    <i class="bi bi-download"></i>
                                </a>
                                <?php endif; ?>
                                <?php if($has('delete-fournisseurs')): ?>
                                <form action="<?php echo e(route('role-dynamique.fournisseurs.destroy', $fournisseur->id)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer" onclick="return confirm('Supprimer ce fournisseur ?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-truck display-4"></i>
                            <p class="mt-3">Aucun fournisseur trouvé</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.role-dynamique', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/role-dynamique/fournisseurs/index.blade.php ENDPATH**/ ?>