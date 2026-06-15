<?php $__env->startSection('title', 'Phases de Projet'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <span class="text-muted">Phases</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="cp-phases">
        <div class="cp-content">
            <!-- Page Header -->
            <div class="cp-page-header">
                <div>
                    <h1 class="cp-page-title"><i class="bi bi-collection me-2"></i>Phases de Projet</h1>
                    <p class="cp-page-subtitle">Liste de toutes les phases</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-danger"
                        onclick="exportToPdf('phasesTable', 'Liste des phases', 'phases_export')">
                        <i class="bi bi-file-earmark-pdf me-2"></i>Exporter tout
                    </button>
                    <a href="<?php echo e(route('super-admin.phases.create')); ?>" class="btn btn-primary px-4">
                        <i class="bi bi-plus-circle me-2"></i>Créer une Phase
                    </a>
                </div>
            </div>


            <div class="cp-chart-card mb-4">
                <div class="cp-chart-header">
                    <h6 class="cp-chart-title"><i class="bi bi-filter me-2"></i>Rechercher une Phase</h6>
                </div>
                <div class="p-4">
                    <form action="<?php echo e(route('super-admin.phases.index')); ?>" method="GET" class="row g-3">
                        <div class="col-md-5">
                            <input type="text" name="nom" class="form-control" placeholder="Rechercher par nom de phase..."
                                value="<?php echo e(request('nom')); ?>">
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search me-1"></i> Filtrer
                            </button>
                            <a href="<?php echo e(route('super-admin.phases.index')); ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>


            <!-- Phases Table -->
            <div class="cp-card">
                <div class="cp-card-header">
                    <h5 class="cp-card-title">
                        <i class="bi bi-list-ul me-2"></i>Liste des Phases
                    </h5>
                </div>
                <div class="cp-card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="phasesTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Projet</th>
                                    <th>Nom de la Phase</th>
                                    <th>Date de début</th>
                                    <th>Date fin prévue</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $phases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><strong><?php echo e($phase->projet->nom ?? '-'); ?></strong></td>
                                        <td><?php echo e($phase->nom); ?></td>
                                        <td><?php echo e($phase->date_debut ? \Carbon\Carbon::parse($phase->date_debut)->format('d/m/Y') : '-'); ?>

                                        </td>
                                        <td><?php echo e($phase->date_fin_prevue ? \Carbon\Carbon::parse($phase->date_fin_prevue)->format('d/m/Y') : '-'); ?>

                                        </td>
                                        <td><?php echo e(Str::limit($phase->description, 50) ?? '-'); ?></td>
                                        <td class="text-end">
                                            <div class="d-flex gap-1 justify-content-end">
                                                <a href="<?php echo e(route('super-admin.phases.show', $phase->id)); ?>"
                                                    class="btn btn-sm btn-outline-secondary" title="Voir">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <?php echo $__env->make('partials.row-export', ['id' => $phase->id, 'prefix' => 'phase', 'title' => 'Phase - ' . ($phase->nom ?? $phase->id)], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                                <a href="<?php echo e(route('super-admin.phases.edit', $phase->id)); ?>"
                                                    class="btn btn-sm btn-outline-primary" title="Modifier">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="<?php echo e(route('super-admin.phases.destroy', $phase->id)); ?>"
                                                    method="POST" class="d-inline">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Supprimer cette phase ?')" title="Supprimer">
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
                                            <p class="mt-3">Aucune phase trouvée</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php if(isset($phases) && $phases->hasPages()): ?>
                    <div class="cp-card-footer">
                        <?php echo e($phases->links()); ?>

                    </div>
                <?php endif; ?>
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
<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/super-admin/phases/index.blade.php ENDPATH**/ ?>