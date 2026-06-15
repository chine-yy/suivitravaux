<?php $__env->startSection('title', 'Historique <?php echo e($annee); ?> — Super Admin'); ?>

<?php $__env->startSection('breadcrumb'); ?>
<a href="<?php echo e(route('super-admin.historique.index')); ?>" class="cp-breadcrumb-item">Historique</a>
<span class="cp-breadcrumb-separator">/</span>
<span class="cp-breadcrumb-item active"><?php echo e($annee); ?></span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-dashboard">
    <div class="cp-content">

        <!-- Header -->
        <div class="cp-page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h1 class="cp-page-title">
                    <i class="bi bi-calendar-range me-2" style="color: var(--cp-orange);"></i>
                    Historique <span style="color: var(--cp-orange);"><?php echo e($annee); ?></span>
                </h1>
                <p class="cp-page-subtitle">Vue complète de toutes les données enregistrées pour l'année <?php echo e($annee); ?>

                </p>
            </div>
<div class="d-flex gap-2">
                <a href="<?php echo e(route('super-admin.historique.pdf', $annee)); ?>" class="btn btn-outline-danger">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Exporter tout
                </a>
                <a href="<?php echo e(route('super-admin.historique.voir-pdf', $annee)); ?>" class="btn btn-outline-primary" target="_blank">
                    <i class="bi bi-eye me-2"></i>Voir PDF
                </a>
                <a href="<?php echo e(route('super-admin.historique.index')); ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Toutes les années
                </a>
            </div>
        </div>


        <!-- Stats Cards -->
        <div class="hist-stats-grid mt-4">
            <div class="hist-stat-card hist-stat-blue">
                <i class="bi bi-briefcase"></i>
                <div><strong><?php echo e($stats['total_projets']); ?></strong><span>Projets</span></div>
            </div>
            <div class="hist-stat-card hist-stat-purple">
                <i class="bi bi-collection"></i>
                <div><strong><?php echo e($stats['total_phases']); ?></strong><span>Phases</span></div>
            </div>
            <div class="hist-stat-card hist-stat-orange">
                <i class="bi bi-list-task"></i>
                <div><strong><?php echo e($stats['total_taches']); ?></strong><span>Tâches</span></div>
            </div>
            <div class="hist-stat-card hist-stat-teal">
                <i class="bi bi-list-check"></i>
                <div><strong><?php echo e($stats['total_sous_taches']); ?></strong><span>Sous-Tâches</span></div>
            </div>
            <div class="hist-stat-card hist-stat-red">
                <i class="bi bi-exclamation-triangle"></i>
                <div><strong><?php echo e($stats['total_incidents']); ?></strong><span>Incidents</span></div>
            </div>
            <div class="hist-stat-card hist-stat-indigo">
                <i class="bi bi-file-earmark-text"></i>
                <div><strong><?php echo e($stats['total_rapports']); ?></strong><span>Rapports</span></div>
            </div>
            <div class="hist-stat-card hist-stat-emerald">
                <i class="bi bi-cash-stack"></i>
                <div><strong><?php echo e(number_format($stats['budget_total'], 0, ',', ' ')); ?></strong><span>Budget FCFA</span>
                </div>
            </div>
            <div class="hist-stat-card hist-stat-cyan">
                <i class="bi bi-people"></i>
                <div><strong><?php echo e($stats['total_membres']); ?></strong><span>Membres</span></div>
            </div>
            <div class="hist-stat-card hist-stat-yellow">
                <i class="bi bi-person-workspace"></i>
                <div><strong><?php echo e($stats['total_partenaires']); ?></strong><span>Partenaires</span></div>
            </div>
            <div class="hist-stat-card" style="background:#f3f4f6; color:#1f2937;">
                <i class="bi bi-folder2-open"></i>
                <div><strong><?php echo e($stats['total_documents']); ?></strong><span>Documents</span></div>
            </div>
            <div class="hist-stat-card" style="background:#e0f2fe; color:#0369a1;">
                <i class="bi bi-file-earmark-ruled"></i>
                <div><strong><?php echo e($stats['total_contrats']); ?></strong><span>Contrats</span></div>
            </div>
            <div class="hist-stat-card" style="background:#fce7f3; color:#be185d;">
                <i class="bi bi-truck"></i>
                <div><strong><?php echo e($stats['total_fournisseurs']); ?></strong><span>Fournisseurs</span></div>
            </div>
        </div>

        <!-- Navigation onglets (scrollable) -->
        <div class="mt-4">
            <ul class="nav nav-tabs hist-tabs flex-nowrap overflow-auto" id="histTabs" role="tablist"
                style="white-space:nowrap;">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-projets" type="button">
                        <i class="bi bi-briefcase me-1"></i> Projets
                        <span class="badge bg-primary ms-1"><?php echo e($stats['total_projets']); ?></span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-phases" type="button">
                        <i class="bi bi-collection me-1"></i> Phases
                        <span class="badge bg-secondary ms-1"><?php echo e($stats['total_phases']); ?></span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-taches" type="button">
                        <i class="bi bi-list-task me-1"></i> Tâches
                        <span class="badge bg-warning text-dark ms-1"><?php echo e($stats['total_taches']); ?></span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-sous-taches" type="button">
                        <i class="bi bi-list-check me-1"></i> Sous-Tâches
                        <span class="badge bg-secondary ms-1"><?php echo e($stats['total_sous_taches']); ?></span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-incidents" type="button">
                        <i class="bi bi-exclamation-triangle me-1"></i> Incidents
                        <span class="badge bg-danger ms-1"><?php echo e($stats['total_incidents']); ?></span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-rapports" type="button">
                        <i class="bi bi-file-earmark-text me-1"></i> Rapports
                        <span class="badge bg-info text-dark ms-1"><?php echo e($stats['total_rapports']); ?></span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-budget" type="button">
                        <i class="bi bi-cash-stack me-1"></i> Budgets
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-roles" type="button">
                        <i class="bi bi-shield-check me-1"></i> Rôles
                        <span class="badge bg-warning text-dark ms-1"><?php echo e($stats['total_roles'] ?? $roles->count()); ?></span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-users" type="button">
                        <i class="bi bi-people me-1"></i> Utilisateurs
                        <span class="badge bg-info text-dark ms-1"><?php echo e($stats['total_users'] ?? $users->count()); ?></span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-equipes" type="button">
                        <i class="bi bi-people-fill me-1"></i> Équipes
                        <span class="badge bg-warning text-dark ms-1"><?php echo e($stats['total_equipes'] ?? $equipes->count()); ?></span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-partenaires" type="button">
                        <i class="bi bi-person-workspace me-1"></i> Partenaires
                        <span class="badge bg-secondary ms-1"><?php echo e($stats['total_partenaires']); ?></span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-documents" type="button">
                        <i class="bi bi-folder2-open me-1"></i> Documents
                        <span class="badge bg-dark ms-1"><?php echo e($stats['total_documents']); ?></span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-contrats" type="button">
                        <i class="bi bi-file-earmark-ruled me-1"></i> Contrats
                        <span class="badge bg-primary ms-1"><?php echo e($stats['total_contrats']); ?></span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-factures" type="button">
                        <i class="bi bi-receipt-cutoff me-1"></i> Factures
                        <span class="badge bg-secondary ms-1"><?php echo e($stats['total_factures']); ?></span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-interventions" type="button">
                        <i class="bi bi-tools me-1"></i> Interventions
                        <span class="badge bg-info text-dark ms-1"><?php echo e($stats['total_interventions']); ?></span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-soustraitances" type="button">
                        <i class="bi bi-diagram-3 me-1"></i> Sous-Traitances
                        <span class="badge bg-warning text-dark ms-1"><?php echo e($stats['total_sous_traitances'] ?? $sousTraitances->count()); ?></span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-fournisseurs" type="button">
                        <i class="bi bi-truck me-1"></i> Fournisseurs
                        <span class="badge bg-warning text-dark ms-1"><?php echo e($stats['total_fournisseurs']); ?></span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-stocks" type="button">
                        <i class="bi bi-box-seam me-1"></i> Stocks
                        <span class="badge bg-secondary ms-1"><?php echo e($stats['total_stocks']); ?></span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-rendezvous" type="button">
                        <i class="bi bi-calendar-event me-1"></i> RDV
                        <span class="badge bg-success ms-1"><?php echo e($stats['total_rendezvous']); ?></span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-satisfaction" type="button">
                        <i class="bi bi-emoji-smile me-1"></i> Satisfaction
                        <span class="badge bg-danger ms-1"><?php echo e($stats['total_satisfactions']); ?></span>
                    </button>
                </li>
            </ul>

            <div class="tab-content hist-tab-content">

                
                <div class="tab-pane fade show active" id="tab-projets">
                    <?php $__empty_1 = true; $__currentLoopData = $projets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $projet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="hist-card">
                        <div class="hist-card-header">
                            <div class="hist-card-icon-title">
                                <div class="hist-card-icon"><i class="bi bi-briefcase-fill"></i></div>
                                <div>
                                    <h6 class="mb-0 fw-bold"><?php echo e($projet->nom); ?></h6>
                                    <div class="d-flex flex-wrap gap-2 mt-1 align-items-center">
                                        <?php
                                        $sc = ['termine'=>['bg-success','Terminé'],'en_cours'=>['bg-primary','En
                                        cours'],'en_retard'=>['bg-danger','En retard'],'en_attente'=>['bg-warning
                                        text-dark','En attente']][$projet->statut ?? ''] ?? ['bg-secondary','N/A'];
                                        ?>
                                        <span class="badge <?php echo e($sc[0]); ?>"><?php echo e($sc[1]); ?></span>
                                        <?php $projPartenaires = isset($partenaires) ? $partenaires->where('projet_id', $projet->id) : collect(); ?>
                                        <?php if($projPartenaires->count()): ?>
                                            <?php $__currentLoopData = $projPartenaires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <small class="text-muted d-block"><i class="bi bi-person me-1"></i><?php echo e($c->nom ?? '—'); ?> <?php echo e($c->prenom ?? ''); ?></small>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php else: ?>
                                            <?php if($projet->partenaire): ?>
                                                <small class="text-muted"><i class="bi bi-person me-1"></i><?php echo e($projet->partenaire->nom); ?> <?php echo e($projet->partenaire->prenom ?? ''); ?></small>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if($projet->admin): ?><small class="text-muted"><i
                                                class="bi bi-person-check me-1"></i><?php echo e($projet->admin->name); ?></small><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div style="min-width:140px;">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">Avancement</small>
                                    <small class="fw-bold"><?php echo e($projet->avancement ?? 0); ?>%</small>
                                </div>
                                <div class="progress" style="height:8px;border-radius:4px;">
                                    <div class="progress-bar bg-primary" style="width:<?php echo e($projet->avancement ?? 0); ?>%;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="hist-card-meta">
                            <?php if($projet->date_debut): ?><span><i class="bi bi-calendar-event text-primary me-1"></i><?php echo e($projet->date_debut->format('d/m/Y')); ?></span><?php endif; ?>
                            <?php if($projet->date_fin_prevue): ?><span><i class="bi bi-calendar-check text-success me-1"></i><?php echo e($projet->date_fin_prevue->format('d/m/Y')); ?></span><?php endif; ?>
                            <?php if($projet->budget): ?><span><i class="bi bi-cash text-warning me-1"></i><?php echo e(number_format($projet->budget, 0, ',', ' ')); ?> FCFA</span><?php endif; ?>
                            <?php if($projet->type_travaux): ?><span><i class="bi bi-tools text-secondary me-1"></i><?php echo e($projet->type_travaux); ?></span><?php endif; ?>
                        </div>
                        <div class="hist-card-footer-stats">
                            <span><i class="bi bi-collection text-purple me-1"></i><?php echo e($projet->phases->count()); ?>

                                phases</span>
                            <span><i class="bi bi-list-task text-warning me-1"></i><?php echo e($projet->taches->count()); ?>

                                tâches</span>
                            <span><i class="bi bi-exclamation-triangle text-danger me-1"></i><?php echo e($projet->incidents->count()); ?> incidents</span>
                            <span><i class="bi bi-file-earmark-text text-primary me-1"></i><?php echo e($projet->rapports->count()); ?> rapports</span>
                            <span><i class="bi bi-receipt text-info me-1"></i><?php echo e($projet->depenses->count()); ?>

                                dépenses</span>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="hist-empty"><i class="bi bi-briefcase"></i>
                        <p>Aucun projet pour <?php echo e($annee); ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                
                <div class="tab-pane fade" id="tab-phases">
                    <?php $__empty_1 = true; $__currentLoopData = $phases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="hist-card hist-card-sm">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div>
                                <strong class="text-dark"><i class="bi bi-collection me-2 text-purple"></i><?php echo e($phase->nom); ?></strong>
                                <span class="text-muted small ms-2">— <?php echo e($phase->projet->nom ?? 'N/A'); ?></span>
                            </div>
                            <div class="d-flex gap-2 align-items-center">
                                <?php if($phase->statut === 'terminee'): ?><span class="badge bg-success">Terminée</span>
                                <?php elseif($phase->statut === 'en_cours'): ?><span class="badge bg-primary">En cours</span>
                                <?php else: ?><span class="badge bg-secondary"><?php echo e(ucfirst($phase->statut ?? 'N/A')); ?></span><?php endif; ?>
                                <span class="badge bg-light text-dark"><?php echo e($phase->taches->count()); ?> tâches</span>
                            </div>
                        </div>
                        <?php if($phase->description): ?>
                        <p class="text-muted small mt-1 mb-0"><?php echo e($phase->description); ?></p>
                        <?php endif; ?>
                        <?php if($phase->date_debut || $phase->date_fin): ?>
                        <div class="hist-card-meta mt-2">
                            <?php if($phase->date_debut): ?><span><i class="bi bi-calendar-event text-primary me-1"></i><?php echo e(\Carbon\Carbon::parse($phase->date_debut)->format('d/m/Y')); ?></span><?php endif; ?>
                            <?php if($phase->date_fin): ?><span><i class="bi bi-calendar-check text-success me-1"></i><?php echo e(\Carbon\Carbon::parse($phase->date_fin)->format('d/m/Y')); ?></span><?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="hist-empty"><i class="bi bi-collection"></i>
                        <p>Aucune phase pour <?php echo e($annee); ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                
                <div class="tab-pane fade" id="tab-taches">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Titre</th>
                                    <th>Projet</th>
                                    <th>Responsable</th>
                                    <th>Statut</th>
                                    <th>Priorité</th>
                                    <th>Sous-tâches</th>
                                    <th>Dates</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $taches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tache): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><strong><?php echo e($tache->titre); ?></strong></td>
                                    <td class="text-muted small"><?php echo e($tache->projet->nom ?? 'N/A'); ?></td>
                                    <td>
                                        <?php if($tache->responsable): ?>
                                        <div class="d-flex align-items-center gap-1">
                                            <span class="hist-avatar-sm"><?php echo e(substr($tache->responsable->name, 0, 1)); ?></span>
                                            <small><?php echo e($tache->responsable->name); ?></small>
                                        </div>
                                        <?php else: ?><span class="text-muted">—</span><?php endif; ?>
                                    </td>
                                    <td>
                                        <?php $ts = ['terminee'=>['bg-success','Terminée'],'en_cours'=>['bg-primary','En
                                        cours'],'en_attente'=>['bg-secondary','En
                                        attente'],'annulee'=>['bg-danger','Annulée']][$tache->statut ?? ''] ??
                                        ['bg-light text-dark',ucfirst($tache->statut ?? 'N/A')]; ?>
                                        <span class="badge <?php echo e($ts[0]); ?>"><?php echo e($ts[1]); ?></span>
                                    </td>
                                    <td>
                                        <?php if($tache->priorite): ?>
                                        <?php $pc =
                                        ['haute'=>'danger','moyenne'=>'warning','basse'=>'success'][$tache->priorite] ??
                                        'secondary'; ?>
                                        <span class="badge bg-<?php echo e($pc); ?>"><?php echo e(ucfirst($tache->priorite)); ?></span>
                                        <?php else: ?><span class="text-muted">—</span><?php endif; ?>
                                    </td>
                                    <td><span class="badge bg-light text-dark"><?php echo e($tache->sousTaches->count()); ?></span>
                                    </td>
                                    <td class="text-muted small">
                                        <?php if($tache->date_debut): ?><?php echo e(\Carbon\Carbon::parse($tache->date_debut)->format('d/m/Y')); ?><?php endif; ?>
                                        <?php if($tache->date_fin_prevue): ?> → <?php echo e(\Carbon\Carbon::parse($tache->date_fin_prevue)->format('d/m/Y')); ?><?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Aucune tâche pour <?php echo e($annee); ?>

                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                
                <div class="tab-pane fade" id="tab-sous-taches">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Sous-tâche</th>
                                    <th>Tâche parente</th>
                                    <th>Projet</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $sousTaches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><strong><?php echo e($st->titre ?? $st->description ?? 'Sous-tâche #' . $st->id); ?></strong></td>
                                    <td class="text-muted small"><?php echo e($st->tache->titre ?? 'N/A'); ?></td>
                                    <td class="text-muted small"><?php echo e($st->tache->projet->nom ?? 'N/A'); ?></td>
                                    <td>
                                        <?php if($st->fait ?? false): ?>
                                        <span class="badge bg-success">Terminée</span>
                                        <?php else: ?>
                                        <span class="badge bg-warning text-dark">En cours</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Aucune sous-tâche pour <?php echo e($annee); ?></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                
                <div class="tab-pane fade" id="tab-incidents">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Titre</th>
                                    <th>Projet</th>
                                    <th>Type</th>
                                    <th>Gravité</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $incidents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><strong><?php echo e($inc->titre ?? $inc->description ?? 'Incident #' . $inc->id); ?></strong></td>
                                    <td class="text-muted small">
                                        <?php $projetInc = $projets->find($inc->projet_id); ?>
                                        <?php echo e($projetInc->nom ?? 'N/A'); ?>

                                    </td>
                                    <td><?php echo e($inc->type ?? '—'); ?></td>
                                    <td>
                                        <?php $gc =
                                        ['critique'=>'danger','haute'=>'danger','moyenne'=>'warning','basse'=>'success'][$inc->gravite
                                        ?? ''] ?? 'secondary'; ?>
                                        <?php if($inc->gravite): ?><span class="badge bg-<?php echo e($gc); ?>"><?php echo e(ucfirst($inc->gravite)); ?></span><?php else: ?><span class="text-muted">—</span><?php endif; ?>
                                    </td>
                                    <td>
                                        <?php $ic = ['resolu'=>['bg-success','Résolu'],'en_cours'=>['bg-primary','En
                                        cours'],'ouvert'=>['bg-warning text-dark','Ouvert']][$inc->statut ?? ''] ??
                                        ['bg-secondary', ucfirst($inc->statut ?? 'N/A')]; ?>
                                        <span class="badge <?php echo e($ic[0]); ?>"><?php echo e($ic[1]); ?></span>
                                    </td>
                                    <td class="text-muted small"><?php echo e($inc->created_at ? $inc->created_at->format('d/m/Y')
                                        : '—'); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Aucun incident pour <?php echo e($annee); ?>

                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                
                <div class="tab-pane fade" id="tab-rapports">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Titre</th>
                                    <th>Projet</th>
                                    <th>Auteur</th>
                                    <th>Type</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $rapports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rapport): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><strong><?php echo e($rapport->titre); ?></strong></td>
                                    <td class="text-muted small">
                                        <?php $projetRap = $projets->find($rapport->projet_id); ?>
                                        <?php echo e($projetRap->nom ?? 'N/A'); ?>

                                    </td>
                                    <td>
                                        <?php if($rapport->auteur): ?>
                                        <div class="d-flex align-items-center gap-1">
                                            <span class="hist-avatar-sm"><?php echo e(substr($rapport->auteur->name, 0, 1)); ?></span>
                                            <small><?php echo e($rapport->auteur->name); ?></small>
                                        </div>
                                        <?php else: ?><span class="text-muted">—</span><?php endif; ?>
                                    </td>
                                    <td><span class="badge bg-light text-dark"><?php echo e($rapport->type ?? '—'); ?></span></td>
                                    <td>
                                        <?php $rs = ['valide'=>['bg-success','Validé'],'en_attente'=>['bg-warning
                                        text-dark','En attente'],'rejete'=>['bg-danger','Rejeté']][$rapport->statut ??
                                        ''] ?? ['bg-secondary', ucfirst($rapport->statut ?? 'N/A')]; ?>
                                        <span class="badge <?php echo e($rs[0]); ?>"><?php echo e($rs[1]); ?></span>
                                    </td>
                                    <td class="text-muted small"><?php echo e($rapport->created_at ?
                                        $rapport->created_at->format('d/m/Y') : '—'); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Aucun rapport pour <?php echo e($annee); ?>

                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                
                <div class="tab-pane fade" id="tab-budget">
                    <!-- Résumé -->
                    <div class="hist-budget-summary mb-4">
                        <div class="hist-budget-card hist-budget-total">
                            <i class="bi bi-cash-coin"></i>
                            <div><strong><?php echo e(number_format($stats['budget_total'], 0, ',', ' ')); ?>

                                    FCFA</strong><span>Budget Total <?php echo e($annee); ?></span></div>
                        </div>
                        <div class="hist-budget-card hist-budget-consomme">
                            <i class="bi bi-graph-down-arrow"></i>
                            <div><strong><?php echo e(number_format($stats['budget_consomme'], 0, ',', ' ')); ?>

                                    FCFA</strong><span>Consommé</span></div>
                        </div>
                        <div class="hist-budget-card hist-budget-restant">
                            <i class="bi bi-piggy-bank"></i>
                            <div><strong><?php echo e(number_format($stats['budget_restant'], 0, ',', ' ')); ?>

                                    FCFA</strong><span>Restant</span></div>
                        </div>
                        <div class="hist-budget-card hist-budget-factures">
                            <i class="bi bi-receipt-cutoff"></i>
                            <div><strong><?php echo e(number_format($stats['total_factures'], 0, ',', ' ')); ?>

                                    FCFA</strong><span>Total Factures</span></div>
                        </div>
                    </div>

                    <?php if($stats['budget_total'] > 0): ?>
                    <div class="hist-card hist-card-sm mb-3">
                        <?php $taux = $stats['budget_total'] > 0 ? round(($stats['budget_consomme'] /
                        $stats['budget_total']) * 100) : 0; ?>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold">Taux d'utilisation du budget</span>
                            <span
                                class="fw-bold <?php echo e($taux > 90 ? 'text-danger' : ($taux > 70 ? 'text-warning' : 'text-success')); ?>"><?php echo e($taux); ?>%</span>
                        </div>
                        <div class="progress" style="height:12px;border-radius:6px;">
                            <div class="progress-bar <?php echo e($taux > 90 ? 'bg-danger' : ($taux > 70 ? 'bg-warning' : 'bg-success')); ?>"
                                style="width:<?php echo e($taux); ?>%;"></div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if($budgets->count() > 0): ?>
                    <h6 class="fw-bold mb-3"><i class="bi bi-wallet2 me-2"></i>Budgets annuels</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Description</th>
                                    <th>Budget Total</th>
                                    <th>Statut</th>
                                    <th>Projets associés</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $budgets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($b->description ?? 'Budget ' . $b->annee); ?></td>
                                    <td><strong><?php echo e(number_format($b->budget_total, 0, ',', ' ')); ?> FCFA</strong></td>
                                    <td>
                                        <?php $sColors = ['brouillon'=>'secondary','valide'=>'success','clos'=>'dark'];
                                        ?>
                                        <span class="badge bg-<?php echo e($sColors[$b->statut] ?? 'secondary'); ?>"><?php echo e($b->getStatutLabel()); ?></span>
                                    </td>
                                    <td>
                                        <?php $__currentLoopData = $b->budgetProjets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="badge bg-light text-dark me-1"><?php echo e($bp->projet->nom ?? 'N/A'); ?></span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>

                    <?php if($factures->count() > 0): ?>
                    <h6 class="fw-bold mt-4 mb-3"><i class="bi bi-receipt-cutoff me-2"></i>Factures (<?php echo e($factures->count()); ?>)</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Référence</th>
                                    <th>Notes</th>
                                    <th>Montant TTC</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $factures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><strong><?php echo e($f->numero_facture ?? '#' . $f->id); ?></strong></td>
                                    <td class="text-muted small"><?php echo e($f->notes ?? '—'); ?></td>
                                    <td><?php echo e(number_format($f->montant_ttc ?? 0, 0, ',', ' ')); ?> FCFA</td>
                                    <td><span class="badge bg-light text-dark"><?php echo e($f->statut_paiement ?? '—'); ?></span>
                                    </td>
                                    <td class="text-muted small"><?php echo e($f->date_emission ?
                                        $f->date_emission->format('d/m/Y') : ($f->created_at ?
                                        $f->created_at->format('d/m/Y') : '—')); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>

                    <?php if($sousTraitances->count() > 0): ?>
                    <h6 class="fw-bold mt-4 mb-3"><i class="bi bi-tools me-2"></i>Sous-traitances</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Prestataire</th>
                                    <th>Montant</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $sousTraitances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><strong><?php echo e($st->nom_entreprise ?? $st->nom_prestataire ?? '—'); ?></strong></td>
                                    <td><?php echo e(number_format($st->montant_contrat ?? 0, 0, ',', ' ')); ?> FCFA</td>
                                    <td><span class="badge bg-light text-dark"><?php echo e($st->statut ?? '—'); ?></span></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>

                
                <div class="tab-pane fade" id="tab-roles">
                    <div class="table-responsive mt-2">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Rôle</th>
                                    <th>Slug</th>
                                    <th>Permissions</th>
                                    <th>Utilisateurs</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="hist-avatar-sm" style="background:#7c3aed;"><?php echo e(substr($role->nom, 0, 1)); ?></span>
                                            <strong><?php echo e($role->nom); ?></strong>
                                        </div>
                                    </td>
                                    <td><code class="text-muted small"><?php echo e($role->slug ?? '—'); ?></code></td>
                                    <td>
                                        <?php if($role->permissions->count() > 0): ?>
                                        <?php $__currentLoopData = $role->permissions->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="badge bg-light text-dark me-1" style="font-size:0.68rem;"><?php echo e($perm->nom); ?></span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($role->permissions->count() > 5): ?>
                                        <span class="text-muted small">+<?php echo e($role->permissions->count() - 5); ?>

                                            autres</span>
                                        <?php endif; ?>
                                        <?php else: ?>
                                        <span class="text-muted small">Aucune permission</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge" style="background:#dbeafe;color:#1e40af;"><?php echo e($role->users_count); ?> utilisateur(s)</span></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Aucun rôle défini</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                
                <div class="tab-pane fade" id="tab-users">
                    <div class="table-responsive mt-2">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Rôle</th>
                                    <th>Type</th>
                                    <th>Statut</th>
                                    <th>Créé le</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="hist-avatar-sm"><?php echo e(substr($user->name, 0, 1)); ?></span>
                                            <strong><?php echo e($user->name); ?></strong>
                                        </div>
                                    </td>
                                    <td class="text-muted"><?php echo e($user->email); ?></td>
                                    <td>
                                        <?php if($user->role): ?><span class="badge" style="background:#ede9fe;color:#7c3aed;"><?php echo e($user->role->nom); ?></span>
                                        <?php else: ?><span class="text-muted">—</span><?php endif; ?>
                                    </td>
                                    <td><span class="badge bg-light text-dark"><?php echo e($user->type_compte ?? 'standard'); ?></span></td>
                                    <td>
                                        <?php if($user->is_active): ?><span class="badge bg-success">Actif</span>
                                        <?php else: ?><span class="badge bg-secondary">Inactif</span><?php endif; ?>
                                    </td>
                                    <td class="text-muted small"><?php echo e($user->created_at ?
                                        $user->created_at->format('d/m/Y') : '—'); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Aucun utilisateur</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if($admins->count() > 0): ?>
                    <h6 class="fw-bold mt-4 mb-3"><i
                            class="bi bi-person-workspace me-2 text-warning"></i>Administrateurs Entreprise</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Rôle</th>
                                    <th>Créé le</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $admins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2"><span class="hist-avatar-sm"
                                                style="background:#84cc16;"><?php echo e(substr($admin->name, 0, 1)); ?></span><strong><?php echo e($admin->name); ?></strong></div>
                                    </td>
                                    <td class="text-muted"><?php echo e($admin->email); ?></td>
                                    <td><?php if($admin->role): ?><span class="badge"
                                            style="background:#fef3c7;color:#92400e;"><?php echo e($admin->role->nom); ?></span><?php else: ?><span class="text-muted">—</span><?php endif; ?></td>
                                    <td class="text-muted small"><?php echo e($admin->created_at ?
                                        $admin->created_at->format('d/m/Y') : '—'); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>

                
                <div class="tab-pane fade" id="tab-equipes">
                    <?php $__empty_1 = true; $__currentLoopData = $equipes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $equipe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="hist-card hist-card-sm">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                            <div>
                                <strong><i class="bi bi-people-fill me-2 text-primary"></i><?php echo e($equipe->nom); ?></strong>
                                <span class="text-muted small ms-2">— Projet : <?php echo e($equipe->projet->nom ?? 'N/A'); ?></span>
                            </div>
                            <span class="badge bg-light text-dark"><?php echo e($equipe->users->count()); ?> membre(s)</span>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <?php $__empty_2 = true; $__currentLoopData = $equipe->users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                            <div class="hist-user-chip">
                                <span class="hist-avatar-sm"><?php echo e(substr($m->name, 0, 1)); ?></span>
                                <div>
                                    <div class="fw-semibold" style="font-size:0.82rem;"><?php echo e($m->name); ?></div>
                                    <?php if($m->role): ?><div class="text-muted" style="font-size:0.72rem;"><?php echo e($m->role->nom); ?>

                                    </div><?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                            <span class="text-muted small">Aucun membre dans cette équipe</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="hist-empty"><i class="bi bi-people"></i>
                        <p>Aucune équipe pour <?php echo e($annee); ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                
                <div class="tab-pane fade" id="tab-partenaires">
                    <div class="table-responsive mt-2">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nom complet</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                    <th>Projet</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $partenaires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partenaire): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="hist-avatar-sm" style="background:#009A44;"><?php echo e(substr($partenaire->nom, 0, 1)); ?></span>
                                            <strong><?php echo e($partenaire->nom); ?> <?php echo e($partenaire->prenom ?? ''); ?></strong>
                                        </div>
                                    </td>
                                    <td class="text-muted"><?php echo e($partenaire->email ?? '—'); ?></td>
                                    <td class="text-muted"><?php echo e($partenaire->telephone ?? '—'); ?></td>
                                    <td class="text-muted small">
                                        <?php $projetPartenaire = $projets->find($partenaire->projet_id); ?>
                                        <?php echo e($projetPartenaire->nom ?? 'N/A'); ?>

                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Aucun partenaire pour les projets de
                                        <?php echo e($annee); ?></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                
                <div class="tab-pane fade" id="tab-documents">
                    <div class="table-responsive mt-2">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nom du fichier</th>
                                    <th>Projet</th>
                                    <th>Type</th>
                                    <th>Taille</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-file-earmark-text text-primary fs-5"></i>
                                            <strong><?php echo e($doc->nom_fichier ?? $doc->titre ?? 'Document #' . $doc->id); ?></strong>
                                        </div>
                                    </td>
                                    <td class="text-muted small">
                                        <?php $projetDoc = $projets->find($doc->projet_id); ?>
                                        <?php echo e($projetDoc->nom ?? 'N/A'); ?>

                                    </td>
                                    <td><span class="badge bg-light text-dark"><?php echo e($doc->type ?? 'Général'); ?></span></td>
                                    <td class="text-muted small"><?php echo e($doc->taille ? round($doc->taille / 1024, 2) . ' KB'
                                        : '—'); ?></td>
                                    <td class="text-muted small"><?php echo e($doc->created_at ? $doc->created_at->format('d/m/Y')
                                        : '—'); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Aucun document pour <?php echo e($annee); ?>

                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                
                <div class="tab-pane fade" id="tab-contrats">
                    <div class="table-responsive mt-2">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Référence</th>
                                    <th>Projet</th>
                                    <th>Partie prenante</th>
                                    <th>Montant</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $contrats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contrat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><strong><?php echo e($contrat->reference ?? $contrat->titre ?? 'Contrat #' . $contrat->id); ?></strong></td>
                                    <td class="text-muted small">
                                        <?php $projetContrat = $projets->find($contrat->projet_id); ?>
                                        <?php echo e($projetContrat->nom ?? 'N/A'); ?>

                                    </td>
                                    <td><?php echo e($contrat->partie_prenante ?? '—'); ?></td>
                                    <td><strong><?php echo e(number_format($contrat->montant ?? 0, 0, ',', ' ')); ?> FCFA</strong>
                                    </td>
                                    <td>
                                        <?php $cs =
                                        ['actif'=>['bg-success','Actif'],'termine'=>['bg-dark','Terminé'],'annule'=>['bg-danger','Annulé']][$contrat->statut
                                        ?? ''] ?? ['bg-secondary', ucfirst($contrat->statut ?? 'N/A')]; ?>
                                        <span class="badge <?php echo e($cs[0]); ?>"><?php echo e($cs[1]); ?></span>
                                    </td>
                                    <td class="text-muted small"><?php echo e($contrat->date_signature ?
                                        \Carbon\Carbon::parse($contrat->date_signature)->format('d/m/Y') : '—'); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Aucun contrat pour <?php echo e($annee); ?>

                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                
                <div class="tab-pane fade" id="tab-factures">
                    <div class="table-responsive mt-2">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Numéro</th>
                                    <th>Partenaire / Fournisseur</th>
                                    <th>Montant TTC</th>
                                    <th>Statut</th>
                                    <th>Date Émission</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $factures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $facture): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><strong><?php echo e($facture->numero_facture ?? 'Facture #' . $facture->id); ?></strong>
                                    </td>
                                    <td><?php echo e($facture->partenaire->nom ?? $facture->fournisseur->nom ?? '—'); ?></td>
                                    <td><strong><?php echo e(number_format($facture->montant_ttc ?? 0, 0, ',', ' ')); ?>

                                            FCFA</strong></td>
                                    <td>
                                        <?php $fs = ['payee'=>['bg-success','Payée'],'en_attente'=>['bg-warning
                                        text-dark','En
                                        attente'],'annulee'=>['bg-danger','Annulée']][$facture->statut_paiement ?? '']
                                        ?? ['bg-secondary', ucfirst($facture->statut_paiement ?? 'N/A')]; ?>
                                        <span class="badge <?php echo e($fs[0]); ?>"><?php echo e($fs[1]); ?></span>
                                    </td>
                                    <td class="text-muted small"><?php echo e($facture->date_emission ?
                                        \Carbon\Carbon::parse($facture->date_emission)->format('d/m/Y') : '—'); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Aucune facture pour <?php echo e($annee); ?>

                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                
                <div class="tab-pane fade" id="tab-interventions">
                    <div class="table-responsive mt-2">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Titre</th>
                                    <th>Date</th>
                                    <th>Projet</th>
                                    <th>Intervenant</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $interventions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $intervention): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><strong><?php echo e($intervention->titre ?? 'Intervention #' . $intervention->id); ?></strong></td>
                                    <td class="text-muted small"><?php echo e($intervention->date_intervention ?
                                        \Carbon\Carbon::parse($intervention->date_intervention)->format('d/m/Y H:i') :
                                        '—'); ?></td>
                                    <td class="text-muted small">
                                        <?php $projetInt = $projets->find($intervention->projet_id); ?>
                                        <?php echo e($projetInt->nom ?? 'N/A'); ?>

                                    </td>
                                    <td><?php echo e($intervention->intervenant->name ?? $intervention->nom_intervenant ?? '—'); ?>

                                    </td>
                                    <td>
                                        <?php $is =
                                        ['terminee'=>['bg-success','Terminée'],'planifiee'=>['bg-primary','Planifiée'],'annulee'=>['bg-danger','Annulée']][$intervention->statut
                                        ?? ''] ?? ['bg-secondary', ucfirst($intervention->statut ?? 'N/A')]; ?>
                                        <span class="badge <?php echo e($is[0]); ?>"><?php echo e($is[1]); ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Aucune intervention pour <?php echo e($annee); ?></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                
                <div class="tab-pane fade" id="tab-soustraitances">
                    <div class="table-responsive mt-2">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Prestataire</th>
                                    <th>Projet</th>
                                    <th>Montant Contrat</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $sousTraitances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><strong><?php echo e($st->nom_entreprise ?? $st->nom_prestataire ?? 'Prestataire #' .
                                            $st->id); ?></strong></td>
                                    <td class="text-muted small">
                                        <?php $projetSt = $projets->find($st->projet_id); ?>
                                        <?php echo e($projetSt->nom ?? 'N/A'); ?>

                                    </td>
                                    <td><strong><?php echo e(number_format($st->montant_contrat ?? 0, 0, ',', ' ')); ?>

                                            FCFA</strong></td>
                                    <td>
                                        <?php $sts = ['en_cours'=>['bg-primary','En
                                        cours'],'termine'=>['bg-success','Terminé'],'suspendu'=>['bg-warning
                                        text-dark','Suspendu']][$st->statut ?? ''] ?? ['bg-secondary',
                                        ucfirst($st->statut ?? 'N/A')]; ?>
                                        <span class="badge <?php echo e($sts[0]); ?>"><?php echo e($sts[1]); ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Aucune sous-traitance pour <?php echo e($annee); ?></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                
                <div class="tab-pane fade" id="tab-fournisseurs">
                    <div class="table-responsive mt-2">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Fournisseur</th>
                                    <th>Catégorie</th>
                                    <th>Contact</th>
                                    <th>Téléphone</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $fournisseurs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fournisseur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="hist-avatar-sm" style="background:#be185d;"><?php echo e(substr($fournisseur->nom, 0, 1)); ?></span>
                                            <strong><?php echo e($fournisseur->nom); ?></strong>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark"><?php echo e($fournisseur->categorie ?? '—'); ?></span></td>
                                    <td><?php echo e($fournisseur->contact_prenom); ?> <?php echo e($fournisseur->contact_nom); ?></td>
                                    <td class="text-muted"><?php echo e($fournisseur->telephone ?? $fournisseur->contact_telephone
                                        ?? '—'); ?></td>
                                    <td>
                                        <?php if($fournisseur->statut === 'actif'): ?><span class="badge bg-success">Actif</span>
                                        <?php else: ?><span class="badge bg-secondary">Inactif</span><?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Aucun fournisseur pour <?php echo e($annee); ?></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                
                <div class="tab-pane fade" id="tab-stocks">
                    <div class="table-responsive mt-2">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nom / Référence</th>
                                    <th>Fournisseur</th>
                                    <th>Catégorie</th>
                                    <th>Quantité</th>
                                    <th>Prix U.</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $stocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stock): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <strong><?php echo e($stock->nom); ?></strong>
                                        <div class="text-muted small"><?php echo e($stock->reference ?? '—'); ?></div>
                                    </td>
                                    <td><?php echo e($stock->fournisseur->nom ?? '—'); ?></td>
                                    <td><span class="badge bg-light text-dark"><?php echo e($stock->categorie ?? '—'); ?></span>
                                    </td>
                                    <td><strong><?php echo e($stock->quantite); ?></strong></td>
                                    <td><?php echo e(number_format($stock->prix_unitaire ?? 0, 0, ',', ' ')); ?></td>
                                    <td><strong><?php echo e(number_format(($stock->quantite ?? 0) * ($stock->prix_unitaire ?? 0),
                                            0, ',', ' ')); ?> FCFA</strong></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Aucun stock pour <?php echo e($annee); ?>

                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                
                <div class="tab-pane fade" id="tab-rendezvous">
                    <div class="table-responsive mt-2">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Titre</th>
                                    <th>Projet</th>
                                    <th>Contact</th>
                                    <th>Date & Heure</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $rendezvous; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rdv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><strong><?php echo e($rdv->titre); ?></strong></td>
                                    <td>
                                        <?php if($rdv->projet): ?><div class="text-muted small"><i
                                                class="bi bi-briefcase me-1"></i><?php echo e($rdv->projet->nom); ?></div><?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($rdv->user): ?>
                                        <div class="d-flex align-items-center gap-1">
                                            <span class="hist-avatar-sm"><?php echo e(substr($rdv->user->name, 0, 1)); ?></span>
                                            <small><?php echo e($rdv->user->name); ?></small>
                                        </div>
                                        <?php else: ?>—<?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($rdv->date_heure): ?>
                                        <div class="fw-semibold"><i class="bi bi-calendar-event me-1"></i><?php echo e($rdv->date_heure->format('d/m/Y')); ?></div>
                                        <div class="text-muted small"><i class="bi bi-clock me-1"></i><?php echo e($rdv->date_heure->format('H:i')); ?> (<?php echo e($rdv->duree_minutes); ?> min)</div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php $rds =
                                        ['planifie'=>['bg-primary','Planifié'],'termine'=>['bg-success','Terminé'],'annule'=>['bg-danger','Annulé']][$rdv->statut
                                        ?? ''] ?? ['bg-secondary', ucfirst($rdv->statut ?? 'N/A')]; ?>
                                        <span class="badge <?php echo e($rds[0]); ?>"><?php echo e($rds[1]); ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Aucun rendez-vous pour <?php echo e($annee); ?></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                
                <div class="tab-pane fade" id="tab-satisfaction">
                    <div class="table-responsive mt-2">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Partenaire</th>
                                    <th>Projet associé</th>
                                    <th>Note</th>
                                    <th>Commentaire</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $satisfactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <?php if($sat->partenaire): ?>
                                        <strong><?php echo e($sat->partenaire->nom); ?> <?php echo e($sat->partenaire->prenom ?? ''); ?></strong>
                                        <?php else: ?>
                                        <span class="text-muted">Anonyme</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-muted small"><?php echo e($sat->projet->nom ?? '—'); ?></td>
                                    <td>
                                        <?php if($sat->note): ?>
                                        <div class="text-warning mb-1">
                                            <?php for($i=1; $i<=5; $i++): ?> <i
                                                class="bi bi-star-fill <?php echo e($i <= $sat->note ? '' : 'text-light'); ?>"></i>
                                                <?php endfor; ?>
                                        </div>
                                        <span
                                            class="badge <?php echo e($sat->note >= 4 ? 'bg-success' : ($sat->note == 3 ? 'bg-warning text-dark' : 'bg-danger')); ?>"><?php echo e($sat->getNoteLabel()); ?></span>
                                        <?php else: ?>—<?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($sat->commentaire): ?>
                                        <div class="text-muted small" style="max-width:300px; white-space:normal;">"<?php echo e(Str::limit($sat->commentaire, 100)); ?>"</div>
                                        <?php else: ?><span class="text-muted">—</span><?php endif; ?>
                                    </td>
                                    <td class="text-muted small"><?php echo e($sat->created_at ? $sat->created_at->format('d/m/Y')
                                        : '—'); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Aucune évaluation de
                                        satisfaction pour <?php echo e($annee); ?></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div><!-- .tab-content -->
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    /* Stats grid */
    .hist-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 0.75rem;
    }

    .hist-stat-card {
        border-radius: 12px;
        padding: 0.9rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.65rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .hist-stat-card i {
        font-size: 1.5rem;
        opacity: 0.85;
    }

    .hist-stat-card div {
        display: flex;
        flex-direction: column;
    }

    .hist-stat-card strong {
        font-size: 1.15rem;
        font-weight: 800;
        line-height: 1.2;
    }

    .hist-stat-card span {
        font-size: 0.72rem;
        font-weight: 500;
        opacity: 0.75;
    }

    .hist-stat-blue {
        background: #c8e6c9;
        color: #005a28;
    }

    .hist-stat-purple {
        background: #ede9fe;
        color: #5b21b6;
    }

    .hist-stat-orange {
        background: #c8e6c9;
        color: #9a3412;
    }

    .hist-stat-teal {
        background: #e8f5e9;
        color: #007a35;
    }

    .hist-stat-red {
        background: #fee2e2;
        color: #991b1b;
    }

    .hist-stat-indigo {
        background: #e0e7ff;
        color: #3730a3;
    }

    .hist-stat-emerald {
        background: #c8e6c9;
        color: #9a3412;
    }

    .hist-stat-cyan {
        background: #fffbeb;
        color: #b45309;
    }

    .hist-stat-yellow {
        background: #fef9c3;
        color: #854d0e;
    }

    /* Tabs */
    .hist-tabs {
        border-bottom: 2px solid #e5e7eb;
        gap: 0.15rem;
    }

    .hist-tabs .nav-link {
        border: none;
        border-radius: 8px 8px 0 0;
        padding: 0.5rem 0.85rem;
        color: #6b7280;
        font-weight: 500;
        font-size: 0.83rem;
        transition: all 0.2s;
        white-space: nowrap;
    }

    .hist-tabs .nav-link:hover {
        background: #f3f4f6;
        color: #111;
    }

    .hist-tabs .nav-link.active {
        background: #fff;
        color: #009A44;
        border-bottom: 2px solid #009A44;
        font-weight: 700;
    }

    .hist-tab-content {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-top: none;
        border-radius: 0 0 12px 12px;
        padding: 1.25rem;
    }

    /* Cards */
    .hist-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 1.1rem 1.25rem;
        margin-bottom: 0.85rem;
        background: #fff;
        transition: box-shadow 0.2s;
    }

    .hist-card:hover {
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
    }

    .hist-card-sm {
        padding: 0.85rem 1rem;
    }

    .hist-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .hist-card-icon-title {
        display: flex;
        align-items: flex-start;
        gap: 0.65rem;
    }

    .hist-card-icon {
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, #009A44, #ef4444);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .hist-card-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem 1.25rem;
        margin-top: 0.65rem;
        font-size: 0.82rem;
        color: #374151;
    }

    .hist-card-footer-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem 1.25rem;
        margin-top: 0.65rem;
        padding-top: 0.65rem;
        border-top: 1px solid #f3f4f6;
        font-size: 0.8rem;
        color: #374151;
    }

    /* Avatars */
    .hist-avatar-sm {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: #009A44;
        color: #fff;
        font-size: 0.68rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .hist-user-chip {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 0.3rem 0.6rem;
    }

    /* Budget cards */
    .hist-budget-summary {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
        gap: 0.85rem;
    }

    .hist-budget-card {
        border-radius: 12px;
        padding: 1rem 1.1rem;
        display: flex;
        align-items: center;
        gap: 0.65rem;
    }

    .hist-budget-card i {
        font-size: 1.6rem;
        opacity: 0.85;
    }

    .hist-budget-card div {
        display: flex;
        flex-direction: column;
    }

    .hist-budget-card strong {
        font-size: 0.95rem;
        font-weight: 800;
    }

    .hist-budget-card span {
        font-size: 0.72rem;
        opacity: 0.75;
    }

    .hist-budget-total {
        background: #d1fae5;
        color: #065f46;
    }

    .hist-budget-consomme {
        background: #fee2e2;
        color: #991b1b;
    }

    .hist-budget-restant {
        background: #dbeafe;
        color: #1e40af;
    }

    .hist-budget-factures {
        background: #fef9c3;
        color: #854d0e;
    }

    /* Empty state */
    .hist-empty {
        text-align: center;
        padding: 3rem 1rem;
        color: #9ca3af;
    }

    .hist-empty i {
        font-size: 2.5rem;
        display: block;
        margin-bottom: 0.75rem;
    }

    .hist-empty p {
        font-size: 0.9rem;
    }

    /* Color helpers */
    .text-purple {
        color: #7c3aed !important;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
        const activeTab = document.querySelector('.tab-pane.active.show');
        if (!activeTab) {
            alert("Aucun onglet actif trouvé.");
            return;
        }
        const table = activeTab.querySelector('table');
        if (!table) {
            alert("Aucune donnée à exporter dans cet onglet.");
            return;
        }

        const tempId = 'tempExportTable_' + Date.now();
        const originalId = table.id;
        table.id = tempId;

        let filename = 'historique_<?php echo e($annee); ?>';
        const tabId = activeTab.id;
        if (tabId) {
            filename += '_' + tabId.replace('tab-', '').replace('pills-', '');
        }


        if (originalId) {
            table.id = originalId;
        } else {
            table.removeAttribute('id');
        }
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/super-admin/historique/show.blade.php ENDPATH**/ ?>