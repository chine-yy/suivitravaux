<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue sur <?php echo e(config('app.name')); ?> - Création de compte</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .email-wrapper { background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); }
        .email-header { background: linear-gradient(135deg, #FF6B35 0%, #e85a2a 100%); padding: 30px; text-align: center; }
        .email-header h1 { color: #ffffff; font-size: 28px; font-weight: bold; margin: 0; }
        .email-header .subtitle { color: #ffffff; opacity: 0.9; font-size: 14px; margin-top: 5px; }
        .email-body { padding: 30px; }
        .email-body p { color: #555555; margin-bottom: 15px; font-size: 15px; }
        .welcome-box { background: linear-gradient(135deg, #e8f5e9, #c8e6c9); border-left: 4px solid #FF6B35; border-radius: 0 8px 8px 0; padding: 20px; margin: 20px 0; }
        .welcome-box h2 { color: #005a28; font-size: 20px; margin-bottom: 10px; }
        .info-item { margin-bottom: 10px; }
        .info-label { font-weight: bold; color: #333333; }
        .info-value { color: #555555; }
        .btn-connect {
            display: inline-block; background: linear-gradient(135deg, #FF6B35, #e85a2a);
            color: #ffffff; padding: 14px 32px; border-radius: 8px; text-decoration: none;
            font-weight: bold; font-size: 16px; margin: 20px 0;
        }
        .text-center { text-align: center; }
        .email-footer { background-color: #1a1a1a; padding: 20px; text-align: center; }
        .email-footer p { color: #999999; font-size: 12px; margin: 0; }
        .email-footer .brand { color: #FF6B35; font-weight: bold; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="email-wrapper">
            <div class="email-header">
                <h1>👷 Bienvenue Membre !</h1>
                <p class="subtitle">Votre compte a été créé par l'administrateur</p>
            </div>

            <div class="email-body">
                <p>Bonjour <strong><?php echo e($userName); ?></strong>,</p>
                <p>Un compte Membre a été créé pour vous sur la plateforme <?php echo e(config('app.name')); ?>.</p>

                <div class="welcome-box">
                    <h2>🔑 Vos identifiants de connexion</h2>
                    <div class="info-item">
                        <span class="info-label">✉️ Email :</span>
                        <span class="info-value"><?php echo e($email); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">🔑 Mot de passe :</span>
                        <span class="info-value" style="background-color: #fee2e2; padding: 2px 6px; border-radius: 4px; font-family: monospace;"><?php echo e($password); ?></span>
                    </div>
                </div>

                <p>Nous vous recommandons de changer votre mot de passe dès votre première connexion dans les paramètres du profil.</p>

                <div class="text-center">
                    <a href="<?php echo e(url('/login')); ?>" class="btn-connect">Accéder au site</a>
                </div>

                <p style="color: #999; font-size: 13px; margin-top: 20px;">
                    Si vous n'êtes pas à l'origine de cette demande, veuillez contacter votre administrateur.
                </p>
            </div>

            <div class="email-footer">
                <p class="brand"><?php echo e(config('app.name')); ?> - Solution de gestion de chantier BTP</p>
                <p>Cet email a été envoyé automatiquement.</p>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/emails/user-created.blade.php ENDPATH**/ ?>