<?php $__env->startSection('title', 'Gestion des Tâches'); ?>

<?php $__env->startSection('breadcrumb'); ?>
<span class="text-muted">Tâches</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-list-task me-2"></i>Gestion des Tâches</h1>
                <p class="cp-page-subtitle">Visualisez et gérez toutes les tâches des projets</p>
            </div>
<div class="d-flex gap-2">
                <button class="btn btn-outline-danger" onclick="exportToPdf('tachesTable', 'Liste des taches', 'taches_export')">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Exporter tout
                </button>
                <a href="<?php echo e(route('super-admin.taches.create')); ?>" class="btn btn-primary px-4">
                    <i class="bi bi-plus-circle me-2"></i>Créer une Tâche
                </a>
            </div>
        </div>


        <div class="cp-chart-card mb-4">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-filter me-2"></i>Filtres de recherche</h6>
            </div>
            <div class="p-4">
                <form action="<?php echo e(route('super-admin.taches.index')); ?>" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Titre de la Tâche</label>
                        <input type="text" name="titre" class="form-control form-control-sm"
                            placeholder="Ex: Peinture, Maçonnerie..." value="<?php echo e(request('titre')); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Projet</label>
                        <select name="projet_id" class="form-select form-select-sm">
                            <option value="">Tous les projets</option>
                            <?php $__currentLoopData = $projets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $projet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($projet->id); ?>" <?php echo e(request('projet_id')==$projet->id ? 'selected' : ''); ?>><?php echo e($projet->nom); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Statut</label>
                        <select name="statut" class="form-select form-select-sm">
                            <option value="">Tous les statuts</option>
                            <option value="a_faire" <?php echo e(request('statut')=='a_faire' ? 'selected' : ''); ?>>À faire
                            </option>
                            <option value="en_cours" <?php echo e(request('statut')=='en_cours' ? 'selected' : ''); ?>>En cours
                            </option>
                            <option value="terminee" <?php echo e(request('statut')=='terminee' ? 'selected' : ''); ?>>Terminée
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Phase</label>
                        <select name="phase_id" class="form-select form-select-sm">
                            <option value="">Toutes les phases</option>
                            <?php $__currentLoopData = $projets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $__currentLoopData = $p->phases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($phase->id); ?>" <?php echo e(request('phase_id')==$phase->id ? 'selected' : ''); ?>>
                                <?php echo e($p->nom); ?> - <?php echo e($phase->nom); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="bi bi-search me-1"></i> Filtrer
                        </button>
                        <a href="<?php echo e(route('super-admin.taches.index')); ?>" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>


        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-list-ul me-2"></i>Liste des Tâches</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tachesTable">
                    <thead>
                        <tr style="background: rgba(99,102,241,.08);">
                            <th>Tâche</th>
                            <th>Projet / Phase</th>
                            <th>Personne assignée</th>
                            <th>Statut</th>
                            <th>Date de fin</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $taches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tache): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><strong><?php echo e($tache->titre); ?></strong></td>
                            <td>
                                <div class="d-flex flex-column align-items-start">
                                    <span class="badge bg-light text-dark mb-1"><?php echo e($tache->projet ? $tache->projet->nom
                                        : 'N/A'); ?></span>
                                    <?php if($tache->phase): ?>
                                    <span class="small text-muted"><i class="bi bi-layers me-1"></i><?php echo e($tache->phase->nom); ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <?php if($tache->user): ?>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold"
                                            style="width:24px;height:24px;font-size:0.7rem;flex-shrink:0;">
                                            <?php echo e(strtoupper(substr($tache->user->prenom ?? $tache->user->name, 0, 1))); ?><?php echo e(strtoupper(substr($tache->user->name, 0, 1))); ?>

                                        </div>
                                        <div>
                                            <div class="small lh-1"><?php echo e($tache->user->name); ?> <?php echo e($tache->user->prenom ?? ''); ?></div>
                                            <?php if($tache->user->role): ?><div class="text-muted" style="font-size:0.6rem;"><?php echo e($tache->user->role->nom); ?></div><?php endif; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#voirModal<?php echo e($tache->id); ?>">
                                        <i class="bi bi-eye me-1"></i>Voir
                                    </button>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $statusBadge = [
                                'a_faire' => 'bg-secondary',
                                'en_cours' => 'bg-primary',
                                'terminee' => 'bg-success'
                                ][$tache->statut] ?? 'bg-secondary';
                                $statusText = [
                                'a_faire' => 'À faire',
                                'en_cours' => 'En cours',
                                'terminee' => 'Terminée'
                                ][$tache->statut] ?? ucfirst($tache->statut);
                                ?>
                                <span class="badge <?php echo e($statusBadge); ?>"><?php echo e($statusText); ?></span>
                            </td>
                            <td><?php echo e($tache->date_fin_prevue ?
                                \Carbon\Carbon::parse($tache->date_fin_prevue)->format('d/m/Y') : 'N/A'); ?></td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="<?php echo e(route('super-admin.taches.show', $tache->id)); ?>"
                                        class="btn btn-sm btn-outline-secondary" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php echo $__env->make('partials.row-export', ['id' => $tache->id, 'prefix' => 'tache', 'title' => 'Tâche - ' . ($tache->titre ?? $tache->id)], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                    <a href="<?php echo e(route('super-admin.taches.edit', $tache->id)); ?>"
                                        class="btn btn-sm btn-outline-primary" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="<?php echo e(route('super-admin.taches.destroy', $tache->id)); ?>" method="POST"
                                        class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Supprimer cette tâche ?')" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-list-task display-4"></i>
                                <p class="mt-3">Aucune tâche trouvée</p>
                <button class="btn btn-outline-danger" onclick="exportToPdf('id="tachesTable"', 'Liste des taches', 'taches_export')">                    <i class="bi bi-file-earmark-pdf me-2"></i> Exporter                </button>
                                <a href="<?php echo e(route('super-admin.taches.create')); ?>">Créer une tâche</a>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<?php $__currentLoopData = $taches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tache): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if(!$tache->user): ?>
    <div class="modal fade" id="voirModal<?php echo e($tache->id); ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-success"><i class="bi bi-eye me-2 text-success"></i>Personnes assignées - <?php echo e($tache->titre); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php
                        $personnes = $tache->sousTaches->filter(fn($st) => $st->user);
                        $totalSousTaches = $tache->sousTaches->count();
                        $totalPersonnes = $personnes->pluck('user')->unique('id')->count();
                    ?>
                    <div class="d-flex justify-content-center gap-4 mb-3">
                        <div class="bg-light rounded p-2 text-center border border-success" style="min-width:120px;">
                            <strong class="d-block fs-4"><?php echo e($totalSousTaches); ?></strong>
                            <small class="text-muted">Sous-tâche(s)</small>
                        </div>
                        <div class="bg-light rounded p-2 text-center border border-success" style="min-width:120px;">
                            <strong class="d-block fs-4"><?php echo e($totalPersonnes); ?></strong>
                            <small class="text-muted">Personne(s) assignée(s)</small>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Sous-tâche</th>
                                    <th>Personne assignée</th>
                                    <th>Rôle</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $tache->sousTaches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($loop->iteration); ?></td>
                                        <td><?php echo e($st->titre); ?></td>
                                        <td><?php echo e($st->user->name ?? $st->user->prenom ?? '—'); ?></td>
                                        <td>
                                            <?php if($st->user && $st->user->role): ?>
                                                <span class="badge bg-light text-dark border"><?php echo e($st->user->role->nom); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <i class="bi bi-info-circle d-block mb-2" style="font-size:1.5rem;"></i>
                                            Aucune sous-tâche pour cette tâche.
                                            <a href="<?php echo e(route('super-admin.sous-taches.create')); ?>" class="d-block mt-2">Créer une sous-tâche</a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php $__env->stopSection(); ?>

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

<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/super-admin/taches/index.blade.php ENDPATH**/ ?>