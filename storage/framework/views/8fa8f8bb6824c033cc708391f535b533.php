<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Vous êtes le nouveau Chef d'Équipe</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { padding: 20px; border: 1px solid #eee; border-top: none; }
        .footer { text-align: center; padding: 10px; font-size: 12px; color: #666; }
        .btn { display: inline-block; padding: 10px 20px; background-color: #0d6efd; color: #fff; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Félicitations !</h2>
        </div>
        <div class="content">
            <p>Bonjour <strong><?php echo e($chef->name); ?></strong>,</p>
            
            <p>Nous vous informons que vous avez été désigné(e) comme <strong>Chef de l'équipe</strong> "<strong><?php echo e($equipe->nom); ?></strong>".</p>
            
            <p>En tant que chef d'équipe, vous serez responsable de l'organisation et du bon déroulement des missions confiées à votre groupe sur le projet <strong><?php echo e($equipe->projet->nom ?? 'N/A'); ?></strong>.</p>
            
            <p>Veuillez vous connecter à la plateforme pour découvrir les détails de votre équipe et planifier vos prochaines étapes.</p>
            
            <p style="text-align: center; margin: 30px 0;">
                <a href="<?php echo e(url('/login')); ?>" class="btn">Accéder à mon espace</a>
            </p>
            
            <p>Cordialement,<br>L'équipe <?php echo e(config('app.name')); ?></p>
        </div>
        <div class="footer">
            Cet e-mail a été envoyé automatiquement, merci de ne pas y répondre directement.
        </div>
    </div>
</body>
</html>
<?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/emails/nouvelle-equipe.blade.php ENDPATH**/ ?>