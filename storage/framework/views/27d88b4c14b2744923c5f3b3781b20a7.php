<?php $__env->startSection('title', 'Définir le Budget Annuel'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <a href="<?php echo e(route('super-admin.budget.index')); ?>" class="text-muted">Budgets</a>
    <span class="mx-2">/</span>
    <span>Définir le Budget</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-cash-stack me-2"></i>Définir le Budget Annuel</h1>
                <p class="cp-page-subtitle">Établir le budget global pour l'année <?php echo e($currentYear); ?></p>
            </div>
            <a href="<?php echo e(route('super-admin.budget.index')); ?>" class="btn btn-outline-secondary px-4">
                <i class="bi bi-arrow-left me-2"></i>Retour
            </a>
        </div>

        <?php echo $__env->make('partials.alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-info-circle me-2"></i>Informations du Budget</h6>
            </div>
            <div class="card-body p-4">
                <form action="<?php echo e(route('super-admin.budget.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="annee" value="<?php echo e($currentYear); ?>">

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Année</label>
                            <input type="text" class="form-control" value="<?php echo e($currentYear); ?>" disabled readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Budget Total (FCF) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="budget_total" class="form-control <?php $__errorArgs = ['budget_total'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('budget_total')); ?>" placeholder="Ex: 10000000" required autofocus>
                            <?php $__errorArgs = ['budget_total'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Description / Notes</label>
                            <textarea name="description" class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="4" placeholder="Notes sur la provenance ou l'allocation du budget..."><?php echo e(old('description')); ?></textarea>
                            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="<?php echo e(route('super-admin.budget.index')); ?>" class="btn btn-outline-secondary px-4">Annuler</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-circle me-2"></i>Enregistrer le Budget
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux/resources/views/super-admin/budget/create.blade.php ENDPATH**/ ?>