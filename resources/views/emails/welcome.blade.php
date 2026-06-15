<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue - {{ config('app.name') }}</title>
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
                <h1>🎉 Bienvenue sur {{ config('app.name') }} !</h1>
                <p class="subtitle">Votre compte a été créé avec succès</p>
            </div>

            <div class="email-body">
                <p>Bonjour <strong>{{ $userName }}</strong>,</p>
                <p>Nous sommes ravis de vous accueillir sur {{ config('app.name') }}, votre solution de gestion de chantier BTP.</p>

                <div class="welcome-box">
                    <h2>📋 Récapitulatif de votre inscription</h2>
                    <div class="info-item">
                        <span class="info-label">🏢 Entreprise :</span>
                        <span class="info-value">{{ $companyName }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">✉️ Email :</span>
                        <span class="info-value">{{ $email }}</span>
                    </div>
                </div>

                <p>Vous pouvez maintenant vous connecter à votre espace pour commencer à gérer vos projets de construction.</p>

                <div class="text-center">
                    <a href="{{ url('/login') }}" class="btn-connect">Se connecter à {{ config('app.name') }}</a>
                </div>

                <p style="color: #999; font-size: 13px; margin-top: 20px;">
                    Si vous n'avez pas créé ce compte, veuillez nous contacter immédiatement.
                </p>
            </div>

            <div class="email-footer">
                <p class="brand">{{ config('app.name') }} - Solution de gestion de chantier BTP</p>
                <p>Cet email a été envoyé automatiquement suite à votre inscription.</p>
            </div>
        </div>
    </div>
</body>
</html>
