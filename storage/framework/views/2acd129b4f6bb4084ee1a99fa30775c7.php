<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ajout à l'équipe</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    
    <div style="background-color: #f8f9fc; padding: 20px; border-radius: 8px; border-left: 4px solid #4e73df;">
        <h2 style="color: #4e73df; margin-top: 0;">Bonjour <?php echo e($user->name); ?>,</h2>
        
        <p>Nous vous informons que vous avez été ajouté(e) en tant que membre à l'équipe <strong><?php echo e($equipe->nom); ?></strong>.</p>
        
        <div style="background-color: #fff; padding: 15px; border-radius: 5px; margin: 20px 0; border: 1px solid #e3e6f0;">
            <h3 style="margin-top: 0; color: #5a5c69; font-size: 16px;">Détails de l'équipe :</h3>
            <ul style="list-style-type: none; padding-left: 0; margin-bottom: 0;">
                <li style="margin-bottom: 8px;"><strong>Nom :</strong> <?php echo e($equipe->nom); ?></li>
                <?php if($equipe->projet): ?>
                <li style="margin-bottom: 8px;"><strong>Projet :</strong> <?php echo e($equipe->projet->nom); ?></li>
                <?php endif; ?>
                <?php if($equipe->description): ?>
                <li style="margin-bottom: 8px;"><strong>Description :</strong> <?php echo e($equipe->description); ?></li>
                <?php endif; ?>
            </ul>
        </div>
        
        <p>Vous avez désormais accès à ce projet avec le reste de l'équipe.</p>
        
        <div style="margin-top: 30px; font-size: 13px; color: #858796;">
            <p>Ceci est un email automatique, merci de ne pas y répondre.</p>
        </div>
    </div>
    
</body>
</html>
<?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/emails/user-added-equipe.blade.php ENDPATH**/ ?>