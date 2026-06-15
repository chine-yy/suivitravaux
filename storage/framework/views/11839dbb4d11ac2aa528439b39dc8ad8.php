<?php
    use Illuminate\Support\Facades\DB;
?>

<?php $__env->startSection('title', 'Gestion de la Base de Donnees'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <a href="<?php echo e(route('super-admin.configuration.index')); ?>" class="text-decoration-none">Configuration</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Base de donnees</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-database me-2"></i>Gestion de la Base de Donnees</h1>
                <p class="cp-page-subtitle">Les actions visibles dependent des permissions choisies.</p>
            </div>
        </div>

        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Attention :</strong> Les actions de vidage et de suppression sont irreversibles.
        </div>

        <?php if($canClearDatabase): ?>
        <div class="cp-chart-card mb-4">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-trash me-2"></i>Vider toutes les tables</h6>
            </div>
            <div class="p-4">
                <p>Cette action vide le contenu de toutes les tables sauf les tables systeme.</p>
                <button type="button" class="btn btn-danger" onclick="openClearAllModal()">
                    <i class="bi bi-trash me-2"></i>Vider toutes les tables
                </button>
            </div>
        </div>
        <?php endif; ?>

        <?php if($canExportDatabase): ?>
        <div class="cp-chart-card mb-4">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-cloud-arrow-down me-2"></i>Sauvegarder la base de donnees</h6>
            </div>
            <div class="p-4">
                <p>Telecharger une sauvegarde SQL complete de la base de donnees.</p>
                <a href="<?php echo e(route('super-admin.database.backup')); ?>" class="btn btn-primary">
                    <i class="bi bi-cloud-arrow-down me-2"></i>Sauvegarder maintenant
                </a>
            </div>
        </div>
        <?php endif; ?>

        <?php if($canViewDatabase || $canClearDatabase): ?>
        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-table me-2"></i>Gestion table par table</h6>
            </div>
            <div class="p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Nom de la table</th>
                                <th>Enregistrements</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $tableData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $table): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><code><?php echo e($table['name']); ?></code></td>
                                <td>
                                    <span class="badge <?php echo e($table['count'] > 0 ? 'bg-primary' : 'bg-secondary'); ?>">
                                        <?php echo e($table['count']); ?>

                                    </span>
                                </td>
                                <td class="text-end">
                                    <?php if($canViewDatabase): ?>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo e($table['name']); ?>">
                                        <i class="bi bi-eye"></i> Voir
                                    </button>
                                    <?php endif; ?>

                                    <?php if($canExportDatabase): ?>
                                    <a href="<?php echo e(route('super-admin.database.export-table', ['table' => $table['name']])); ?>" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-download"></i> Exporter
                                    </a>
                                    <?php endif; ?>

                                    <?php if($canClearDatabase): ?>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#clearModal<?php echo e($table['name']); ?>" <?php echo e($table['count'] == 0 ? 'disabled' : ''); ?>>
                                        <i class="bi bi-trash"></i> Vider
                                    </button>

                                    <div class="modal fade" id="clearModal<?php echo e($table['name']); ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true" style="background: rgba(0,0,0,0.5);">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content" style="border-radius: 16px;">
                                                <div class="modal-header border-0 pb-0">
                                                    <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Vider la table</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center pt-4 pb-4">
                                                    <p class="mb-3">Êtes-vous sûr de vouloir vider le contenu de la table <code><?php echo e($table['name']); ?></code> ?</p>
                                                    <div class="alert alert-danger mb-0" style="background: #fee2e2; border-color: #fca5a5;">
                                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                                        Cette action supprimera définitivement toutes les données de cette table.<br>
                                                        Cette action est irréversible.
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 pt-0 justify-content-center gap-3">
                                                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Annuler</button>
                                                    <form action="<?php echo e(route('super-admin.database.clear-table')); ?>" method="POST" class="d-inline">
                                                        <?php echo csrf_field(); ?>
                                                        <input type="hidden" name="table_name" value="<?php echo e($table['name']); ?>">
                                                        <input type="hidden" name="confirmation" value="CONFIRMER">
                                                        <button type="submit" class="btn btn-danger px-4">Confirmer</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <?php if($canViewDatabase): ?>
                                    <div class="modal fade" id="viewModal<?php echo e($table['name']); ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-xl">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Table: <?php echo e($table['name']); ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                                                    <?php
                                                    $columns = DB::getSchemaBuilder()->getColumnListing($table['name']);
                                                    $data = DB::table($table['name'])->limit(50)->get();
                                                    ?>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered" style="max-height: 50vh;">
                                                            <thead>
                                                                <tr>
                                                                    <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <th><?php echo e($column); ?></th>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                                <tr>
                                                                    <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <td><?php echo e($row->$column); ?></td>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                </tr>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                                <tr>
                                                                    <td colspan="<?php echo e(count($columns)); ?>" class="text-center text-muted">Aucun enregistrement</td>
                                                                </tr>
                                                                <?php endif; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <?php if($canExportDatabase): ?>
                                                    <a href="<?php echo e(route('super-admin.database.export-table', ['table' => $table['name']])); ?>" class="btn btn-success">
                                                        <i class="bi bi-download me-1"></i> Exporter
                                                    </a>
                                                    <?php endif; ?>
                                                    <?php if($canClearDatabase && $table['count'] > 0): ?>
                                                    <button type="button" class="btn btn-danger"
                                                        onclick="var vm=bootstrap.Modal.getInstance(document.getElementById('viewModal<?php echo e($table['name']); ?>')); vm.hide(); document.getElementById('viewModal<?php echo e($table['name']); ?>').addEventListener('hidden.bs.modal', function handler(){ this.removeEventListener('hidden.bs.modal', handler); new bootstrap.Modal(document.getElementById('clearModal<?php echo e($table['name']); ?>')).show(); });">
                                                        <i class="bi bi-trash me-1"></i> Vider
                                                    </button>
                                                    <?php endif; ?>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('modals'); ?>
<style>
    .clearAll-overlay {
        position: fixed; inset: 0; width: 100vw; height: 100vh;
        background: rgba(0,0,0,0.65); z-index: 9999;
    }
    .clearAll-container {
        position: fixed; inset: 0; z-index: 10000;
        display: flex; align-items: center; justify-content: center;
    }
    .clearAll-container.show {
        animation: clearAllFadeIn 0.3s ease-out;
    }
    @keyframes clearAllFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .clearAll-container.show .clearAll-box {
        animation: clearAllScaleIn 0.3s ease-out;
    }
    @keyframes clearAllScaleIn {
        from { opacity: 0; transform: scale(0.92); }
        to { opacity: 1; transform: scale(1); }
    }
    .btn-cancel-clear {
        width: 180px; height: 55px; border-radius: 12px;
        background: #f3f4f6; color: #111827;
        font-weight: 600; font-size: 16px; border: none;
        transition: background 0.2s;
    }
    .btn-cancel-clear:hover {
        background: #e5e7eb;
    }
    .btn-confirm-clear {
        width: 220px; height: 55px; border-radius: 12px;
        background: #16a34a; color: white;
        font-weight: 600; font-size: 16px; border: none;
        display: inline-flex; align-items: center; justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 6px -1px rgba(22, 163, 74, 0.3);
        transition: background 0.2s;
    }
    .btn-confirm-clear:hover {
        background: #15803d;
    }
</style>
<div id="clearAllOverlay" class="clearAll-overlay" style="display: none;" onclick="closeClearAllModal()"></div>
<div id="clearAllContainer" class="clearAll-container" style="display: none;">
    <div class="clearAll-box" style="max-width: 520px; width: calc(100% - 32px); background: #fff; border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
        <div class="d-flex align-items-center px-4 pt-4 pb-3">
            <div class="d-flex align-items-center gap-3 flex-grow-1">
                <div style="width: 44px; height: 44px; background: #dcfce7; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="bi bi-trash" style="color: #16a34a; font-size: 20px;"></i>
                </div>
                <h5 style="color: #16a34a; font-size: 20px; font-weight: 600; margin: 0;">Vider Toutes les Tables</h5>
            </div>
            <button type="button" class="btn-close" onclick="closeClearAllModal()" aria-label="Close" style="font-size: 1.5rem; opacity: 0.7;"></button>
        </div>
        <hr style="margin: 0 16px; color: #e5e7eb;">

        <div class="px-4 py-4 text-center">
            <p style="color: #111827; font-size: 17px; font-weight: 600; margin-bottom: 24px;">
                Êtes-vous sûr de vouloir vider le contenu de toutes les tables ?
            </p>
            <div style="background: #fef2f2; border: 1px solid #fca5a5; border-radius: 12px; padding: 16px; display: flex; align-items: flex-start; gap: 12px;">
                <div style="flex-shrink: 0; margin-top: 2px;">
                    <i class="bi bi-exclamation-triangle-fill" style="color: #dc2626; font-size: 24px;"></i>
                </div>
                <div style="color: #991b1b; font-size: 15px; font-weight: 700; text-align: left; line-height: 1.5;">
                    Cette action supprimera définitivement toutes les données.<br>
                    Cette action est irréversible.
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center align-items-center gap-3 px-4 pb-4 pt-2" style="border: none;">
            <button type="button" class="btn-cancel-clear" onclick="closeClearAllModal()">Annuler</button>
            <form action="<?php echo e(route('super-admin.database.clear-all')); ?>" method="POST" style="margin: 0;">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="confirmation" value="SUPPRIMER_TOUT">
                <button type="submit" class="btn-confirm-clear"><i class="bi bi-trash"></i> Confirmer</button>
            </form>
        </div>
    </div>
</div>
<script>
function openClearAllModal() {
    document.getElementById('clearAllOverlay').style.display = 'block';
    var c = document.getElementById('clearAllContainer');
    c.style.display = 'flex';
    c.classList.add('show');
    document.body.style.overflow = 'hidden';
}
function closeClearAllModal() {
    document.getElementById('clearAllOverlay').style.display = 'none';
    var c = document.getElementById('clearAllContainer');
    c.style.display = 'none';
    c.classList.remove('show');
    document.body.style.overflow = '';
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/super-admin/database/index.blade.php ENDPATH**/ ?>