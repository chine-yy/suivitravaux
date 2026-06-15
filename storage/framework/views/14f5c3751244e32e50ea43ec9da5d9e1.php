<?php $__env->startSection('title', 'Gestion des Contrats'); ?>

<?php $__env->startSection('breadcrumb'); ?>
<span class="text-muted">Contrats</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-file-earmark-text me-2"></i>Gestion des Contrats</h1>
                <p class="cp-page-subtitle">Gérez vos contrats</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?php echo e(route('super-admin.contrats.export-pdf')); ?><?php echo e(request()->getQueryString() ? '?' . request()->getQueryString() : ''); ?>"
                   class="btn btn-outline-danger">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Exporter tout
                </a>
                <a href="<?php echo e(route('super-admin.contrats.create')); ?>" class="btn btn-primary px-4">
                    <i class="bi bi-plus-circle me-2"></i>Nouveau Contrat
                </a>
            </div>
        </div>


        <!-- Filtre -->
        <div class="cp-chart-card mb-4">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-filter me-2"></i>Filtres de recherche</h6>
            </div>
            <div class="p-4">
                <form action="<?php echo e(route('super-admin.contrats.index')); ?>" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">N° Contrat / Objet</label>
                        <input type="text" name="search" class="form-control form-control-sm"
                            placeholder="Rechercher..." value="<?php echo e(request('search')); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Statut</label>
                        <select name="statut" class="form-select form-select-sm">
                            <option value="">Tous les statuts</option>
                            <option value="brouillon" <?php echo e(request('statut')=='brouillon' ? 'selected' : ''); ?>>Brouillon
                            </option>
                            <option value="signe" <?php echo e(request('statut')=='signe' ? 'selected' : ''); ?>>Signé</option>
                            <option value="en_cours" <?php echo e(request('statut')=='en_cours' ? 'selected' : ''); ?>>En cours
                            </option>
                            <option value="termine" <?php echo e(request('statut')=='termine' ? 'selected' : ''); ?>>Terminé
                            </option>
                            <option value="annule" <?php echo e(request('statut')=='annule' ? 'selected' : ''); ?>>Annulé</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="bi bi-search me-1"></i> Filtrer
                        </button>
                        <a href="<?php echo e(route('super-admin.contrats.index')); ?>" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-list-ul me-2"></i>Liste des Contrats</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="contratsTable">
                    <thead>
                        <tr style="background: rgba(99,102,241,.08);">
                            <th>N° Contrat</th>
                            <th>Projet</th>
                            <th>Type</th>
                            <th>Montant</th>
                            <th>Date Début</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $contrats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contrat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><strong><?php echo e($contrat->numero_contrat); ?></strong></td>
                            <td><?php echo e($contrat->projet->nom ?? 'N/A'); ?></td>
                            <td>
                                <?php
                                $types = ['prestation' => 'Prestation', 'marche' => 'Marché', 'sous_traitance' =>
                                'Sous-traitance', 'autre' => 'Autre'];
                                ?>
                                <span class="badge bg-light text-dark"><?php echo e($types[$contrat->type] ?? $contrat->type); ?></span>
                            </td>
                            <td><?php echo e(number_format($contrat->montant, 0, ',', ' ')); ?> FCFA</td>
                            <td><?php echo e($contrat->date_debut ? date('d/m/Y', strtotime($contrat->date_debut)) : 'N/A'); ?></td>
                            <td>
                                <?php
                                $statusClass = ['brouillon' => 'bg-secondary', 'signe' => 'bg-info', 'en_cours' =>
                                'bg-primary', 'termine' => 'bg-success', 'annule' => 'bg-danger'];
                                $statusText = ['brouillon' => 'Brouillon', 'signe' => 'Signé', 'en_cours' => 'En cours',
                                'termine' => 'Terminé', 'annule' => 'Annulé'];
                                ?>
                                <span class="badge <?php echo e($statusClass[$contrat->statut] ?? 'bg-secondary'); ?>"><?php echo e($statusText[$contrat->statut] ?? $contrat->statut); ?></span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="<?php echo e(route('super-admin.contrats.show', $contrat->id)); ?>"
                                        class="btn btn-sm btn-outline-info" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('super-admin.export.pdf.direct', ['type' => 'contrat', 'id' => $contrat->id])); ?>"
                                        class="btn btn-sm btn-outline-secondary" title="Télécharger">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <?php if(!$contrat->est_envoye_partenaire): ?>
                                    <form action="<?php echo e(route('super-admin.contrats.envoyer-partenaire', $contrat->id)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Envoyer au partenaire" onclick="return confirm('Envoyer ce contrat au partenaire ?')">
                                            <i class="bi bi-send"></i>
                                        </button>
                                    </form>
                                    <?php else: ?>
                                    <span class="btn btn-sm btn-outline-secondary" title="Déjà envoyé au partenaire">
                                        <i class="bi bi-check-circle text-success"></i>
                                    </span>
                                    <?php endif; ?>
                                    <a href="<?php echo e(route('super-admin.contrats.edit', $contrat->id)); ?>"
                                        class="btn btn-sm btn-outline-primary" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="<?php echo e(route('super-admin.contrats.destroy', $contrat->id)); ?>"
                                        method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Supprimer ce contrat ?')" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-file-earmark-text display-4"></i>
                                <p class="mt-3">Aucun contrat trouvé</p>
                                <a href="<?php echo e(route('super-admin.contrats.create')); ?>">Créer un contrat</a>
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

<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/super-admin/contrats/index.blade.php ENDPATH**/ ?>