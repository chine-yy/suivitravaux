<?php $__env->startSection('title', 'Configuration Système - Super Admin'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <span class="text-muted">Configuration</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-configuration">
    <div class="cp-content">
        <!-- Page Header -->
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title">Configuration Système</h1>
                <p class="cp-page-subtitle">Gérer les paramètres globaux de l'application</p>
            </div>
        </div>


        <!-- Quick Stats / System Status -->
        <div class="cp-stats-grid mb-4">
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-info"><i class="bi bi-clock-history"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e(now()->format('H:i')); ?></div>
                    <div class="cp-stat-label">Heure du serveur</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-success"><i class="bi bi-check-circle"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e($configurations['app_env'] === 'production' ? 'Production' : 'Développement'); ?></div>
                    <div class="cp-stat-label">Environnement</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon <?php echo e($configurations['app_debug'] ? 'cp-bg-green' : 'cp-bg-success'); ?>">
                    <i class="bi <?php echo e($configurations['app_debug'] ? 'bi-bug' : 'bi-shield-check'); ?>"></i>
                </div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e($configurations['app_debug'] ? 'Activé' : 'Désactivé'); ?></div>
                    <div class="cp-stat-label">Mode Debug</div>
                </div>
            </div>
        </div>

        <!-- Configuration Sections -->
        <div class="row">
            <!-- System Information -->
            <div class="col-lg-6 mb-4">
                <div class="cp-card">
                    <div class="cp-card-header">
                        <h5 class="cp-card-title">
                            <i class="bi bi-info-circle me-2"></i>Informations Système
                        </h5>
                    </div>
                    <div class="cp-card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless table-config">
                                <tbody>
                                    <tr>
                                        <td class="text-muted"><i class="bi bi-database me-2"></i>Base de données</td>
                                        <td><span class="badge bg-info"><?php echo e($configurations['db_driver']); ?></span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><i class="bi bi-hdd me-2"></i>Hôte DB</td>
                                        <td><code><?php echo e($configurations['db_host']); ?></code></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><i class="bi bi-envelope me-2"></i>Mail Driver</td>
                                        <td><span class="badge bg-secondary"><?php echo e($configurations['mail_driver']); ?></span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><i class="bi bi-collection me-2"></i>Session</td>
                                        <td><span class="badge bg-primary"><?php echo e($configurations['session_driver']); ?></span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><i class="bi bi-lightning me-2"></i>Cache</td>
                                        <td><span class="badge bg-success"><?php echo e($configurations['cache_driver']); ?></span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><i class="bi bi-folder me-2"></i>Storage</td>
                                        <td><span class="badge bg-primary"><?php echo e($configurations['filesystems_default']); ?></span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><i class="bi bi-list-task me-2"></i>Queue</td>
                                        <td><span class="badge bg-info"><?php echo e($configurations['queue_default']); ?></span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><i class="bi bi-file-earmark-text me-2"></i>Log Channel</td>
                                        <td><span class="badge bg-secondary"><?php echo e($configurations['log_channel']); ?></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Maintenance Mode -->
            <div class="col-lg-6 mb-4">
                <div class="cp-card mb-4">
                    <div class="cp-card-header">
                        <h5 class="cp-card-title">
                            <i class="bi bi-tools me-2"></i>Maintenance
                        </h5>
                    </div>
                    <div class="cp-card-body">
                        <div class="alert alert-danger mb-3" style="background-color: #e8f5e9; border-color: #81c784; color: #007a35;">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Le mode maintenance rend l'application inaccessible aux utilisateurs normaux.
                        </div>

                        <div class="d-grid gap-2">
                            <?php if(app()->isDownForMaintenance()): ?>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#deactivateMaintenanceModal">
                                    <i class="bi bi-play-circle me-2"></i>Désactiver le mode maintenance
                                </button>
                            <?php else: ?>
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#maintenanceModal">
                                    <i class="bi bi-pause-circle me-2"></i>Activer le mode maintenance
                                </button>
                            <?php endif; ?>

                            <?php if($canAnyPermission([
                                'view-base-de-donnees',
                                'clear-base-de-donnees',
                                'sauvegarde-base-de-donnees',
                            ])): ?>
                                <a href="<?php echo e(route('super-admin.database.index')); ?>" class="btn btn-outline-secondary">
                                    <i class="bi bi-database me-2"></i>Gerer la base de donnees
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Database Management -->
                <div class="cp-card mb-4">
                    <div class="cp-card-header">
                        <h5 class="cp-card-title">
                            <i class="bi bi-database me-2"></i>Base de Données
                        </h5>
                    </div>
                    <div class="cp-card-body">
                        <p class="text-muted small mb-3">Gérer les tables, importer, exporter et configurer votre base de données.</p>
                        <div class="d-grid gap-2">
                            <a href="<?php echo e(route('super-admin.database.index')); ?>" class="btn btn-outline-green">
                                <i class="bi bi-database me-2"></i>Gérer la base de données
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Logs System -->
                <?php if($canPermission('view-logs')): ?>
                <div class="cp-card">
                    <div class="cp-card-header">
                        <h5 class="cp-card-title">
                            <i class="bi bi-file-earmark-text me-2"></i>Logs Système
                        </h5>
                    </div>
                    <div class="cp-card-body">
                        <div class="d-grid gap-2">
                            <a href="<?php echo e(route('super-admin.configuration.logs')); ?>" class="btn btn-outline-green">
                                <i class="bi bi-file-earmark-text me-2"></i>Voir les logs systeme
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        </div>

        <!-- System Resources -->
        <div class="cp-card mb-4">
            <div class="cp-card-header">
                <h5 class="cp-card-title">
                    <i class="bi bi-pie-chart me-2"></i>Ressources Système
                </h5>
            </div>
            <div class="cp-card-body">
                <div class="row text-center">
                    <div class="col-md-3 col-6 mb-3">
                        <div class="p-3 bg-light rounded">
                            <i class="bi bi-cpu fs-1 text-primary"></i>
                            <div class="mt-2"><strong>PHP</strong></div>
                            <small class="text-muted"><?php echo e(phpversion()); ?></small>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="p-3 bg-light rounded">
                            <i class="bi bi-gem fs-1 text-danger"></i>
                            <div class="mt-2"><strong>Laravel</strong></div>
                            <small class="text-muted"><?php echo e(app()->version()); ?></small>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="p-3 bg-light rounded">
                            <i class="bi bi-git fs-1 text-primary"></i>
                            <div class="mt-2"><strong>Version</strong></div>
                            <small class="text-muted">1.0.0</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="p-3 bg-light rounded">
                            <i class="bi bi-calendar-check fs-1 text-success"></i>
                            <div class="mt-2"><strong>Dernière mise à jour</strong></div>
                            <small class="text-muted"><?php echo e(now()->format('d/m/Y')); ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Maintenance Modal -->
<div class="modal fade" id="maintenanceModal" tabindex="-1" aria-labelledby="maintenanceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="maintenanceModalLabel">
                    <i class="bi bi-pause-circle text-primary me-2"></i>Mode Maintenance
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir activer le mode maintenance ?</p>
                <div class="alert alert-danger" style="background-color: #e8f5e9; border-color: #81c784; color: #007a35;">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    L'application sera inaccessible pour tous les utilisateurs jusqu'à désactivation.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="<?php echo e(route('super-admin.configuration.maintenance.toggle')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-pause-circle me-2"></i>Activer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="deactivateMaintenanceModal" tabindex="-1" aria-labelledby="deactivateMaintenanceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deactivateMaintenanceModalLabel">
                    <i class="bi bi-play-circle text-primary me-2"></i>Désactiver la Maintenance
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir désactiver le mode maintenance ?</p>
                <div class="alert alert-warning" style="background-color: #e8f5e9; border-color: #81c784; color: #007a35;">
                    <i class="bi bi-check-circle me-2"></i>
                    L'application sera de nouveau accessible à tous les utilisateurs.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="<?php echo e(route('super-admin.configuration.maintenance.toggle')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-play-circle me-2"></i>Désactiver
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/super-admin/configuration/index.blade.php ENDPATH**/ ?>