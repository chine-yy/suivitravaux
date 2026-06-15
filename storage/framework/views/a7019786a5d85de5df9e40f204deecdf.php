<!DOCTYPE html>
<html>
<head>
    <title>Réinitialisation de mot de passe</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px; }
        .header { background-color: #009A44; color: white; padding: 15px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { padding: 20px; }
        .footer { font-size: 0.8em; color: #718096; text-align: center; padding: 20px; }
        .credentials { background-color: #f7fafc; padding: 15px; border-radius: 6px; border: 1px dashed #cbd5e0; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?php echo e(config('app.name')); ?></h1>
        </div>
        <div class="content">
            <p>Bonjour <?php echo e($userName); ?>,</p>
            <p>Votre mot de passe pour la plateforme <?php echo e(config('app.name')); ?> a été réinitialisé par un administrateur.</p>
            
            <p>Voici vos nouveaux identifiants de connexion :</p>
            
            <div class="credentials">
                <strong>Email :</strong> <?php echo e($email); ?><br>
                <strong>Nouveau Mot de passe :</strong> <?php echo e($password); ?>

            </div>
            
            <p>Nous vous recommandons de changer ce mot de passe dès votre première connexion.</p>
            
            <p>Si vous n'êtes pas à l'origine de cette demande, veuillez contacter le support immédiatement.</p>
            
            <p>Cordialement,<br>L'équipe <?php echo e(config('app.name')); ?></p>
        </div>
        <div class="footer">
            &copy; <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?>. Tous droits réservés.
        </div>
    </div>
</body>
</html>
<?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/emails/password-reset.blade.php ENDPATH**/ ?>