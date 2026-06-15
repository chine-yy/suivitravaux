<?php $__env->startSection('title', 'Tableau de Bord Partenaire'); ?>

<?php $__env->startSection('content'); ?>

<div class="cp-dashboard">
    <div class="cp-content">

        <!-- Page Header -->
        <div class="cp-page-header d-flex justify-content-between align-items-center">
            <div>
                <h1 class="cp-page-title">Tableau de Bord</h1>
                <p class="cp-page-subtitle">Bienvenue sur votre espace de suivi, voici l'état de votre projet.</p>
            </div>
            <div class="d-flex gap-2 flex-wrap align-items-center">
                <span class="badge px-3 py-2 bg-primary fs-6" style="border-radius: 8px;"><?php echo e($projet->nom); ?></span>
                <span class="badge px-3 py-2 bg-primary fs-6" style="border-radius: 8px;">Statut: <?php echo e(ucfirst($projet->statut)); ?></span>
                <a href="<?php echo e(route('partenaire.equipe')); ?>" class="btn btn-sm px-4 py-2" style="background: var(--cp-primary); color: white; border-radius: 8px; font-weight: 500;">
                    Voir détails
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="cp-stats-grid">
            <!-- Project Advancement -->
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-primary">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e($projet->avancement); ?>%</div>
                    <div class="cp-stat-label">Avancement Total</div>
                </div>
            </div>

            <!-- Personnel Working -->
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-primary">
                    <i class="bi bi-people"></i>
                </div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e($totalPersonnes); ?></div>
                    <div class="cp-stat-label">Personnes sur le projet</div>
                </div>
            </div>

            <!-- Total Partenaires -->
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-success">
                    <i class="bi bi-person-workspace"></i>
                </div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e($totalPartenaires); ?></div>
                    <div class="cp-stat-label">Partenaires rattachés</div>
                </div>
            </div>

            <!-- Total Budget -->
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-warning">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e(number_format($projet->budget, 0, ',', ' ')); ?> FCFA</div>
                    <div class="cp-stat-label">Budget Alloué</div>
                </div>
            </div>
        </div>

        <!-- Main Content Row: Chart, Reports, Team -->
        <div class="row mt-4">
            <!-- Advancement Charts -->
            <div class="col-lg-5">
                <div class="cp-card-elevated h-100">
                    <div class="cp-card-header">
                        <h5 class="mb-0">Avancement des Phases</h5>
                    </div>
                    <div class="cp-card-body">
                        <canvas id="phasesChart" style="max-height: 480px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Latest Reports -->
            <div class="col-lg-4">
                <div class="cp-card-elevated h-100">
                    <div class="cp-card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Derniers Rapports Reçus</h5>
                        <a href="<?php echo e(route('partenaire.rapports')); ?>" class="btn btn-sm px-3" style="background: var(--cp-primary); color: white; border-radius: 8px; font-weight: 600;">Tout voir</a>
                    </div>
                    <div class="cp-card-body p-0">
                        <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0">
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $partenaire_rapports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rap): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td class="ps-3">
                                                <div class="fw-bold small"><?php echo e($rap->titre); ?></div>
                                                <div class="text-muted extra-small"><?php echo e($rap->created_at->format('d/m/Y')); ?></div>
                                            </td>
                                            <td class="pe-3" style="width: 80px;">
                                                <span class="badge bg-light text-dark border"><?php echo e($rap->getTypeLabel()); ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="2" class="text-center py-4 text-muted">Aucun rapport reçu.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project Team Info -->
            <div class="col-lg-3">
                <div class="cp-card-elevated h-100">
                    <div class="cp-card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Mon Équipe</h5>
                        <a href="<?php echo e(route('partenaire.equipe')); ?>" class="btn btn-sm px-3" style="background: var(--cp-primary); color: white; border-radius: 8px; font-weight: 600;">Détails</a>
                    </div>
                    <div class="cp-card-body">
                        <div class="d-flex flex-column gap-3">
                            <?php if($projet->admin): ?>
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <div>
                                    <div class="fw-bold small"><?php echo e($projet->admin->prenom); ?> <?php echo e($projet->admin->nom); ?></div>
                                    <div class="text-muted extra-small">Chef de Projet</div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="mt-2 pt-2 border-top">
                                <span class="text-muted small">+ <?php echo e($projet->membresCount()); ?> membres</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Satisfaction Section -->
        <div class="row mt-4 mb-5">
            <div class="col-12">
                <div class="cp-card-elevated">
                    <div class="cp-card-header">
                        <h5 class="mb-0">Votre Satisfaction</h5>
                    </div>
                    <div class="cp-card-body">
                        <?php if($existingSatisfaction): ?>
                            <div class="text-center py-4">
                                <div class="mb-3">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                        <i class="bi <?php echo e($i <= $existingSatisfaction->note ? 'bi-star-fill text-warning' : 'bi-star text-muted'); ?> fs-2"></i>
                                    <?php endfor; ?>
                                </div>
                                <h6 class="fw-bold">Merci pour votre retour !</h6>
                                <p class="text-muted small mb-4"><?php echo e($existingSatisfaction->commentaire ?? 'Vous nous avez donné une note de ' . $existingSatisfaction->note . '/5.'); ?></p>

                                <!-- Boutons d'action -->
                                <div class="d-flex justify-content-center gap-2 flex-wrap">
                                    <!-- Bouton Modifier -->
                                    <button type="button" class="btn btn-sm btn-outline-primary px-3"
                                        data-bs-toggle="modal" data-bs-target="#modalModifierSatisfaction">
                                        <i class="bi bi-pencil me-1"></i> Modifier
                                    </button>

                                    <!-- Bouton Supprimer -->
                                    <form action="<?php echo e(route('partenaire.satisfaction.destroy', $existingSatisfaction->id)); ?>" method="POST"
                                        onsubmit="return confirm('Voulez-vous vraiment supprimer votre avis ?')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger px-3">
                                            <i class="bi bi-trash me-1"></i> Supprimer
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Modal Modifier Satisfaction -->
                            <div class="modal fade" id="modalModifierSatisfaction" tabindex="-1" aria-labelledby="modalModifierLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form action="<?php echo e(route('partenaire.satisfaction.update', $existingSatisfaction->id)); ?>" method="POST">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="modal-title fw-bold" id="modalModifierLabel">
                                                    <i class="bi bi-star-half text-warning me-2"></i>Modifier votre avis
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body pt-2">
                                                <!-- Étoiles interactives -->
                                                <div class="text-center mb-4">
                                                    <label class="form-label small fw-bold text-uppercase text-muted d-block mb-3">Votre note</label>
                                                    <div class="modal-star-rating d-flex justify-content-center gap-2 fs-2" id="modalStarRating">
                                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                                            <i class="bi <?php echo e($i <= $existingSatisfaction->note ? 'bi-star-fill text-warning' : 'bi-star text-muted'); ?> modal-star"
                                                               data-value="<?php echo e($i); ?>" style="cursor:pointer; transition: transform 0.1s;"
                                                               onmouseover="hoverModalStars(<?php echo e($i); ?>)"
                                                               onmouseout="resetModalStars()"
                                                               onclick="selectModalStar(<?php echo e($i); ?>)"></i>
                                                        <?php endfor; ?>
                                                    </div>
                                                    <input type="hidden" name="note" id="modalNoteInput" value="<?php echo e($existingSatisfaction->note); ?>" required>
                                                    <div class="mt-2 text-muted small" id="modalNoteLabel">Note actuelle : <?php echo e($existingSatisfaction->note); ?>/5</div>
                                                </div>

                                                <!-- Commentaire -->
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Commentaire (optionnel)</label>
                                                    <textarea name="commentaire" class="form-control" rows="3"
                                                        placeholder="Partagez votre expérience..."><?php echo e($existingSatisfaction->commentaire); ?></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 pt-0">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                                                <button type="submit" class="btn btn-primary px-4" style="background: var(--cp-primary); border: none;">
                                                    <i class="bi bi-check2 me-1"></i> Enregistrer
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <form action="<?php echo e(route('partenaire.satisfaction.store')); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <div class="row align-items-center">
                                    <div class="col-md-4 text-center border-end">
                                        <div class="mb-2 fw-bold">Notez notre prestation</div>
                                        <div class="star-rating fs-2 d-flex justify-content-center gap-2">
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                                <input type="radio" id="star<?php echo e($i); ?>" name="note" value="<?php echo e($i); ?>" class="d-none" required>
                                                <label for="star<?php echo e($i); ?>" class="bi bi-star cursor-pointer text-muted" style="cursor: pointer;"></label>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Un commentaire ? (Optionnel)</label>
                                        <textarea name="commentaire" class="form-control" rows="2" placeholder="Partagez votre expérience..."></textarea>
                                    </div>
                                    <div class="col-md-2 mt-3 mt-md-0 d-grid">
                                        <button type="submit" class="btn btn-primary fw-bold" style="background: var(--cp-primary); border: none;">Envoyer</button>
                                    </div>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
// ---- Modal Star Rating ----
let modalSelectedNote = <?php echo e($existingSatisfaction ? $existingSatisfaction->note : 0); ?>;

function hoverModalStars(value) {
    document.querySelectorAll('.modal-star').forEach((star, i) => {
        if (i < value) {
            star.classList.replace('bi-star', 'bi-star-fill');
            star.classList.add('text-warning');
            star.classList.remove('text-muted');
        } else {
            star.classList.replace('bi-star-fill', 'bi-star');
            star.classList.remove('text-warning');
            star.classList.add('text-muted');
        }
    });
}

function resetModalStars() {
    hoverModalStars(modalSelectedNote);
}

function selectModalStar(value) {
    modalSelectedNote = value;
    document.getElementById('modalNoteInput').value = value;
    document.getElementById('modalNoteLabel').textContent = 'Note sélectionnée : ' + value + '/5';
    hoverModalStars(value);
}
// ---- End Modal Star Rating ----

document.addEventListener('DOMContentLoaded', function() {
    // Star rating logic
    const stars = document.querySelectorAll('.star-rating label');
    const inputs = document.querySelectorAll('.star-rating input');

    stars.forEach((star, index) => {
        star.addEventListener('mouseover', () => {
            highlightStars(index + 1);
        });

        star.addEventListener('mouseout', () => {
            const checkedInput = document.querySelector('.star-rating input:checked');
            if (checkedInput) {
                highlightStars(parseInt(checkedInput.value));
            } else {
                highlightStars(0);
            }
        });

        star.addEventListener('click', () => {
            const rating = index + 1;
            document.getElementById('star' + rating).checked = true;
            highlightStars(rating);
        });
    });

    function highlightStars(count) {
        stars.forEach((star, i) => {
            if (i < count) {
                star.classList.replace('bi-star', 'bi-star-fill');
                star.classList.add('text-warning');
                star.classList.remove('text-muted');
            } else {
                star.classList.replace('bi-star-fill', 'bi-star');
                star.classList.remove('text-warning');
                star.classList.add('text-muted');
            }
        });
    }

    // Chart logic
    const ctx = document.getElementById('phasesChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($labels, 15, 512) ?>,
            datasets: [{
                label: 'Avancement (%)',
                data: <?php echo json_encode($data, 15, 512) ?>,
                backgroundColor: 'rgba(247, 148, 29, 0.7)',
                borderColor: 'rgba(247, 148, 29, 1)',
                borderWidth: 1,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.partenaire', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux/resources/views/partenaire/dashboard.blade.php ENDPATH**/ ?>