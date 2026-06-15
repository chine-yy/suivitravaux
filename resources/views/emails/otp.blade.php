<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code OTP - {{ config('app.name') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .email-wrapper { background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); }
        .email-header { background: linear-gradient(135deg, #FF6B35 0%, #e85a2a 100%); padding: 30px; text-align: center; }
        .email-header h1 { color: #ffffff; font-size: 28px; font-weight: bold; margin: 0; }
        .email-header .subtitle { color: #ffffff; opacity: 0.9; font-size: 14px; margin-top: 5px; }
        .email-body { padding: 30px; text-align: center; }
        .email-body p { color: #555555; margin-bottom: 20px; font-size: 16px; }
        .otp-box { background: linear-gradient(135deg, #e8f5e9, #c8e6c9); border: 2px dashed #FF6B35; border-radius: 12px; padding: 24px; margin: 20px auto; max-width: 280px; }
        .otp-code { font-size: 36px; font-weight: 900; letter-spacing: 10px; color: #005a28; }
        .warning-text { color: #999; font-size: 13px; margin-top: 20px; }
        .email-footer { background-color: #1a1a1a; padding: 20px; text-align: center; }
        .email-footer p { color: #999999; font-size: 12px; margin: 0; }
        .email-footer .brand { color: #FF6B35; font-weight: bold; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="email-wrapper">
            <div class="email-header">
                <h1>🔐 Réinitialisation</h1>
                <p class="subtitle">Code de vérification - {{ config('app.name') }}</p>
            </div>

            <div class="email-body">
                <p>Bonjour,</p>
                <p>Vous avez demandé la réinitialisation de votre mot de passe. Voici votre code de vérification :</p>

                <div class="otp-box">
                    <div class="otp-code">{{ $otp }}</div>
                </div>

                <p>Saisissez ce code dans le formulaire pour modifier votre mot de passe.</p>
                <p class="warning-text">⚠️ Ce code expire dans <strong>15 minutes</strong>. Si vous n'avez pas fait cette demande, ignorez cet email.</p>
            </div>

            <div class="email-footer">
                <p class="brand">{{ config('app.name') }} - Solution de gestion de chantier BTP</p>
                <p>Cet email a été envoyé automatiquement, ne pas répondre.</p>
            </div>
        </div>
    </div>
</body>
</html>
