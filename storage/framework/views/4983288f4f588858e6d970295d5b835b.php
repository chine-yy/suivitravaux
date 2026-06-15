<?php $__env->startSection('title', 'Logs Système - Super Admin'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <a href="<?php echo e(route('role-dynamique.configuration.index')); ?>" class="text-muted">Configuration</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="text-muted">Logs Système</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-xxl">
<div class="cp-logs-viewer">
    <div class="cp-content">
        <div class="cp-page-header">
            <div class="d-flex align-items-center">
                <i class="bi bi-file-earmark-text fs-2 me-3 text-muted"></i>
                <div>
                    <h1 class="cp-page-title mb-0">Logs</h1>
                    <p class="cp-page-subtitle mb-0">Contenu du fichier laravel.log (dernières 500 lignes)</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <?php if($canClear): ?>
                <form action="<?php echo e(route('role-dynamique.configuration.logs.clear')); ?>" method="POST" onsubmit="return confirm('Voulez-vous vraiment vider le fichier de logs ?')">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-green rounded-pill px-3">
                        <i class="bi bi-x-circle me-1"></i> Vider
                    </button>
                </form>
                <?php endif; ?>
                <?php if($canExport): ?>
                <a href="<?php echo e(route('role-dynamique.configuration.logs.export-pdf')); ?>" class="btn btn-outline-danger rounded-pill px-3">
                    <i class="bi bi-file-earmark-pdf me-1"></i> Exporter PDF
                </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="cp-card">
            <div class="cp-card-header d-flex justify-content-between align-items-center">
                <h5 class="cp-card-title mb-0">
                    <i class="bi bi-file-earmark-text me-2"></i>storage/logs/laravel.log
                </h5>
                <span class="badge bg-dark">Fichier texte</span>
            </div>
            <div class="cp-card-body p-0">
                <pre class="bg-dark text-light p-4 mb-0" style="max-height: 600px; overflow-y: auto; font-family: 'Courier New', Courier, monospace; font-size: 0.85rem;"><?php echo e($logs ?: 'Aucun log disponible pour le moment.'); ?></pre>
            </div>
        </div>
    </div>
</div>
</div>

<style>
    pre::-webkit-scrollbar {
        width: 8px;
    }
    pre::-webkit-scrollbar-track {
        background: #1e293b;
    }
    pre::-webkit-scrollbar-thumb {
        background: #475569;
        border-radius: 4px;
    }
    pre::-webkit-scrollbar-thumb:hover {
        background: #64748b;
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.role-dynamique', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/role-dynamique/configuration/logs.blade.php ENDPATH**/ ?>