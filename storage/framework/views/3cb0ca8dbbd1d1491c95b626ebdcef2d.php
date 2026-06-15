<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', config('app.name') . ' - Suivi Travaux'); ?></title>

    
    <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap.min.css')); ?>">
    
    <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap-icons.css')); ?>">

    
    <link rel="stylesheet" href="<?php echo e(asset('css/dashboard/dashboard.css')); ?>">

    
    <link rel="stylesheet" href="<?php echo e(asset('css/includes/header.css')); ?>">
    
    <link rel="stylesheet" href="<?php echo e(asset('css/includes/footer.css')); ?>">

    
    <link rel="stylesheet" href="<?php echo e(asset('css/theme-green.css')); ?>">

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>

    <?php echo $__env->make('includes.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main>

                 <br>
                 <br>
        <?php echo $__env->yieldContent('content'); ?>

        
    </main>

    <?php echo $__env->make('includes.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <script src="<?php echo e(asset('js/bootstrap.bundle.min.js')); ?>"></script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/layouts/app.blade.php ENDPATH**/ ?>