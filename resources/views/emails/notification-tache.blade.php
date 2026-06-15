<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle tâche assignée - {{ config('app.name') }}</title>
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
        .greeting {
            color: #333333;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .intro-text {
            color: #555555;
            margin-bottom: 25px;
        }
        .info-card {
            background-color: #f8f9fa;
            border-left: 4px solid #FF6B35;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 0 5px 5px 0;
        }
        .info-item {
            margin-bottom: 15px;
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
        .btn-wrapper {
            text-align: center;
            margin-top: 30px;
            margin-bottom: 10px;
        }
        .btn {
            display: inline-block;
            background-color: #FF6B35;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: bold;
            text-align: center;
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
                <h1>📋 Nouvelle Tâche</h1>
                <p class="subtitle">Assignation - {{ config('app.name') }}</p>
            </div>
            
            <div class="email-body">
                <div class="greeting">Bonjour {{ $user->name ?? ($user->prenom ?? 'Utilisateur') }},</div>
                <div class="intro-text">
                    Votre chef d'équipe, <strong>{{ $assignedBy->prenom }} {{ $assignedBy->nom }}</strong>, vient de vous assigner une nouvelle tâche complète.
                </div>
                
                <div class="info-card">
                    <div class="info-item">
                        <span class="info-label">📌 Tâche :</span>
                        <span class="info-value">{{ $tache->titre ?? $tache->nom }}</span>
                    </div>
                    @if($tache->description)
                    <div class="info-item">
                        <span class="info-label">📝 Description :</span>
                        <span class="info-value">{{ $tache->description }}</span>
                    </div>
                    @endif
                    @if($tache->date_fin_prevue)
                    <div class="info-item">
                        <span class="info-label">⏳ Date d'échéance :</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($tache->date_fin_prevue)->format('d/m/Y') }}</span>
                    </div>
                    @endif
                </div>

                <div class="intro-text">
                    Vous pouvez consulter les détails de cette tâche directement sur votre espace {{ config('app.name') }}.
                </div>
                
                <div class="btn-wrapper">
                    <a href="{{ url('/login') }}" class="btn">Accéder à mon espace</a>
                </div>
            </div>
            
            <div class="email-footer">
                <p class="brand">{{ config('app.name') }} - Solution de gestion de chantier BTP</p>
                <p>Cet email a été envoyé automatiquement. Merci de ne pas y répondre.</p>
            </div>
        </div>
    </div>
</body>
</html>
