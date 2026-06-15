<?php $__env->startSection('title', 'Mon Équipe'); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-dashboard">
    <div class="cp-content">

        <!-- Page Header -->
        <div class="cp-page-header">
            <h1 class="cp-page-title">Mon Équipe</h1>
            <p class="cp-page-subtitle">Détails de l'équipe travaillant sur votre projet.</p>
        </div>

        <!-- Team Section -->
        <div class="cp-card-elevated mb-4">
            <div class="cp-card-header">
                <h5 class="mb-0">Membres de l'équipe</h5>
            </div>
            <div class="cp-card-body">
                <?php if($projet->admin): ?>
                <div class="d-flex align-items-center gap-3 mb-3">
<div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div>
                            <div class="fw-bold"><?php echo e($projet->admin->prenom); ?> <?php echo e($projet->admin->nom); ?></div>
                            <div class="text-muted small">Chef de Projet</div>
                        </div>
                </div>
                <?php endif; ?>

                <?php
                $equipes = $projet->equipes()->with('chef')->get();
                $chefEquipes = $equipes->whereNotNull('chef_equipe_id')->pluck('chef')->filter();
                $allMembers = $projet->membres()->get();
                $regularMembers = $allMembers->whereNotIn('id', $chefEquipes->pluck('id'));
                ?>

                <?php if($chefEquipes->count() > 0): ?>
                    <?php $__currentLoopData = $chefEquipes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chef): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div>
                            <div class="fw-bold"><?php echo e($chef->prenom); ?> <?php echo e($chef->nom); ?></div>
                            <div class="text-muted small">Chef d'équipe</div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>

                <?php if($regularMembers->count() > 0): ?>
                    <?php $__currentLoopData = $regularMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div>
                            <div class="fw-bold"><?php echo e($member->prenom); ?> <?php echo e($member->nom); ?></div>
                            <div class="text-muted small">Membre de l'équipe</div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php elseif($chefEquipes->count() == 0): ?>
                    <div class="text-muted small">Aucun membre dans l'équipe pour le moment.</div>
                <?php endif; ?>

                <div class="mt-3 pt-3 border-top">
                    <span class="text-muted small">
                        Total: <?php echo e($chefEquipes->count() + $regularMembers->count()); ?> personne(s) dans l'équipe
                    </span>
                </div>
            </div>
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.partenaire', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux/resources/views/partenaire/equipe/equipe.blade.php ENDPATH**/ ?>