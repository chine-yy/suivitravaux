<?php $__env->startSection('title', 'Satisfaction Partenaire'); ?>

<?php $__env->startSection('breadcrumb'); ?>
<span class="text-muted">Satisfaction</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-dashboard">
    <div class="cp-content">
<div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-star me-2"></i>Satisfaction Partenaire</h1>
                <p class="cp-page-subtitle">Gérez vos enquêtes de satisfaction</p>
            </div>
            <div class="d-flex gap-2">
                <?php if($has('exporter-pdf-satisfaction-partenaire')): ?>
                <button class="btn btn-outline-danger" onclick="exportToPdf('satisfactionTable', 'Liste des satisfactions', 'satisfactions_export')">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Exporter tout
                </button>
                <?php endif; ?>
            </div>
        </div>


        <?php
        $avgNote = $satisfactions->count() > 0 ? round($satisfactions->avg('note'), 1) : 0;
        ?>

        <div class="cp-stats-grid mb-4">
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-success"><i class="bi bi-emoji-smile"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e($avgNote); ?>/5</div>
                    <div class="cp-stat-label">Note Moyenne</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-info"><i class="bi bi-list-check"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e($satisfactions->count()); ?></div>
                    <div class="cp-stat-label">Enquêtes Envoyées</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-green"><i class="bi bi-reply"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e($satisfactions->where('statut', 'repondu')->count()); ?></div>
                    <div class="cp-stat-label">Réponses Reçues</div>
                </div>
            </div>
        </div>

        <!-- Filtre -->
        <div class="cp-chart-card mb-4">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-filter me-2"></i>Filtres de recherche</h6>
            </div>
            <div class="p-4">
                <form action="<?php echo e(route('super-admin.satisfaction.index')); ?>" method="GET" class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label small fw-bold">Partenaire</label>
                        <select name="partenaire_id" class="form-select form-select-sm">
                            <option value="">Tous les partenaires</option>
                            <?php $__currentLoopData = $partenaires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($c->id); ?>" <?php echo e(request('partenaire_id')==$c->id ? 'selected' : ''); ?>>
                                <?php echo e($c->nom); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-bold">Note</label>
                        <select name="note" class="form-select form-select-sm">
                            <option value="">Toutes les notes</option>
                            <?php for($i = 5; $i >= 1; $i--): ?>
                            <option value="<?php echo e($i); ?>" <?php echo e(request('note')==$i ? 'selected' : ''); ?>><?php echo e($i); ?> Étoile(s)
                            </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="bi bi-search me-1"></i> Filtrer
                        </button>
                        <a href="<?php echo e(route('super-admin.satisfaction.index')); ?>"
                            class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-list-ul me-2"></i>Liste des Enquêtes</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="satisfactionTable">
                    <thead>
                        <tr style="background: rgba(99,102,241,.08);">
                            <th>Partenaire</th>
                            <th>Projet</th>
                            <th>Note</th>
                            <th>Commentaire</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $satisfactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $satisfaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($satisfaction->partenaire->nom ?? 'N/A'); ?></td>
                            <td><?php echo e($satisfaction->projet->nom ?? 'N/A'); ?></td>
                            <td>
                                <?php for($i = 1; $i <= 5; $i++): ?> <i
                                    class="bi <?php echo e($i <= $satisfaction->note ? 'bi-star-fill text-primary' : 'bi-star text-muted'); ?>">
                                    </i>
                                    <?php endfor; ?>
                            </td>
                            <td>
                                <span class="text-muted small"><?php echo e(Str::limit($satisfaction->commentaire, 40) ?? 'N/A'); ?></span>
                            </td>
                            <td><?php echo e($satisfaction->date_envoi ? date('d/m/Y', strtotime($satisfaction->date_envoi)) :
                                'N/A'); ?></td>
                            <td>
                                <?php
                                $statutClass = ['envoye' => 'bg-info', 'repondu' => 'bg-success', 'expire' =>
                                'bg-secondary'];
                                $statutText = ['envoye' => 'Envoyé', 'repondu' => 'Répondu', 'expire' => 'Expiré'];
                                ?>
                                <span class="badge <?php echo e($statutClass[$satisfaction->statut] ?? 'bg-secondary'); ?>"><?php echo e($statutText[$satisfaction->statut] ?? $satisfaction->statut); ?></span>
                            </td>
<td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="<?php echo e(route('super-admin.satisfaction.show', $satisfaction->id)); ?>"
                                        class="btn btn-sm btn-outline-info" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('super-admin.export.pdf.direct', ['type' => 'satisfaction', 'id' => $satisfaction->id])); ?>"
                                        class="btn btn-sm btn-outline-secondary" title="Télécharger PDF">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-star display-4"></i>
                                <p class="mt-3">Aucune enquête trouvée</p>
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

<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/super-admin/satisfaction/index.blade.php ENDPATH**/ ?>