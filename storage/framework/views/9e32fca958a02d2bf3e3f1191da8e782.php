<?php $__env->startSection('title', 'Gestion des Incidents'); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-incidents">
    <div class="cp-content">
        <!-- Page Header -->
        <div class="cp-page-header d-flex justify-content-between align-items-center">
            <div>
                <h1 class="cp-page-title">Gestion des Incidents</h1>
                <p class="cp-page-subtitle">Vue d'ensemble de tous les incidents signalés sur la plateforme</p>
            </div>
<div class="d-flex gap-2">
                <button class="btn btn-outline-danger" onclick="exportToPdf('incidentsTable', 'Liste des incidents', 'incidents_export')">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Exporter tout
                </button>
                <a href="<?php echo e(route('super-admin.incidents.create')); ?>" class="btn btn-green btn-with-border">
                    <i class="bi bi-plus-circle me-2"></i>Enregistrer un Incident
                </a>
            </div>
        </div>


        <div class="cp-card mb-4 shadow-sm border-0">
            <div class="cp-card-header bg-light py-3">
                <h6 class="cp-card-title mb-0"><i class="bi bi-filter me-2 text-green"></i>Filtres de recherche</h6>
            </div>
            <div class="p-4">
                <form action="<?php echo e(route('super-admin.incidents.index')); ?>" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Titre de l'Incident</label>
                        <input type="text" name="titre" class="form-control form-control-sm" placeholder="Rechercher..." value="<?php echo e(request('titre')); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Projet</label>
                        <select name="projet_id" class="form-select form-select-sm">
                            <option value="">Tous les projets</option>
                            <?php $__currentLoopData = $projets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $projet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($projet->id); ?>" <?php echo e(request('projet_id') == $projet->id ? 'selected' : ''); ?>><?php echo e($projet->nom); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Statut</label>
                        <select name="statut" class="form-select form-select-sm">
                            <option value="">Tous les statuts</option>
                            <option value="ouvert" <?php echo e(request('statut') == 'ouvert' ? 'selected' : ''); ?>>Ouvert</option>
                            <option value="en_traitement" <?php echo e(request('statut') == 'en_traitement' ? 'selected' : ''); ?>>En cours</option>
                            <option value="resolu" <?php echo e(request('statut') == 'resolu' ? 'selected' : ''); ?>>Résolu</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Gravité</label>
                        <select name="gravite" class="form-select form-select-sm">
                            <option value="">Toutes les gravités</option>
                            <option value="faible" <?php echo e(request('gravite') == 'faible' ? 'selected' : ''); ?>>Faible</option>
                            <option value="moyen" <?php echo e(request('gravite') == 'moyen' ? 'selected' : ''); ?>>Moyen</option>
                            <option value="critique" <?php echo e(request('gravite') == 'critique' ? 'selected' : ''); ?>>Critique</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-sm btn-green w-100">
                            <i class="bi bi-search me-1"></i> Filtrer
                        </button>
                        <a href="<?php echo e(route('super-admin.incidents.index')); ?>" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>


        <!-- Statistics -->
        <div class="cp-stats-grid mb-4">
            <div class="cp-stat-card">
                <div class="cp-stat-icon bg-soft-orange text-green"><i class="bi bi-exclamation-triangle"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e($totalIncidents); ?></div>
                    <div class="cp-stat-label">Total Incidents</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-warning"><i class="bi bi-hourglass-split"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e($openIncidents); ?></div>
                    <div class="cp-stat-label">Ouverts / En cours</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-success"><i class="bi bi-check-circle"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e($resolvedIncidents); ?></div>
                    <div class="cp-stat-label">Résolus</div>
                </div>
            </div>
        </div>

        <!-- Incidents Table -->
        <div class="cp-card">
            <div class="cp-card-header d-flex justify-content-between align-items-center">
                <h5 class="cp-card-title">
                    <i class="bi bi-list-ul me-2 text-green"></i>Liste Globale des Incidents
                </h5>
            </div>
            <div class="cp-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="incidentsTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Incident</th>
                                <th>Projet</th>
                                <th>Signalé par</th>
                                <th>Gravité</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $incidents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $incident): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold"><?php echo e($incident->titre); ?></div>
                                    <div class="text-muted small text-truncate" style="max-width: 250px;"><?php echo e($incident->description); ?></div>
                                </td>
                                <td>
                                    <span class="badge bg-soft-info text-info">
                                        <?php echo e($incident->projet->nom ?? 'N/A'); ?>

                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-soft-orange text-green d-flex align-items-center justify-content-center me-2" style="width: 28px; height: 28px; font-size: 0.8rem;">
                                            <?php echo e(substr($incident->signalePar->name ?? '?', 0, 1)); ?>

                                        </div>
                                        <span class="small"><?php echo e($incident->signalePar->name ?? 'Système'); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                        $graviteClass = [
                                            'critique' => 'bg-danger',
                                            'moyen' => 'bg-warning text-dark',
                                            'faible' => 'bg-info'
                                        ][$incident->gravite] ?? 'bg-secondary';
                                    ?>
                                    <span class="badge <?php echo e($graviteClass); ?>"><?php echo e(ucfirst($incident->gravite)); ?></span>
                                </td>
                                <td>
                                    <?php
                                        $statutClass = [
                                            'resolu' => 'bg-success',
                                            'en_traitement' => 'bg-primary',
                                            'ouvert' => 'bg-warning text-dark'
                                        ][$incident->statut] ?? 'bg-secondary';
                                        $statutLabel = [
                                            'resolu' => 'Résolu',
                                            'en_traitement' => 'En cours',
                                            'ouvert' => 'Ouvert'
                                        ][$incident->statut] ?? $incident->statut;
                                    ?>
                                    <span class="badge <?php echo e($statutClass); ?>"><?php echo e($statutLabel); ?></span>
                                </td>
                                <td><?php echo e($incident->created_at->format('d/m/Y')); ?></td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="<?php echo e(route('super-admin.incidents.show', $incident->id)); ?>" class="btn btn-sm btn-outline-info" title="Voir détails">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <?php echo $__env->make('partials.row-export', ['id' => $incident->id, 'prefix' => 'incident', 'title' => 'Incident - ' . ($incident->titre ?? $incident->id)], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                        <a href="<?php echo e(route('super-admin.incidents.edit', $incident->id)); ?>" class="btn btn-sm btn-outline-green" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="<?php echo e(route('super-admin.incidents.destroy', $incident->id)); ?>" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet incident ?');" class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <div class="mb-3">
                                        <i class="bi bi-shield-check display-4 text-green opacity-50"></i>
                                    </div>
                                    <h5>Aucun incident à signaler</h5>
                                    <p class="small">Tous les incidents résolus ou aucun incident n'a été créé.</p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="cp-card-footer border-top-0 bg-transparent ps-4">
                <?php echo e($incidents->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .bg-soft-orange { background-color: rgba(0, 154, 68, 0.1); color: #009A44; }
    .bg-soft-info { background-color: rgba(13, 202, 240, 0.1); color: #0dcaf0; }
    .text-green { color: #009A44 !important; }
    .btn-outline-green { color: #009A44; border-color: #009A44; }
    .btn-outline-green:hover { background-color: #009A44; color: white; }
    .cp-bg-green { background: #009A44; color: white; }
    .btn-green { background: #009A44; color: white; border: none; transition: all 0.3s ease; }
    .btn-green:hover { background: #007a35; color: white; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0, 154, 68, 0.3); }
    .btn-with-border { border: 2px solid #009A44 !important; }
    .btn-with-border:hover { border-color: #007a35 !important; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    var table = document.getElementById(tableId);
    if (!table) return;
    var csv = [];
    var rows = table.querySelectorAll('tr');
    for (var i = 0; i < rows.length; i++) {
        var cols = rows[i].querySelectorAll('th, td');
        var row = [];
        for (var j = 0; j < cols.length; j++) {
            var text = cols[j].innerText.replace(/(||)/gm, ' ').replace(/"/g, '""').trim();
            row.push('"' + text + '"');
        }
        csv.push(row.join(';'));
    }
    var blob = new Blob(['\uFEFF' + csv.join('')], { type: 'text/csv;charset=utf-8;' });
    var link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = (filename || 'export') + '_' + new Date().toISOString().slice(0,10) + '.csv';
    link.click();
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/super-admin/incidents/index.blade.php ENDPATH**/ ?>