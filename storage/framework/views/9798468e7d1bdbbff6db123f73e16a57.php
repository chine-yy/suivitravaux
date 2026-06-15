<!DOCTYPE html>
<html>
<head>
    <title>Mot de passe modifié</title>
    <style>
        body { font-family: 'Inter', sans-serif; line-height: 1.6; color: #333; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px; }
        .header { background-color: #009A44; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { padding: 20px; }
        .footer { text-align: center; font-size: 12px; color: #777; margin-top: 20px; }
        .btn { display: inline-block; padding: 10px 20px; background-color: #009A44; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Sécurité <?php echo e(config('app.name')); ?></h1>
        </div>
        <div class="content">
            <p>Bonjour <?php echo e($user->name ?? $user->nom); ?>,</p>
            <p>Nous vous informons que le mot de passe de votre compte <?php echo e(config('app.name')); ?> a été modifié avec succès.</p>
            <p>Si vous n'êtes pas à l'origine de cette modification, nous vous recommandons vivement de contacter immédiatement votre administrateur ou de réinitialiser votre mot de passe.</p>
            <p>Pour des raisons de sécurité, ne partagez jamais vos identifiants de connexion.</p>
            <p>L'équipe <?php echo e(config('app.name')); ?></p>
        </div>
        <div class="footer">
            &copy; <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?> - Système de Suivi des Travaux. Tous droits réservés.
        </div>
    </div>
</body>
</html>
<?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/emails/password-changed.blade.php ENDPATH**/ ?>