<?php $__env->startSection('title', 'Gestion des Documents'); ?>

<?php $__env->startSection('breadcrumb'); ?>
<span class="text-muted">Documents</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-file-earmark me-2"></i>Gestion des Documents</h1>
                <p class="cp-page-subtitle">Gérez vos documents</p>
            </div>
            <div class="d-flex gap-2">
                <?php if($has('exporter-pdf-documents')): ?>
                <button class="btn btn-outline-danger" onclick="exportToPdf('documentsTable', 'Liste des documents', 'documents_export')">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Exporter tout
                </button>
                <?php endif; ?>
                <?php if($has('create-documents')): ?>
                <a href="<?php echo e(route('role-dynamique.documents.create')); ?>" class="btn btn-primary px-4">
                    <i class="bi bi-plus-circle me-2"></i>Nouveau Document
                </a>
                <?php endif; ?>
            </div>
        </div>


        <!-- Filtre -->
        <div class="cp-chart-card mb-4">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-filter me-2"></i>Filtres de recherche</h6>
            </div>
            <div class="p-4">
                <form action="<?php echo e(route('role-dynamique.documents.index')); ?>" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Nom / Description</label>
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
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Type de Document</label>
                        <select name="type" class="form-select form-select-sm">
                            <option value="">Tous les types</option>
                            <option value="contrat" <?php echo e(request('type')=='contrat' ? 'selected' : ''); ?>>Contrat</option>
                            <option value="facture" <?php echo e(request('type')=='facture' ? 'selected' : ''); ?>>Facture</option>
                            <option value="rapport" <?php echo e(request('type')=='rapport' ? 'selected' : ''); ?>>Rapport</option>
                            <option value="photo" <?php echo e(request('type')=='photo' ? 'selected' : ''); ?>>Photo</option>
                            <option value="plan" <?php echo e(request('type')=='plan' ? 'selected' : ''); ?>>Plan</option>
                            <option value="autre" <?php echo e(request('type')=='autre' ? 'selected' : ''); ?>>Autre</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="bi bi-search me-1"></i> Filtrer
                        </button>
                        <a href="<?php echo e(route('role-dynamique.documents.index')); ?>" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-list-ul me-2"></i>Liste des Documents</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="documentsTable">
                    <thead>
                        <tr style="background: rgba(99,102,241,.08);">
                            <th>Nom</th>
                            <th>Type</th>
                            <th>Projet</th>
                            <th>Catégorie</th>
                            <?php if($has('download-documents')): ?><th>Fichier</th><?php endif; ?>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><strong><?php echo e($document->nom); ?></strong></td>
                            <td>
                                <?php
                                $types = ['contrat' => 'Contrat', 'facture' => 'Facture', 'rapport' => 'Rapport',
                                'photo' => 'Photo', 'plan' => 'Plan', 'autre' => 'Autre'];
                                $displayType = $document->type === 'autre' && $document->type_personnalise
                                ? $document->type_personnalise
                                : ($types[$document->type] ?? $document->type);
                                ?>
                                <span class="badge bg-light text-dark"><?php echo e($displayType); ?></span>
                            </td>
                            <td><?php echo e($document->projet->nom ?? 'N/A'); ?></td>
                            <td><?php echo e($document->categorie ?? 'Non spécifiée'); ?></td>
                            <?php if($has('download-documents')): ?>
                            <td>
                                <?php if($document->fichier): ?>
                                <a href="<?php echo e(asset('storage/' . $document->fichier)); ?>" target="_blank"
                                    class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-download"></i>
                                </a>
                                <?php else: ?>
                                <span class="text-muted">Aucun</span>
                                <?php endif; ?>
                            </td>
                            <?php endif; ?>
                            <td>
                                <span class="badge <?php echo e($document->statut == 'actif' ? 'bg-success' : 'bg-secondary'); ?>">
                                    <?php echo e($document->statut == 'actif' ? 'Actif' : 'Archivé'); ?>

                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="<?php echo e(route('role-dynamique.documents.show', $document->id)); ?>"
                                        class="btn btn-sm btn-outline-info" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if($has('edit-documents')): ?>
                                    <a href="<?php echo e(route('role-dynamique.documents.edit', $document->id)); ?>"
                                        class="btn btn-sm btn-outline-primary" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php if($has('exporter-pdf-documents')): ?>
                                        <?php echo $__env->make('partials.row-export', ['id' => $document->id, 'prefix' => 'document', 'title' => 'Télécharger'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                    <?php endif; ?>
                                    <?php if($has('delete-documents')): ?>
                                    <form action="<?php echo e(route('role-dynamique.documents.destroy', $document->id)); ?>"
                                        method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Supprimer ce document ?')" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="<?php echo e($has('download-documents') ? 7 : 6); ?>" class="text-center py-5 text-muted">
                                <i class="bi bi-file-earmark display-4"></i>
                                <p class="mt-3">Aucun document trouvé</p>
                                <?php if($has('create-documents')): ?>
                                <a href="<?php echo e(route('role-dynamique.documents.create')); ?>">Créer un document</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.role-dynamique', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/role-dynamique/documents/index.blade.php ENDPATH**/ ?>