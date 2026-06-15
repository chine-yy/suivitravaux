<?php $__env->startSection('title', 'Tableau de Bord - Admin'); ?>

<?php
    $has = fn(string $perm) => $canPermission($perm);
?>

<?php $__env->startSection('content'); ?>
    <div class="cp-dashboard">
        <div class="cp-content">
            <!-- Page Header -->
            <div class="cp-page-header">
                <div>
                    <h1 class="cp-page-title">Tableau de Bord</h1>
                    <p class="cp-page-subtitle">Vue d'ensemble — <?php echo e(now()->format('d/m/Y')); ?></p>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="row mb-4">
                <?php if($has('view-projets')): ?>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded">
                                        <i class="bi bi-kanban text-primary fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="text-muted mb-1">Projets</h6>
                                        <h3 class="mb-0"><?php echo e($totalProjets); ?></h3>
                                        <small class="text-success"><?php echo e($projetsEnCours); ?> en cours</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if($has('view-taches')): ?>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded">
                                        <i class="bi bi-check2-square text-primary fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="text-muted mb-1">Tâches</h6>
                                        <h3 class="mb-0"><?php echo e($tachesEnCours); ?></h3>
                                        <small class="text-danger"><?php echo e($tachesEnRetard); ?> en retard</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if($has('view-rapports')): ?>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded">
                                        <i class="bi bi-file-earmark-bar-graph text-primary fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="text-muted mb-1">Rapports à valider</h6>
                                        <h3 class="mb-0"><?php echo e($rapportsAValider ?? ''); ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if($has('view-incidents')): ?>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 bg-danger bg-opacity-10 p-3 rounded">
                                        <i class="bi bi-exclamation-triangle text-danger fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="text-muted mb-1">Incidents ouverts</h6>
                                        <h3 class="mb-0"><?php echo e($incidentsOuverts); ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if($has('view-satisfaction-partenaire')): ?>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 p-3 rounded" style="background-color: rgba(22, 163, 74, 0.1);">
                                        <i class="bi bi-emoji-smile fs-4" style="color: #16a34a;"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="text-muted mb-1">Satisfaction & partenaire</h6>
                                        <h3 class="mb-0"><?php echo e($totalSatisfactions); ?></h3>
                                        <small style="color: #16a34a;"><?php echo e($satisfactionsRepondues); ?> répondues</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if($has('view-stocks-materiaux')): ?>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 p-3 rounded" style="background-color: rgba(22, 163, 74, 0.1);">
                                        <i class="bi bi-boxes fs-4" style="color: #16a34a;"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="text-muted mb-1">Stocks</h6>
                                        <h3 class="mb-0"><?php echo e($totalStocks); ?></h3>
                                        <small class="text-muted">articles en stock</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if($has('view-fournisseurs')): ?>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 p-3 rounded" style="background-color: rgba(22, 163, 74, 0.1);">
                                        <i class="bi bi-truck fs-4" style="color: #16a34a;"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="text-muted mb-1">Fournisseurs</h6>
                                        <h3 class="mb-0"><?php echo e($totalFournisseurs); ?></h3>
                                        <small class="text-muted">fournisseurs</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if($has('view-documents')): ?>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 p-3 rounded" style="background-color: rgba(22, 163, 74, 0.1);">
                                        <i class="bi bi-folder2-open fs-4" style="color: #16a34a;"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="text-muted mb-1">Documents</h6>
                                        <h3 class="mb-0"><?php echo e($totalDocuments); ?></h3>
                                        <small class="text-muted">documents</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if($has('view-sous-traitances')): ?>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 p-3 rounded" style="background-color: rgba(22, 163, 74, 0.1);">
                                        <i class="bi bi-briefcase fs-4" style="color: #16a34a;"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="text-muted mb-1">Sous-traitances</h6>
                                        <h3 class="mb-0"><?php echo e($totalSousTraitances); ?></h3>
                                        <small class="text-muted">sous-traitances</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if($has('view-rendez-vous')): ?>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 p-3 rounded" style="background-color: rgba(22, 163, 74, 0.1);">
                                        <i class="bi bi-calendar-event fs-4" style="color: #16a34a;"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="text-muted mb-1">Rendez-vous</h6>
                                        <h3 class="mb-0"><?php echo e($totalRendezvous); ?></h3>
                                        <small class="text-muted">rendez-vous</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Budget -->
            <?php if($has('gerer-budgets')): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent border-0">
                                <h5 class="mb-0"><i class="bi bi-cash-stack me-2"></i>Budget</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="text-center p-3">
                                            <h6 class="text-muted">Budget Global</h6>
                                            <h4><?php echo e(number_format($budgetGlobal, 2, ',', ' ')); ?> €</h4>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center p-3">
                                            <h6 class="text-muted">Total Alloué</h6>
                                            <h4><?php echo e(number_format($totalAlloue, 2, ',', ' ')); ?> €</h4>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center p-3">
                                            <h6 class="text-muted">Total Consommé</h6>
                                            <h4><?php echo e(number_format($totalConsomme, 2, ',', ' ')); ?> €</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($has('view-rapports') && auth()->user() && auth()->user()->isAdminEntreprise() && !auth()->user()->isSuperAdmin() && $rapportsEnAttente->isNotEmpty()): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Rapports en attente</h5>
                                <span class="badge bg-warning"><?php echo e($rapportsEnAttente->count()); ?></span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Rapport</th>
                                                <th>Projet</th>
                                                <th>Auteur</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $rapportsEnAttente->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rapport): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><?php echo e($rapport->titre); ?></td>
                                                    <td><?php echo e($rapport->projet->nom ?? 'N/A'); ?></td>
                                                    <td><?php echo e($rapport->auteur->name ?? 'N/A'); ?></td>
                                                    <td><?php echo e($rapport->created_at->format('d/m/Y H:i')); ?></td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Fonctionnalités -->
            <?php
                $hasRolesAccess = collect(['view', 'create', 'edit', 'delete', 'export'])
                    ->contains(fn($action) => $has($action . '-roles-permissions'));

                $features = collect([
                    ['permission' => 'view-projets', 'route' => 'role-dynamique.projets.index', 'label' => 'Projets', 'icon' => 'bi-kanban'],
                    ['permission' => 'view-phases', 'route' => 'role-dynamique.phases.index', 'label' => 'Phases', 'icon' => 'bi-collection'],
                    ['permission' => 'view-taches', 'route' => 'role-dynamique.taches.index', 'label' => 'Tâches', 'icon' => 'bi-check2-square'],
                    ['permission' => 'view-sous-taches', 'route' => 'role-dynamique.sous-taches.index', 'label' => 'Sous-Tâches', 'icon' => 'bi-list-check'],
                    ['permission' => 'view-rapports', 'route' => 'role-dynamique.rapports.index', 'label' => 'Rapports', 'icon' => 'bi-file-earmark-text'],
                    ['permission' => 'view-incidents', 'route' => 'role-dynamique.incidents.index', 'label' => 'Incidents', 'icon' => 'bi-exclamation-triangle'],
                    ['permission' => 'view-interventions', 'route' => 'role-dynamique.interventions.index', 'label' => 'Interventions', 'icon' => 'bi-tools'],
                    ['permission' => 'view-rendez-vous', 'route' => 'role-dynamique.rendezvous.index', 'label' => 'Rendez-vous', 'icon' => 'bi-calendar-event'],
                    ['permission' => 'view-sous-traitances', 'route' => 'role-dynamique.sous-traitances.index', 'label' => 'Sous-traitances', 'icon' => 'bi-briefcase'],
                    ['permission' => 'view-documents', 'route' => 'role-dynamique.documents.index', 'label' => 'Documents', 'icon' => 'bi-file-earmark'],
                    ['permission' => 'view-equipes', 'route' => 'role-dynamique.equipes.index', 'label' => 'Équipes', 'icon' => 'bi-people'],
                    ['permission' => 'view-partenaires', 'route' => 'role-dynamique.partenaires.index', 'label' => 'Partenaires', 'icon' => 'bi-person-workspace'],
                    ['permission' => 'view-satisfaction-partenaire', 'route' => 'role-dynamique.satisfaction.index', 'label' => 'Satisfaction', 'icon' => 'bi-emoji-smile'],
                    ['permission' => 'view-stocks-materiaux', 'route' => 'role-dynamique.stocks.index', 'label' => 'Stocks', 'icon' => 'bi-boxes'],
                    ['permission' => 'view-fournisseurs', 'route' => 'role-dynamique.fournisseurs.index', 'label' => 'Fournisseurs', 'icon' => 'bi-truck'],
                    ['permission' => 'gerer-budgets', 'route' => 'role-dynamique.budget.index', 'label' => 'Budgets', 'icon' => 'bi-cash-stack'],
                    ['permission' => 'view-depenses', 'route' => 'role-dynamique.depenses.index', 'label' => 'Dépenses', 'icon' => 'bi-receipt'],
                    ['permission' => 'view-budget-allocation-projet', 'route' => 'role-dynamique.allocation-projet.index', 'label' => 'Allocation Projet', 'icon' => 'bi-diagram-3'],
                    ['permission' => 'view-budget-allocation-sous-traitance', 'route' => 'role-dynamique.allocation-sous-traitance.index', 'label' => 'Allocation ST', 'icon' => 'bi-person-gear'],
                    ['permission' => 'view-historique', 'route' => 'role-dynamique.historique.index', 'label' => 'Historique', 'icon' => 'bi-clock-history'],
                    ['permission' => 'chat-messagerie-activer', 'route' => 'role-dynamique.chat.index', 'label' => 'Messagerie', 'icon' => 'bi-chat-dots'],
                    ['permission' => 'activer-ia-chat-box', 'route' => 'role-dynamique.ia-chat.index', 'label' => 'IA Chat Box', 'icon' => 'bi-chat-square-quote'],
                ]);

                if ($hasRolesAccess) {
                    $features->push([
                        'permission' => 'roles-permissions-any',
                        'route' => 'role-dynamique.roles.index',
                        'label' => 'Rôles & Permissions',
                        'icon' => 'bi-shield-check'
                    ]);
                }

                $features = $features->filter(
                    fn($item) =>
                    (isset($item['permission']) && $item['permission'] === 'roles-permissions-any') || $has($item['permission'])
                )->filter(fn($item) => Route::has($item['route']));
            ?>

            <!-- Graphiques -->
            <div class="row mb-4">
                <?php if($has('view-taches') && $has('view-projets')): ?>
                    <div class="col-xl-6 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-transparent border-0">
                                <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Tâches par projet</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="tachesChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if($has('view-projets')): ?>
                    <div class="col-xl-6 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-transparent border-0">
                                <h5 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Répartition des projets</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="projetsChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if($features->isNotEmpty()): ?>
                <div class="d-flex justify-content-center align-items-center mb-3 text-center">
                    <h5 class="mb-0"><i class="bi bi-grid-1x2 me-2"></i>Mes fonctionnalités</h5>
                </div>
                <div class="row g-3 mb-4">
                    <?php $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-6 col-md-4 col-xl-3">
                            <div class="card border-0 shadow-sm h-100 feature-square-card">
                                <div class="card-body d-flex flex-column justify-content-between text-center p-3">
                                    <div>
                                        <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-primary bg-opacity-10 mb-3"
                                            style="width:56px;height:56px;">
                                            <i class="bi <?php echo e($feature['icon']); ?> text-primary fs-4"></i>
                                        </div>
                                        <h6 class="mb-0"><?php echo e($feature['label']); ?></h6>
                                    </div>
                                    <a href="<?php echo e(route($feature['route'])); ?>" class="btn btn-outline-primary btn-sm mt-3">Accéder</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <!-- Alertes -->
            <?php if($alertes->isNotEmpty()): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent border-0">
                                <h5 class="mb-0"><i class="bi bi-bell me-2"></i>Alertes</h5>
                            </div>
                            <div class="card-body">
                                <?php $__currentLoopData = $alertes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alerte): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div
                                        class="alert alert-<?php echo e($alerte['type'] === 'critique' ? 'danger' : 'warning'); ?> d-flex align-items-center mb-2">
                                        <i
                                            class="bi bi-<?php echo e($alerte['type'] === 'critique' ? 'exclamation-octagon' : 'exclamation-triangle'); ?> me-2"></i>
                                        <div class="flex-grow-1">
                                            <strong><?php echo e($alerte['titre']); ?></strong> - <?php echo e($alerte['message']); ?>

                                        </div>
                                        <a href="<?php echo e($alerte['lien']); ?>"
                                            class="btn btn-sm btn-outline-<?php echo e($alerte['type'] === 'critique' ? 'danger' : 'warning'); ?>">
                                            Voir
                                        </a>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Listes -->
            <div class="row">
                <?php if($has('view-taches') && $tachesEnRetardList->isNotEmpty()): ?>
                    <div class="col-xl-6 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Tâches en retard</h5>
                                <span class="badge bg-danger"><?php echo e($tachesEnRetardList->count()); ?></span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Tâche</th>
                                                <th>Projet</th>
                                                <th>Échéance</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $tachesEnRetardList->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tache): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><?php echo e($tache->titre); ?></td>
                                                    <td><?php echo e($tache->projet->nom ?? 'N/A'); ?></td>
                                                    <td class="text-danger"><?php echo e($tache->date_fin_prevue->format('d/m/Y')); ?></td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <!-- Calendrier -->
            <?php if($has('view-phases') && $evenementsCalendrier->isNotEmpty()): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent border-0">
                                <h5 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Calendrier des échéances</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php $__currentLoopData = $evenementsCalendrier; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="col-md-2 col-sm-4 mb-3">
                                            <div
                                                class="p-3 text-center rounded border <?php echo e($event['type'] === 'retard' ? 'border-danger bg-danger bg-opacity-10' : 'border-primary bg-primary bg-opacity-10'); ?>">
                                                <div class="fs-3 fw-bold"><?php echo e($event['jour']); ?></div>
                                                <small class="text-muted"><?php echo e($event['titre']); ?></small>
                                                <div class="small text-truncate"><?php echo e($event['projet']); ?></div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <?php $__env->startPush('styles'); ?>
        <style>
            .feature-square-card .card-body {
                aspect-ratio: 1 / 1;
                min-height: 210px;
            }

            @media (max-width: 767.98px) {
                .feature-square-card .card-body {
                    aspect-ratio: auto;
                    min-height: 170px;
                }
            }
        </style>
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('scripts'); ?>
        <script src="<?php echo e(asset('js/chart.min.js')); ?>"></script>
        <script>
            const statusColors = {
                enCours: '#2563eb',
                termines: '#007a35',
                enRetard: '#dc2626',
                enAttente: '#84cc16',
                enPause: '#64748b'
            };

            // Graphique répartition projets
            <?php if($has('view-projets')): ?>
                new Chart(document.getElementById('projetsChart'), {
                    type: 'doughnut',
                    data: {
                        labels: ['En cours', 'Terminés', 'En retard', 'En attente', 'En pause'],
                        datasets: [{
                            data: [
                                <?php echo e($projetsEnCours); ?>,
                                <?php echo e($projetsTermines); ?>,
                                <?php echo e($projetsEnRetard); ?>,
                                <?php echo e($projetsEnAttente); ?>,
                                <?php echo e($repartitionStatuts['en_pause'] ?? 0); ?>

                            ],
                            backgroundColor: [
                                statusColors.enCours,
                                statusColors.termines,
                                statusColors.enRetard,
                                statusColors.enAttente,
                                statusColors.enPause
                            ],
                            borderColor: '#ffffff',
                            borderWidth: 2,
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            <?php endif; ?>

            // Graphique tâches par projet
            <?php if($has('view-taches') && $has('view-projets')): ?>
                new Chart(document.getElementById('tachesChart'), {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($tachesParProjet->pluck('nom')); ?>,
                        datasets: [
                            {
                                label: 'Terminées',
                                data: <?php echo json_encode($tachesParProjet->pluck('terminees')); ?>,
                                backgroundColor: statusColors.termines,
                                borderColor: '#0f8a43',
                                borderWidth: 1
                            },
                            {
                                label: 'En retard',
                                data: <?php echo json_encode($tachesParProjet->pluck('en_retard')); ?>,
                                backgroundColor: statusColors.enRetard,
                                borderColor: '#b91c1c',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                stacked: true
                            },
                            y: {
                                stacked: true
                            }
                        }
                    }
                });
            <?php endif; ?>
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.role-dynamique', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/role-dynamique/dashboard.blade.php ENDPATH**/ ?>