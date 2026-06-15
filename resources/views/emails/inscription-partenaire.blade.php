<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès partenaire - {{ config('app.name') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .email-wrapper {
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #FF6B35 0%, #e85a2a 100%);
            padding: 30px;
            text-align: center;
        }
        .email-header h1 {
            color: #ffffff;
            font-size: 28px;
            font-weight: bold;
            margin: 0;
        }
        .email-header .subtitle {
            color: #ffffff;
            opacity: 0.9;
            font-size: 14px;
            margin-top: 5px;
        }
        .email-body {
            padding: 30px;
        }
        .info-card {
            background-color: #f8f9fa;
            border-left: 4px solid #FF6B35;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 0 5px 5px 0;
        }
        .info-item {
            margin-bottom: 12px;
        }
        .info-item:last-child {
            margin-bottom: 0;
        }
        .info-label {
            font-weight: bold;
            color: #333333;
            display: block;
            margin-bottom: 5px;
        }
        .info-value {
            color: #555555;
        }
        .credentials-box {
            background-color: #fff7f3;
            border: 1px solid #ffd5c7;
            border-radius: 5px;
            padding: 20px;
            margin-top: 20px;
        }
        .credentials-title {
            font-weight: bold;
            color: #333333;
            margin-bottom: 10px;
        }
        .password {
            font-size: 20px;
            letter-spacing: 1px;
            color: #e85a2a;
            font-weight: bold;
        }
        .email-footer {
            background-color: #1a1a1a;
            padding: 20px;
            text-align: center;
        }
        .email-footer p {
            color: #999999;
            font-size: 12px;
            margin: 0;
        }
        .email-footer .brand {
            color: #FF6B35;
            font-weight: bold;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="email-wrapper">
            <div class="email-header">
                <h1>🔐 Vos accès partenaire</h1>
                <p class="subtitle">Bienvenue sur {{ config('app.name') }}</p>
            </div>

            <div class="email-body">
                <div class="info-card">
                    <div class="info-item">
                        <span class="info-label">👤 Partenaire :</span>
                        <span class="info-value">{{ $prenom }} {{ $nom }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">✉️ Email :</span>
                        <span class="info-value">{{ $email }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">📁 Projet :</span>
                        <span class="info-value">{{ $projet_nom }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">👷 Chef d'équipe :</span>
                        <span class="info-value">{{ $chef_equipe_prenom }} {{ $chef_equipe_nom }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">🧭 Chef de projet :</span>
                        <span class="info-value">{{ trim(($chef_projet_prenom ?? '') . ' ' . ($chef_projet_nom ?? '')) }}</span>
                    </div>
                </div>

                <div class="credentials-box">
                    <div class="credentials-title">Mot de passe généré :</div>
                    <div class="password">{{ $mot_de_passe }}</div>
                    <p style="margin-top: 10px; color: #555555;">Merci de le modifier lors de votre première connexion.</p>
                </div>
            </div>

            <div class="email-footer">
                <p class="brand">{{ config('app.name') }} - Solution de gestion de chantier BTP</p>
                <p>Ce message contient vos identifiants d'accès partenaire</p>
            </div>
        </div>
    </div>
</body>
</html>
