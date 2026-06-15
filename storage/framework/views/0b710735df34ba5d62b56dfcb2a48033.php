<?php $__env->startSection('title', 'Historique — Super Admin'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <span class="cp-breadcrumb-item active">Historique</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-dashboard">
    <div class="cp-content">
        <!-- Header -->
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title">
                    <i class="bi bi-clock-history me-2" style="color: var(--cp-orange);"></i>Historique Global
                </h1>
                <p class="cp-page-subtitle">Consultez toutes les données archivées par année</p>
            </div>
        </div>


        <?php if($annees->isEmpty()): ?>
            <div class="cp-chart-card p-5 text-center mt-4">
                <i class="bi bi-archive" style="font-size: 3rem; color: #9ca3af;"></i>
                <h4 class="mt-3 fw-bold text-muted">Aucune donnée disponible</h4>
                <p class="text-muted">Commencez par créer des projets ou des budgets pour les voir apparaître ici.</p>
                <a href="<?php echo e(route('super-admin.projets.index')); ?>" class="btn btn-primary mt-2">
                    <i class="bi bi-plus-circle me-2"></i>Créer un projet
                </a>
            </div>
        <?php else: ?>
            <!-- Timeline des années -->
            <div class="row mt-4 g-4">
                <?php $__currentLoopData = $annees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $annee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $s = $statsParAnnee[$annee] ?? []; ?>
                    <div class="col-md-4 col-lg-3">
                        <a href="<?php echo e(route('super-admin.historique.show', $annee)); ?>" class="text-decoration-none">
                            <div class="hist-year-card <?php echo e(($s['is_current'] ?? false) ? 'hist-year-current' : ''); ?>">
                                <!-- Année -->
                                <div class="hist-year-badge">
                                    <?php if($s['is_current'] ?? false): ?>
                                        <span class="badge bg-green mb-2" style="font-size:0.7rem;">Année en cours</span><br>
                                    <?php endif; ?>
                                    <span class="hist-year-number"><?php echo e($annee); ?></span>
                                </div>
                                <!-- Stats miniatures -->
                                <div class="hist-year-stats">
                                    <div class="hist-year-stat">
                                         <i class="bi bi-briefcase text-green"></i>
                                        <div>
                                            <strong><?php echo e($s['total_projets'] ?? 0); ?></strong>
                                            <small>Projets</small>
                                        </div>
                                    </div>
                                    <div class="hist-year-stat">
                                         <i class="bi bi-check-circle text-green"></i>
                                        <div>
                                            <strong><?php echo e($s['termines'] ?? 0); ?></strong>
                                            <small>Terminés</small>
                                        </div>
                                    </div>
                                    <div class="hist-year-stat">
                                        <i class="bi bi-list-task text-warning"></i>
                                        <div>
                                            <strong><?php echo e($s['total_taches'] ?? 0); ?></strong>
                                            <small>Tâches</small>
                                        </div>
                                    </div>
                                    <div class="hist-year-stat">
                                         <i class="bi bi-people text-green"></i>
                                        <div>
                                            <strong><?php echo e($s['total_users'] ?? 0); ?></strong>
                                            <small>Utilisateurs</small>
                                        </div>
                                    </div>
                                </div>
                                <!-- Budget -->
                                <?php if(($s['budget_total'] ?? 0) > 0): ?>
                                <div class="hist-year-budget">
                                    <i class="bi bi-cash-stack me-1"></i>
                                    <span><?php echo e(number_format($s['budget_total'], 0, ',', ' ')); ?> FCFA</span>
                                </div>
                                <?php endif; ?>
                                <div class="hist-year-footer">
                                    <span>Voir l'historique complet</span>
                                    <i class="bi bi-arrow-right"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .hist-year-card {
        background: #fff;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        border: 2px solid #f1f5f9;
        transition: all 0.25s ease;
        display: block;
        height: 100%;
    }
    .hist-year-card:hover {
        box-shadow: 0 8px 30px rgba(249,115,22,0.2);
        border-color: #009A44;
        transform: translateY(-4px);
    }
    .hist-year-current {
        border-color: #009A44;
        background: linear-gradient(135deg, #e8f5e9, #fff);
    }
    .hist-year-current:hover {
        box-shadow: 0 8px 30px rgba(249,115,22,0.2);
        border-color: #009A44;
    }
    .hist-year-badge {
        text-align: center;
        margin-bottom: 1rem;
    }
    .hist-year-number {
        font-size: 3rem;
        font-weight: 800;
        background: linear-gradient(135deg, #009A44, #ef4444);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1;
    }
    .hist-year-current .hist-year-number {
        background: linear-gradient(135deg, #009A44, #007a35);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .hist-year-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }
    .hist-year-stat {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: #f8fafc;
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
    }
    .hist-year-stat i { font-size: 1.1rem; }
    .hist-year-stat div { display: flex; flex-direction: column; }
    .hist-year-stat strong { font-size: 1rem; font-weight: 700; line-height: 1.2; }
    .hist-year-stat small { font-size: 0.7rem; color: #6b7280; }
    .hist-year-budget {
        background: linear-gradient(135deg, #fffbeb, #fef3c7);
        border: 1px solid #fcd34d;
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
        font-size: 0.82rem;
        font-weight: 600;
        color: #92400e;
        margin-bottom: 1rem;
        text-align: center;
    }
    .hist-year-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: #009A44;
        font-weight: 600;
        font-size: 0.85rem;
        border-top: 1px solid #f1f5f9;
        padding-top: 0.75rem;
    }
    .hist-year-current .hist-year-footer { color: #009A44; }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/super-admin/historique/index.blade.php ENDPATH**/ ?>