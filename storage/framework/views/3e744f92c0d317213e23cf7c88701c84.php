<?php $__env->startSection('title', 'Gestion des Partenaires'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <span class="text-muted">Partenaires</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-people me-2"></i>Gestion des Partenaires</h1>
                <p class="cp-page-subtitle">Visualisez et gérez les comptes partenaires liés aux projets</p>
            </div>



            <div class="d-flex gap-2">
                  <?php if($has('exporter-pdf-partenaires')): ?>
                <?php echo $__env->make('partials.export-buttons', ['tableId' => 'partenairesTable', 'title' => 'Liste des partenaires', 'filename' => 'partenaires_export'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endif; ?>
                <?php if($has('create-partenaires')): ?>
                <button type="button" class="btn btn-primary px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#partenaireCountModal">
                    <i class="bi bi-person-plus me-2"></i>Inscrire Partenaire
                </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Modal pour le nombre de partenaires -->
        <div class="modal fade" id="partenaireCountModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header bg-gradient border-0 p-4" style="background: linear-gradient(135deg, #0d6efd 0%, #0d5fdd 100%);">
                        <h5 class="modal-title fw-bold text-white"><i class="bi bi-people-fill me-2"></i>Inscription Partenaire(s)</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="<?php echo e(route('role-dynamique.partenaires.create')); ?>" method="GET">
                        <div class="modal-body p-5 text-center">
                            <label class="form-label d-block mb-4 fw-bold fs-5">Combien de partenaires souhaitez-vous inscrire pour ce projet ?</label>
                            <div class="d-flex justify-content-center mb-3">
                                <input type="number" name="count" class="form-control form-control-lg text-center fw-bold" value="1" min="1" max="10" style="width: 120px; border-radius: 12px; border: 2px solid #0d6efd; font-size: 1.5rem;">
                            </div>
                            <p class="text-muted small"><i class="bi bi-info-circle me-1"></i>Maximum 10 partenaires par lot</p>
                        </div>
                        <div class="modal-footer border-top-0 p-4 gap-2">
                            <button type="button" class="btn btn-light px-5 py-2 fw-semibold" data-bs-dismiss="modal" style="border: 1px solid #dee2e6;">Annuler</button>
                            <button type="submit" class="btn btn-primary px-5 py-2 fw-semibold shadow-sm rounded-3">Continuer <i class="bi bi-arrow-right ms-2"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <style>
            .modal-backdrop {
                background-color: rgba(0, 0, 0, 0.7);
                backdrop-filter: blur(4px);
                z-index: 2040;
            }

            #partenaireCountModal {
                z-index: 2050;
            }

            #partenaireCountModal .modal-dialog {
                z-index: 2051;
            }

            #partenaireCountModal .modal-content {
                animation: modalSlideIn 0.4s ease-out;
            }

            @keyframes modalSlideIn {
                from {
                    opacity: 0;
                    transform: scale(0.95) translateY(-20px);
                }
                to {
                    opacity: 1;
                    transform: scale(1) translateY(0);
                }
            }
        </style>

        <?php $__env->startPush('scripts'); ?>
        <script>
            document.addEventListener('show.bs.modal', function (event) {
                const modal = event.target;
                if (!modal.id || !modal.id.includes('partenaireCountModal')) {
                    return;
                }
                if (modal.parentElement !== document.body) {
                    document.body.appendChild(modal);
                }
            });
        </script>
        <?php $__env->stopPush(); ?>


        <?php if(session('generated_password') || session('created_partenaires')): ?>
        <div class="alert alert-warning border-0 shadow-sm mb-4 p-4" id="partenairePasswordAlert" style="background-color: #fffbeb; border-left: 5px solid #009A44 !important;">
            <div class="d-flex align-items-start justify-content-between">
                <div class="w-100">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-shield-lock-fill me-2 text-green"></i>Mots de passe générés</h6>

                    <?php if(session('created_partenaires')): ?>
                        <div class="row g-3">
                            <?php $__currentLoopData = session('created_partenaires'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $createdPartenaire): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="p-3 bg-white rounded border border-warning shadow-sm">
                                    <div class="small text-muted mb-1"><?php echo e($createdPartenaire['name']); ?></div>
                                    <code class="fs-5 fw-bold text-green"><?php echo e($createdPartenaire['password']); ?></code>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="p-3 bg-white d-inline-block rounded border border-warning shadow-sm">
                            <code class="fs-5 fw-bold text-green"><?php echo e(session('generated_password')); ?></code>
                        </div>
                    <?php endif; ?>

                    <div class="mt-3 small text-muted">
                        <i class="bi bi-info-circle me-1"></i> Veuillez noter ces mots de passe. Ils ont également été envoyés par email aux partenaires.
                    </div>
                </div>
                <button class="btn-close ms-2" onclick="document.getElementById('partenairePasswordAlert').remove()"></button>
            </div>
        </div>
        <?php endif; ?>

        <?php
            $totalPartenaires = $partenaires->count();
        ?>

        <div class="cp-stats-grid mb-4">
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-primary"><i class="bi bi-people"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e($totalPartenaires); ?></div>
                    <div class="cp-stat-label">Total Partenaires</div>
                </div>
            </div>
        </div>

        <!-- Filtre -->
        <div class="cp-chart-card mb-4">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-filter me-2"></i>Filtres de recherche</h6>
            </div>
            <div class="p-4">
                <form action="<?php echo e(route('role-dynamique.partenaires.index')); ?>" method="GET" class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label small fw-bold">Nom / Prénom / Email</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Rechercher..." value="<?php echo e(request('search')); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Projet Associé</label>
                        <select name="projet_id" class="form-select form-select-sm">
                            <option value="">Tous les projets</option>
                            <?php $__currentLoopData = $projets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $projet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($projet->id); ?>" <?php echo e(request('projet_id') == $projet->id ? 'selected' : ''); ?>>
                                    <?php echo e($projet->nom); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="bi bi-search me-1"></i> Filtrer
                        </button>
                        <a href="<?php echo e(route('role-dynamique.partenaires.index')); ?>" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-list-ul me-2"></i>Liste des Partenaires</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="partenairesTable">
                    <thead>
                        <tr style="background: rgba(99,102,241,.08);">
                            <th>Partenaire</th>
                            <th>Contact</th>
                            <th>Projet Lié</th>
                            <th>Statut</th>
                            <th>Date d'Inscription</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $partenaires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partenaire): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold me-3" style="width:40px;height:40px;font-size:0.9rem;">
                                        <?php echo e(strtoupper(substr($partenaire->prenom ?? $partenaire->name, 0, 1))); ?><?php echo e(strtoupper(substr($partenaire->name ?? '', 0, 1))); ?>

                                    </div>
                                    <div>
                                        <div class="fw-semibold"><?php echo e($partenaire->prenom ?? ''); ?> <?php echo e($partenaire->name ?? ''); ?></div>
                                        <small class="text-muted"><?php echo e($partenaire->email); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div><i class="bi bi-telephone me-1 text-muted"></i><?php echo e($partenaire->telephone ?? 'N/A'); ?></div>
                            </td>
                            <td>
                                <?php
                                    $projetLie = \App\Models\Projet::where('partenaire_id', $partenaire->id)
                                        ->orWhereHas('partenaires', fn($q) => $q->where('user_id', $partenaire->id))
                                        ->first();
                                ?>
                                <span class="badge bg-light text-dark"><?php echo e($projetLie->nom ?? 'N/A'); ?></span>
                            </td>
                            <td>
                                <?php if($partenaire->is_active): ?>
                                    <span class="badge bg-success">Actif</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inactif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small class="text-muted"><?php echo e($partenaire->created_at->format('d/m/Y')); ?></small>
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="<?php echo e(route('role-dynamique.partenaires.show', $partenaire->id)); ?>" class="btn btn-sm btn-outline-info" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if($has('edit-partenaires')): ?>
                                    <a href="<?php echo e(route('role-dynamique.partenaires.edit', $partenaire->id)); ?>" class="btn btn-sm btn-outline-primary" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php if($has('reset-password-partenaires')): ?>
                                    <form action="<?php echo e(route('role-dynamique.partenaires.reset-password', $partenaire->id)); ?>"
                                        method="POST"
                                        onsubmit="return confirm('Réinitialiser le mot de passe de <?php echo e($partenaire->prenom ?? ''); ?> <?php echo e($partenaire->name ?? ''); ?> ?');"
                                        class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-primary" title="Réinitialiser mot de passe">
                                            <i class="bi bi-key"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    <?php if($has('delete-partenaires')): ?>
                                    <form action="<?php echo e(route('role-dynamique.partenaires.destroy', $partenaire->id)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer ce partenaire ?')" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-people display-4"></i>
                                <p class="mt-3">Aucun partenaire enregistré</p>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#partenaireCountModal">
                                    <i class="bi bi-person-plus me-2"></i>Inscrire Partenaire
                                </button>
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

<?php echo $__env->make('layouts.role-dynamique', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/role-dynamique/partenaires/index.blade.php ENDPATH**/ ?>