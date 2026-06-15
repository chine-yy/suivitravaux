<?php $__env->startSection('title', 'Rapports et Analytique - Super Admin'); ?>

<?php $__env->startSection('breadcrumb'); ?>
<span class="text-muted">Rapports</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-rapports">
    <div class="cp-content">
        <!-- Page Header -->
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title">Rapports</h1>
                <p class="cp-page-subtitle">Vue d'ensemble et statistiques des rapports et le bouton envoyer permet l'envoi du rapport au partenaire(s) rattacher au projet</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-danger" onclick="exportToPdf('rapportsTable', 'Liste des rapports', 'rapports_export')">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Exporter tout
                </button>
            </div>
        </div>


        <!-- Statistics Cards -->
        <div class="cp-stats-grid mb-4">
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-info"><i class="bi bi-file-earmark-text"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e($rapports->total()); ?></div>
                    <div class="cp-stat-label">Total Rapports</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-green"><i class="bi bi-hourglass-split"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e($rapportsParStatut['en_attente']); ?></div>
                    <div class="cp-stat-label">En attente</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-success"><i class="bi bi-check-circle"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e($rapportsParStatut['valide']); ?></div>
                    <div class="cp-stat-label">Validés</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-danger"><i class="bi bi-x-circle"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e($rapportsParStatut['rejete']); ?></div>
                    <div class="cp-stat-label">Rejetés</div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row mb-4">
            <!-- Monthly chart -->
            <div class="col-xl-8 mb-4">
                <div class="cp-chart-card h-100">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-bar-chart me-2"></i>Rapports par mois</h6>
                    </div>
                    <div class="cp-chart-body">
                        <canvas id="rapportsParMoisChart" height="300"></canvas>
                    </div>
                </div>
            </div>
            <!-- Status chart -->
            <div class="col-xl-4 mb-4">
                <div class="cp-chart-card h-100">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-pie-chart me-2"></i>Statuts des rapports</h6>
                    </div>
                    <div class="cp-chart-body">
                        <canvas id="rapportsParStatutChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="cp-chart-card mb-4">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-funnel me-2"></i>Filtres</h6>
            </div>
            <div class="p-4">
                <form action="<?php echo e(route('super-admin.rapports.index')); ?>" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Projet</label>
                        <select name="projet_id" class="form-select form-select-sm">
                            <option value="">Tous les projets</option>
                            <?php $__currentLoopData = $projets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $projet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($projet->id); ?>" <?php echo e(request('projet_id') == $projet->id ? 'selected' : ''); ?>><?php echo e($projet->nom); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Statut</label>
                        <select name="statut" class="form-select form-select-sm">
                            <option value="">Tous les statuts</option>
                            <option value="en_attente" <?php echo e(request('statut') == 'en_attente' ? 'selected' : ''); ?>>En attente</option>
                            <option value="valide" <?php echo e(request('statut') == 'valide' ? 'selected' : ''); ?>>Validé</option>
                            <option value="rejete" <?php echo e(request('statut') == 'rejete' ? 'selected' : ''); ?>>Rejeté</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Type</label>
                        <select name="type" class="form-select form-select-sm">
                            <option value="">Tous les types</option>
                            <option value="journalier" <?php echo e(request('type') == 'journalier' ? 'selected' : ''); ?>>Journalier</option>
                            <option value="hebdomadaire" <?php echo e(request('type') == 'hebdomadaire' ? 'selected' : ''); ?>>Hebdomadaire</option>
                            <option value="mensuel" <?php echo e(request('type') == 'mensuel' ? 'selected' : ''); ?>>Mensuel</option>
                            <option value="incident" <?php echo e(request('type') == 'incident' ? 'selected' : ''); ?>>Incident</option>
                            <option value="fin_tache" <?php echo e(request('type') == 'fin_tache' ? 'selected' : ''); ?>>Fin de Tâche</option>
                            <option value="sous_tache" <?php echo e(request('type') == 'sous_tache' ? 'selected' : ''); ?>>Sous-tâche</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Titre</label>
                        <input type="text" name="titre" class="form-control form-control-sm" placeholder="Rechercher..." value="<?php echo e(request('titre')); ?>">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="bi bi-search me-1"></i> Filtrer
                        </button>
                        <a href="<?php echo e(route('super-admin.rapports.index')); ?>" class="btn btn-sm btn-outline-secondary ms-2">
                            <i class="bi bi-x-circle me-1"></i> Réinitialiser
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Rapports Table -->
        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-table me-2"></i>Liste des rapports</h6>
            </div>
            <div class="table-responsive p-3">
                <table class="table table-hover align-middle" id="rapportsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Titre</th>
                            <th>Projet</th>
                            <th>Auteur</th>
                            <th>Type</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $rapports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rapport): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <div class="fw-semibold"><?php echo e($rapport->created_at->format('d/m/Y')); ?></div>
                                <small class="text-muted"><?php echo e($rapport->created_at->format('H:i')); ?></small>
                            </td>
                            <td>
                                <div class="fw-semibold"><?php echo e($rapport->titre); ?></div>
                                <small class="text-muted"><?php echo e(Str::limit($rapport->contenu, 30)); ?></small>
                            </td>
                            <td>
                                <span class="badge bg-secondary"><?php echo e($rapport->projet->nom ?? 'N/A'); ?></span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-2"><?php echo e(strtoupper(substr($rapport->auteur->name ?? 'U', 0, 1))); ?></div>
                                    <?php echo e($rapport->auteur->name ?? 'N/A'); ?>

                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info"><?php echo e(ucfirst(str_replace('_', ' ', $rapport->type))); ?></span>
                            </td>
                            <td>
                                <?php if($rapport->statut === 'valide'): ?>
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Validé</span>
                                <?php elseif($rapport->statut === 'rejete'): ?>
                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Rejeté</span>
                                <?php else: ?>
                                <span class="badge bg-warning"><i class="bi bi-hourglass-split me-1"></i>En attente</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-info" title="Voir" data-bs-toggle="modal" data-bs-target="#viewRapportModal<?php echo e($rapport->id); ?>">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-warning" title="Modifier le statut" data-bs-toggle="modal" data-bs-target="#statusOnlyModal<?php echo e($rapport->id); ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <?php echo $__env->make('partials.row-export', ['id' => $rapport->id, 'prefix' => 'rapport', 'title' => 'Rapport - ' . ($rapport->titre ?? $rapport->id)], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                    <?php if(!$rapport->est_envoye): ?>
                                    <form action="<?php echo e(route('super-admin.rapports.envoyer-partenaire', $rapport->id)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-outline-success" title="Envoyer au partenaire" onclick="return confirm('Envoyer ce rapport au partenaire ?')">
                                            <i class="bi bi-send"></i>
                                        </button>
                                    </form>
                                    <?php else: ?>
                                    <span class="btn btn-outline-secondary btn-sm" title="Déjà envoyé au partenaire">
                                        <i class="bi bi-send-check-fill text-success"></i>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                Aucun rapport trouvé
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php $__currentLoopData = $rapports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rapport): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $currentStatut = $rapport->statut;
                $approuveStatuts = ['valide', 'approuve'];
                $soumisStatuts = ['soumis', 'en_revision', 'en_revue'];
            ?>

            <div class="modal fade js-rapport-modal" id="statusOnlyModal<?php echo e($rapport->id); ?>" tabindex="-1" aria-labelledby="statusOnlyModalLabel<?php echo e($rapport->id); ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-md">
                    <div class="modal-content">
                        <form action="<?php echo e(route('super-admin.rapports.update-statut', $rapport->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <div class="modal-header py-3">
                                <h5 class="modal-title fw-semibold" id="statusOnlyModalLabel<?php echo e($rapport->id); ?>">
                                    Modifier le statut du rapport #<?php echo e($rapport->id); ?>

                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                            </div>
                            <div class="modal-body py-3">
                                <p class="text-muted mb-3">Vous pouvez modifier uniquement le statut de ce rapport.</p>
                                <label for="statut_<?php echo e($rapport->id); ?>" class="form-label fw-semibold">Statut du rapport <span class="text-danger">*</span></label>
                                <select name="statut" id="statut_<?php echo e($rapport->id); ?>" class="form-select" required>
                                    <option value="soumis" <?php echo e(in_array($currentStatut, $soumisStatuts) ? 'selected' : ''); ?>>Soumis / En révision</option>
                                    <option value="valide" <?php echo e(in_array($currentStatut, $approuveStatuts) ? 'selected' : ''); ?>>Validé</option>
                                    <option value="rejete" <?php echo e($currentStatut == 'rejete' ? 'selected' : ''); ?>>Rejeté</option>
                                    <option value="brouillon" <?php echo e($currentStatut == 'brouillon' ? 'selected' : ''); ?>>Brouillon</option>
                                </select>
                            </div>
                            <div class="modal-footer py-3 d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-green px-4">
                                    <i class="bi bi-check-circle me-2"></i>Enregistrer le statut
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade js-rapport-modal" id="viewRapportModal<?php echo e($rapport->id); ?>" tabindex="-1" aria-labelledby="viewRapportModalLabel<?php echo e($rapport->id); ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
                    <div class="modal-content">
                        <div class="modal-header py-3">
                            <h5 class="modal-title fw-semibold" id="viewRapportModalLabel<?php echo e($rapport->id); ?>">
                                <i class="bi bi-file-earmark-text me-2"></i><?php echo e($rapport->titre); ?>

                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                        </div>
                        <div class="modal-body py-3">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <div class="p-3 border rounded bg-light h-100">
                                        <h6 class="fw-bold mb-3"><i class="bi bi-file-earmark-text me-2"></i>Détails du rapport</h6>
                                        <p class="mb-2"><strong>Titre :</strong> <?php echo e($rapport->titre ?? 'N/A'); ?></p>
                                        <p class="mb-2"><strong>Projet :</strong> <?php echo e($rapport->projet->nom ?? 'N/A'); ?></p>
                                        <p class="mb-2"><strong>Type :</strong> <?php echo e(ucfirst(str_replace('_', ' ', $rapport->type ?? 'N/A'))); ?></p>
                                        <p class="mb-2"><strong>Date de création :</strong> <?php echo e($rapport->created_at ? $rapport->created_at->format('d/m/Y H:i') : 'N/A'); ?></p>
                                        <p class="mb-0"><strong>Heure d'envoi :</strong> <?php echo e($rapport->date_envoi ? \Carbon\Carbon::parse($rapport->date_envoi)->format('d/m/Y H:i') : ($rapport->created_at ? $rapport->created_at->format('d/m/Y H:i') : 'N/A')); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 border rounded h-100">
                                        <h6 class="fw-bold mb-3"><i class="bi bi-person-badge me-2"></i>Détails de l'utilisateur</h6>
                                        <p class="mb-2"><strong>Prénom :</strong> <?php echo e($rapport->auteur->prenom ?? 'N/A'); ?></p>
                                        <p class="mb-2"><strong>Nom :</strong> <?php echo e($rapport->auteur->name ?? 'N/A'); ?></p>
                                        <p class="mb-2"><strong>Rôle :</strong> <?php echo e($rapport->auteur->role->nom ?? 'N/A'); ?></p>
                                        <p class="mb-2"><strong>Numéro :</strong> <?php echo e($rapport->auteur->phone ?? $rapport->auteur->telephone ?? 'N/A'); ?></p>
                                        <p class="mb-0"><strong>Email :</strong> <?php echo e($rapport->auteur->email ?? 'N/A'); ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <strong>Statut :</strong>
                                <?php if($rapport->statut === 'valide'): ?>
                                <span class="badge bg-success ms-2">Validé</span>
                                <?php elseif($rapport->statut === 'rejete'): ?>
                                <span class="badge bg-danger ms-2">Rejeté</span>
                                <?php else: ?>
                                <span class="badge bg-warning ms-2">En attente</span>
                                <?php endif; ?>
                            </div>

                            <?php if($rapport->contenu): ?>
                            <div>
                                <strong>Contenu :</strong>
                                <div class="bg-light p-3 rounded mt-2 border"><?php echo e($rapport->contenu); ?></div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="modal-footer py-3 d-flex justify-content-between flex-wrap gap-2">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                            <a href="<?php echo e(route('super-admin.export.pdf.direct', ['type' => 'rapport', 'id' => $rapport->id])); ?>" class="btn btn-primary">
                                <i class="bi bi-download me-2"></i>Télécharger PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <!-- Pagination -->
            <?php if($rapports->hasPages()): ?>
            <div class="card-footer bg-transparent border-0 d-flex justify-content-between align-items-center p-3">
                <div class="text-muted">
                    Affichage de <?php echo e($rapports->firstItem()); ?> à <?php echo e($rapports->lastItem()); ?> sur <?php echo e($rapports->total()); ?> résultats
                </div>
                <div>
                    <?php echo e($rapports->links()); ?>

                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/chart.min.js')); ?>"></script>
<script>
    // Ensure report modals are attached to <body> so they overlay the full viewport.
    document.addEventListener('show.bs.modal', function (event) {
        const modal = event.target;
        if (!modal.classList || !modal.classList.contains('js-rapport-modal')) {
            return;
        }
        if (modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    });

    // Monthly chart
    const rapportsParMois = <?php echo json_encode($rapportsParMois, 15, 512) ?>;
    const labels = rapportsParMois.map(r => `Mois ${r.mois}/${r.annee}`);
    const data = rapportsParMois.map(r => r.total);

    new Chart(document.getElementById('rapportsParMoisChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Rapports',
                data: data,
                borderColor: '#fd7e14',
                backgroundColor: 'rgba(253, 126, 20, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Auto-open modal if redirected from show route
    <?php if(session('open_modal')): ?>
    var modalToOpen = document.getElementById('<?php echo e(session('open_modal')); ?>');
    if (modalToOpen) {
        var modal = new bootstrap.Modal(modalToOpen);
        modal.show();
    }
    <?php endif; ?>

    // Status chart
    const statuts = <?php echo json_encode($rapportsParStatut, 15, 512) ?>;
    new Chart(document.getElementById('rapportsParStatutChart'), {
        type: 'doughnut',
        data: {
            labels: ['En attente', 'Validés', 'Rejetés'],
            datasets: [{
                data: [statuts.en_attente, statuts.valide, statuts.rejete],
                backgroundColor: ['#ffc107', '#198754', '#dc3545']
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
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/super-admin/rapports/index.blade.php ENDPATH**/ ?>