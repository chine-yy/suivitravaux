<?php $__env->startSection('title', 'Planification des Rendez-vous'); ?>

<?php $__env->startSection('breadcrumb'); ?>
<span class="text-muted">Rendez-vous</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-calendar-event me-2"></i>Planification des Rendez-vous</h1>
                <p class="cp-page-subtitle">Gérez vos rendez-vous</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-danger" onclick="exportToPdf('rendezvousTable', 'Liste des rendez-vous', 'rendezvous_export')">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Exporter tout
                </button>
                <a href="<?php echo e(route('super-admin.rendezvous.create')); ?>" class="btn btn-primary px-4">
                    <i class="bi bi-plus-circle me-2"></i>Nouveau Rendez-vous
                </a>
            </div>
        </div>


        <div class="cp-chart-card mb-4">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-filter me-2"></i>Filtres de recherche</h6>
            </div>
            <div class="p-4">
                <form action="<?php echo e(route('super-admin.rendezvous.index')); ?>" method="GET" class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Date</label>
                        <input type="date" name="date" class="form-control form-control-sm"
                            value="<?php echo e(request('date')); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Projet</label>
                        <select name="projet_id" class="form-select form-select-sm">
                            <option value="">Tous les projets</option>
                            <?php $__currentLoopData = $projets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $projet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($projet->id); ?>" <?php echo e(request('projet_id')==$projet->id ? 'selected' : ''); ?>><?php echo e($projet->nom); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Lieu</label>
                        <input type="text" name="lieu" class="form-control form-control-sm"
                            placeholder="Ville, bureau..." value="<?php echo e(request('lieu')); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Statut</label>
                        <select name="statut" class="form-select form-select-sm">
                            <option value="">Tous les statuts</option>
                            <option value="planifie" <?php echo e(request('statut')=='planifie' ? 'selected' : ''); ?>>Planifié
                            </option>
                            <option value="confirme" <?php echo e(request('statut')=='confirme' ? 'selected' : ''); ?>>Confirmé
                            </option>
                            <option value="termine" <?php echo e(request('statut')=='termine' ? 'selected' : ''); ?>>Terminé
                            </option>
                            <option value="annule" <?php echo e(request('statut')=='annule' ? 'selected' : ''); ?>>Annulé</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-sm btn-primary text-nowrap">
                            <i class="bi bi-search me-1"></i> Filtrer
                        </button>
                        <a href="<?php echo e(route('super-admin.rendezvous.index')); ?>"
                            class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>


        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-list-ul me-2"></i>Liste des Rendez-vous</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="rendezvousTable">
                    <thead>
                        <tr style="background: rgba(99,102,241,.08);">
                            <th>Titre</th>
                            <th>Projet</th>
                            <th>Date & Heure</th>
                            <th>Durée</th>
                            <th>Lieu</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $rendezvous; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rdv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><strong><?php echo e($rdv->titre); ?></strong></td>
                            <td><span class="text-primary"><?php echo e($rdv->projet->nom ?? 'N/A'); ?></span></td>
                            <td>
                                <?php if($rdv->date_heure): ?>
                                <?php echo e(date('d/m/Y', strtotime($rdv->date_heure))); ?> à <?php echo e(date('H:i',
                                strtotime($rdv->date_heure))); ?>

                                <?php else: ?>
                                N/A
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($rdv->duree_minutes); ?> min</td>
                            <td><?php echo e($rdv->lieu ?? 'N/A'); ?></td>
                            <td><?php echo e(Str::limit($rdv->description, 50) ?? 'N/A'); ?></td>
                            <td>
                                <?php $types = ['reunion' => 'Réunion', 'visite' => 'Visite', 'appel' => 'Appel', 'autre'
                                => 'Autre']; ?>
                                <span class="badge bg-light text-dark">
                                    <?php echo e($rdv->type == 'autre' ? ($rdv->type_autre ?? 'Autre') : ($types[$rdv->type] ??
                                    $rdv->type)); ?>

                                </span>
                            </td>
                            <td>
                                <?php
                                $statutClass = ['planifie' => 'bg-info', 'confirme' => 'bg-success', 'termine' =>
                                'bg-secondary', 'annule' => 'bg-danger'];
                                $statutText = ['planifie' => 'Planifié', 'confirme' => 'Confirmé', 'termine' =>
                                'Terminé', 'annule' => 'Annulé'];
                                ?>
                                <span class="badge <?php echo e($statutClass[$rdv->statut] ?? 'bg-secondary'); ?>"><?php echo e($statutText[$rdv->statut] ?? $rdv->statut); ?></span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="<?php echo e(route('super-admin.rendezvous.show', $rdv->id)); ?>"
                                        class="btn btn-sm btn-outline-info" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('super-admin.rendezvous.edit', $rdv->id)); ?>"
                                        class="btn btn-sm btn-outline-primary" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="<?php echo e(route('super-admin.export.pdf.direct', ['type' => 'rendezvous', 'id' => $rdv->id])); ?>"
                                        class="btn btn-sm btn-outline-secondary" title="Télécharger PDF">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <form action="<?php echo e(route('super-admin.rendezvous.destroy', $rdv->id)); ?>" method="POST"
                                        class="d-inline">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Supprimer ce rendez-vous ?')" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-event display-4"></i>
                                <p class="mt-3">Aucun rendez-vous trouvé</p>
                <button class="btn btn-outline-danger" onclick="exportToPdf('id="rendezvousTable"', 'Liste des rendezvous', 'rendezvous_export')">                    <i class="bi bi-file-earmark-pdf me-2"></i> Exporter                </button>
                                <a href="<?php echo e(route('super-admin.rendezvous.create')); ?>">Créer un rendez-vous</a>
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

<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/super-admin/rendezvous/index.blade.php ENDPATH**/ ?>