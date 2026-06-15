<?php $__env->startSection('title', 'Tableau de Bord - Super Admin'); ?>

<?php $__env->startSection('content'); ?>
<style>
.btn-delete {
    background-color: #fd7e14;
    border-color: #fd7e14;
    color: white;
}
.btn-delete:hover {
    background-color: #e96b00;
    border-color: #e96b00;
    color: white;
}
</style>

<div class="cp-dashboard">
    <div class="cp-content">
        <!-- Page Header -->
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title">Tableau de Bord</h1>
                <p class="cp-page-subtitle">Vue d'ensemble globale — <?php echo e(now()->format('d/m/Y')); ?></p>
            </div>
        </div>


        <?php if(session('generated_password')): ?>
        <div class="alert border-0 shadow mb-4 p-4" id="dashboardPasswordAlert"
            style="background: linear-gradient(135deg, #ede9fe, #ddd6fe); border-left: 5px solid #7c3aed !important;">
            <div class="d-flex align-items-start gap-3">
                <div class="fs-1">🔐</div>
                <div class="flex-grow-1">
                    <h5 class="fw-bold mb-1" style="color:#7c3aed;">Mot de Passe Généré</h5>
                    <p class="mb-1">Utilisateur : <strong><?php echo e(session('new_user_name')); ?></strong> — <span
                            class="text-muted"><?php echo e(session('new_user_email')); ?></span></p>
                    <div class="d-flex align-items-center gap-2 mt-2">
                        <code class="fs-4 fw-bold px-3 py-2 rounded"
                            style="background:#fff; border: 2px solid #7c3aed; letter-spacing:3px; color:#7c3aed;">
                            <?php echo e(session('generated_password')); ?>

                        </code>
                        <button class="btn btn-sm btn-outline-primary"
                            onclick="navigator.clipboard.writeText('<?php echo e(session('generated_password')); ?>'); this.innerHTML='<i class=\'bi bi-check2\'></i> Copié!';">
                            <i class="bi bi-clipboard"></i> Copier
                        </button>
                    </div>
                    <p class="mt-2 small text-danger fw-semibold">
                        <i class="bi bi-exclamation-triangle me-1"></i>Ce mot de passe ne sera plus affiché.
                        Communiquez-le maintenant.
                    </p>
                </div>
                <button class="btn-close" onclick="document.getElementById('dashboardPasswordAlert').remove()"></button>
            </div>
        </div>
        <?php endif; ?>

        <!-- Statistiques Projets -->
        <div class="cp-stats-grid" id="cp-stats-grid" data-section-name="statistiques">
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-green"><i class="bi bi-folder2-open"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e($totalProjets); ?></div>
                    <div class="cp-stat-label">Total Projets</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-success"><i class="bi bi-check-circle"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e($projetsTermines); ?></div>
                    <div class="cp-stat-label">Terminés</div>
                </div>
            </div>
            <div class="cp-stat-card cp-stat-danger">
                <div class="cp-stat-icon cp-bg-danger"><i class="bi bi-exclamation-triangle"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e($projetsEnRetard); ?></div>
                    <div class="cp-stat-label">En Retard</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-info"><i class="bi bi-file-earmark-text"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e($totalRapports); ?></div>
                    <div class="cp-stat-label">Rapports</div>
                </div>
            </div>
        </div>

        <!-- Statistiques Utilisateurs & Rôles -->
        <div class="cp-stats-grid mt-3" data-section-name="utilisateurs roles">
            <div class="cp-stat-card" style="cursor:pointer;"
                onclick="window.location='<?php echo e(route('super-admin.users.index')); ?>'">
                <div class="cp-stat-icon cp-bg-info"><i class="bi bi-people"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e($totalUsers); ?></div>
                    <div class="cp-stat-label">Utilisateurs Créés</div>
                </div>
            </div>
            <div class="cp-stat-card" style="cursor:pointer;"
                onclick="window.location='<?php echo e(route('super-admin.users.index')); ?>'">
                <div class="cp-stat-icon cp-bg-success"><i class="bi bi-person-check"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e($usersActifs); ?></div>
                    <div class="cp-stat-label">Comptes Actifs</div>
                </div>
            </div>
            <div class="cp-stat-card" style="cursor:pointer;"
                onclick="window.location='<?php echo e(route('super-admin.roles.index')); ?>'">
                <div class="cp-stat-icon cp-bg-green"><i class="bi bi-shield-check"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e($totalRoles); ?></div>
                    <div class="cp-stat-label">Rôles Créés</div>
                </div>
            </div>
        </div>

        <!-- Budget Global -->
        <div class="row mt-4" id="section-budget" data-section-name="budget">
            <div class="col-md-12">
                <div class="cp-stat-card cp-stat-card-budget w-100">
                    <div class="cp-stat-icon cp-bg-success"><i class="bi bi-cash-stack"></i></div>
                    <div class="cp-stat-content">
                        <div class="cp-stat-value"><?php echo e(number_format($budgetGlobal, 0, ',', ' ')); ?> FCFA</div>
                        <div class="cp-stat-label">Budget Global Alloué</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphiques -->
        <div class="cp-charts-grid mt-4" id="section-graphiques" data-section-name="graphiques">
            <div class="cp-chart-card">
                <div class="cp-chart-header">
                    <h6 class="cp-chart-title">Avancement des Projets</h6>
                </div>
                <div class="cp-chart-body">
                    <div class="cp-project-progress-list">
                        <?php $__empty_1 = true; $__currentLoopData = $avancementProjets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $projet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="cp-project-progress">
                            <div class="cp-project-name">
                                <span><?php echo e($projet['nom']); ?></span>
                                <span class="fw-bold"><?php echo e($projet['avancement']); ?>%</span>
                            </div>
                            <div class="cp-progress-bar-container">
                                <div class="cp-progress-bar bg-green" style="width: <?php echo e($projet['avancement']); ?>%">
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-muted text-center py-3">Aucun projet enregistré.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="cp-chart-card">
                <div class="cp-chart-header">
                    <h6 class="cp-chart-title">Répartition par Statut</h6>
                </div>
                <div class="cp-chart-body">
                    <canvas id="cp-repartition-chart" data-statuts='<?php echo json_encode($repartitionStatuts, 15, 512) ?>'></canvas>
                </div>
            </div>
        </div>

        <!-- Graphique Utilisateurs par Rôle -->
        <?php if($totalRoles > 0): ?>
        <?php 
        $usersParRoleData = collect($usersParRole);
        ?>
        <div class="row mt-4" data-section-name="roles utilisateurs">
            <div class="col-md-6">
                <div class="cp-chart-card h-100">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-bar-chart me-2"></i>Répartition des Rôles (Barres)</h6>
                    </div>
                    <div class="cp-chart-body" style="height:220px;">
                        <?php if($usersParRoleData->count() > 0): ?>
                        <canvas id="usersRoleChart" data-users='<?php echo e($usersParRoleData->values()->toJson()); ?>'></canvas>
                        <?php else: ?>
                        <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                            <div class="text-center">
                                <i class="bi bi-diagram-3 display-4"></i>
                                <p class="mt-2">Aucun utilisateur assigné à un rôle</p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="cp-chart-card h-100">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-pie-chart me-2"></i>Répartition des Rôles (Camembert)</h6>
                    </div>
                    <div class="cp-chart-body" style="height:220px;">
                        <?php if($usersParRoleData->count() > 0): ?>
                        <canvas id="usersRolePieChart" data-users='<?php echo e($usersParRoleData->values()->toJson()); ?>'></canvas>
                        <?php else: ?>
                        <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                            <div class="text-center">
                                <i class="bi bi-diagram-3 display-4"></i>
                                <p class="mt-2">Aucun utilisateur assigné à un rôle</p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="cp-chart-card h-100">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-lightning me-2"></i>Accès Rapides</h6>
                    </div>
                    <div class="p-3 d-flex flex-column gap-2">
                        <a href="<?php echo e(route('super-admin.roles.create')); ?>" class="btn btn-outline-primary text-start">
                            <i class="bi bi-plus-circle me-2"></i>Créer un nouveau Rôle
                        </a>
                        <a href="<?php echo e(route('super-admin.users.create')); ?>" class="btn btn-outline-success text-start">
                            <i class="bi bi-person-plus me-2"></i>Ajouter un Utilisateur
                        </a>
                        <a href="<?php echo e(route('super-admin.projets.index')); ?>" class="btn btn-outline-primary text-start">
                            <i class="bi bi-briefcase me-2"></i>Voir tous les Projets
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="row mt-4">
            <div class="col-12">
                <div class="cp-chart-card p-4 text-center"
                    style="background:linear-gradient(135deg,#e8f5e9,#c8e6c9); border: 2px dashed #009A44;">
                    <i class="bi bi-shield-plus" style="font-size:3rem;color:#009A44;"></i>
                    <h4 class="mt-3 fw-bold">Commencez par créer des Rôles</h4>
                    <p class="text-muted">Créez des rôles personnalisés, puis ajoutez-y des utilisateurs avec des
                        permissions spécifiques.</p>
                    <div class="d-flex gap-2 justify-content-center mt-3">
                        <a href="<?php echo e(route('super-admin.roles.create')); ?>" class="btn btn-primary px-4">
                            <i class="bi bi-plus-circle me-2"></i>Créer un Rôle
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/super-admin/dashboard.js')); ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const usersCanvas = document.getElementById('usersRoleChart');
        if (usersCanvas) {
            const rawData = JSON.parse(usersCanvas.dataset.users || '[]');
            if (rawData.length) {
                const palette = ['#2563eb', '#007a35', '#84cc16', '#dc2626', '#9333ea', '#0891b2', '#009A44', '#14b8a6', '#8b5cf6', '#84cc16'];
                const colors = rawData.map((_, index) => palette[index % palette.length]);
                const hexToRgba = (hex, alpha) => {
                    const clean = hex.replace('#', '');
                    const bigint = parseInt(clean, 16);
                    const r = (bigint >> 16) & 255;
                    const g = (bigint >> 8) & 255;
                    const b = bigint & 255;
                    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
                };
                
                // Bar chart
                new Chart(usersCanvas, {
                    type: 'bar',
                    data: {
                        labels: rawData.map(r => r.nom),
                        datasets: [{
                            label: 'Utilisateurs',
                            data: rawData.map(r => r.count),
                            backgroundColor: colors.map(c => hexToRgba(c, 0.82)),
                            borderColor: colors,
                            borderWidth: 1.5,
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                    }
                });
                
                // Pie chart with same data
                const pieCanvas = document.getElementById('usersRolePieChart');
                if (pieCanvas) {
                    new Chart(pieCanvas, {
                        type: 'doughnut',
                        data: {
                            labels: rawData.map(r => r.nom),
                            datasets: [{
                                data: rawData.map(r => r.count),
                                backgroundColor: colors,
                                borderWidth: 2,
                                borderColor: '#fff',
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'right' }
                            }
                        }
                    });
                }
            }
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/super-admin/dashboard.blade.php ENDPATH**/ ?>