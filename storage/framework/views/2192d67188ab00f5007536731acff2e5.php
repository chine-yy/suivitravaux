<?php $__env->startSection('title', 'Gestion des Projets'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <span class="text-muted">Projets</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="cp-dashboard">
        <div class="cp-content">
            <div class="cp-page-header">
                <div>
                    <h1 class="cp-page-title"><i class="bi bi-briefcase me-2"></i>Gestion des Projets</h1>
                    <p class="cp-page-subtitle">Visualisez et gérez tous les projets de l'entreprise</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-danger"
                        onclick="exportToPdf('projetsTable', 'Liste des projets', 'projets_export')">
                        <i class="bi bi-file-earmark-pdf me-2"></i>Exporter tout
                    </button>
                    <a href="<?php echo e(route('super-admin.projets.create')); ?>" class="btn btn-primary px-4">
                        <i class="bi bi-plus-circle me-2"></i>Nouveau Projet
                    </a>
                </div>
            </div>

            <?php echo $__env->make('partials.alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <?php
                $total = $projets->count();
                $enCours = $projets->where('statut', 'en_cours')->count();
                $termines = $projets->where('statut', 'termine')->count();
                $enRetard = $projets->where('statut', 'en_retard')->count();
            ?>

            <div class="cp-stats-grid mb-4">
                <div class="cp-stat-card">
                    <div class="cp-stat-icon cp-bg-primary"><i class="bi bi-folder2-open"></i></div>
                    <div class="cp-stat-content">
                        <div class="cp-stat-value"><?php echo e($total); ?></div>
                        <div class="cp-stat-label">Total Projets</div>
                    </div>
                </div>
                <div class="cp-stat-card">
                    <div class="cp-stat-icon cp-bg-green"><i class="bi bi-hourglass-split"></i></div>
                    <div class="cp-stat-content">
                        <div class="cp-stat-value"><?php echo e($enCours); ?></div>
                        <div class="cp-stat-label">En Cours</div>
                    </div>
                </div>
                <div class="cp-stat-card">
                    <div class="cp-stat-icon cp-bg-success"><i class="bi bi-check-circle"></i></div>
                    <div class="cp-stat-content">
                        <div class="cp-stat-value"><?php echo e($termines); ?></div>
                        <div class="cp-stat-label">Terminés</div>
                    </div>
                </div>
                <div class="cp-stat-card cp-stat-danger">
                    <div class="cp-stat-icon cp-bg-danger"><i class="bi bi-exclamation-octagon"></i></div>
                    <div class="cp-stat-content">
                        <div class="cp-stat-value"><?php echo e($enRetard); ?></div>
                        <div class="cp-stat-label">En Retard</div>
                    </div>
                </div>
            </div>

            <div class="cp-chart-card mb-4">
                <div class="cp-chart-header">
                    <h6 class="cp-chart-title"><i class="bi bi-filter me-2"></i>Filtres de recherche</h6>
                </div>
                <div class="p-4">
                    <form action="<?php echo e(route('super-admin.projets.index')); ?>" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Nom du Projet</label>
                            <input type="text" name="nom" class="form-control form-control-sm"
                                placeholder="Ex: Construction villa..." value="<?php echo e(request('nom')); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Statut</label>
                            <select name="statut" class="form-select form-select-sm">
                                <option value="">Tous les statuts</option>
                                <option value="en_attente" <?php echo e(request('statut') == 'en_attente' ? 'selected' : ''); ?>>En
                                    Attente</option>
                                <option value="en_cours" <?php echo e(request('statut') == 'en_cours' ? 'selected' : ''); ?>>En Cours
                                </option>
                                <option value="termine" <?php echo e(request('statut') == 'termine' ? 'selected' : ''); ?>>Terminé
                                </option>
                                <option value="en_retard" <?php echo e(request('statut') == 'en_retard' ? 'selected' : ''); ?>>En Retard
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Date de début</label>
                            <input type="date" name="date" class="form-control form-control-sm"
                                value="<?php echo e(request('date')); ?>">
                        </div>
                        <div class="col-md-2 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-sm btn-primary w-100">
                                <i class="bi bi-search me-1"></i> Filtrer
                            </button>
                            <a href="<?php echo e(route('super-admin.projets.index')); ?>" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="cp-chart-card">
                <div class="cp-chart-header">
                    <h6 class="cp-chart-title"><i class="bi bi-list-ul me-2"></i>Liste des Projets</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="projetsTable">
                        <thead>
                            <tr style="background: rgba(99,102,241,.08);">
                                <th>Nom du Projet</th>
                                <th>Statut</th>
                                <th>Avancement</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $projets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $projet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <span class="fw-bold"><?php echo e($projet->nom); ?></span>
                                        <?php if($projet->date_fin_prevue): ?>
                                                                <div class="small text-muted mt-1"><i class="bi bi-calendar-event me-1"></i>Échéance: <?php echo e($projet->date_fin_prevue->format('d/m/Y')); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                            $statusClass = [
                                                'en_attente' => 'bg-secondary',
                                                'en_cours' => 'bg-primary',
                                                'termine' => 'bg-success',
                                                'en_retard' => 'bg-danger'
                                            ][$projet->statut] ?? 'bg-secondary';

                                            $statusIcon = [
                                                'en_attente' => 'bi-clock',
                                                'en_cours' => 'bi-play-circle',
                                                'termine' => 'bi-check-circle',
                                                'en_retard' => 'bi-exclamation-triangle'
                                            ][$projet->statut] ?? 'bi-circle';
                                        ?>
                                        <span class="badge rounded-pill px-3 <?php echo e($statusClass); ?>">
                                            <i class="bi <?php echo e($statusIcon); ?> me-1"></i>
                                            <?php echo e(ucfirst(str_replace('_', ' ', $projet->statut))); ?>

                                        </span>
                                    </td>
                                    <td style="min-width: 150px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 8px; border-radius: 4px;">
                                                <div class="progress-bar bg-primary" role="progressbar"
                                                    style="width: <?php echo e($projet->avancement ?? 0); ?>%;"></div>
                                            </div>
                                            <span class="fw-bold small" style="min-width:35px;"><?php echo e($projet->avancement ?? 0); ?>%</span>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <a href="<?php echo e(route('super-admin.projets.show', $projet->id)); ?>"
                                                class="btn btn-sm btn-outline-info" title="Voir">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?php echo e(route('super-admin.projets.edit', $projet->id)); ?>"
                                                class="btn btn-sm btn-outline-primary" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <?php echo $__env->make('partials.row-export', ['id' => $projet->id, 'prefix' => 'projet', 'title' => 'Projet', 'project_name_only' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                            <form action="<?php echo e(route('super-admin.projets.destroy', $projet->id)); ?>" method="POST"
                                                class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Supprimer ce projet ?')" title="Supprimer">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox display-4"></i>
                                        <p class="mt-3">Aucun projet trouvé</p>
                                        <button class="btn btn-outline-danger" onclick="exportToPdf('id="
                                            projetsTable"', 'Liste des projets' , 'projets_export' )"> <i
                                                class="bi bi-file-earmark-pdf me-2"></i> Exporter </button>
                                        <a href="<?php echo e(route('super-admin.projets.create')); ?>">Créer un projet</a>
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
        link.download = (filename || 'export') + '_' + new Date().toISOString().slice(0, 10) + '.csv';
        link.click();
    }
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux/resources/views/super-admin/projets/index.blade.php ENDPATH**/ ?>