<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Suppression de projet</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #dc3545;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 30px;
        }
        .info-box {
            background-color: #fff3e0;
            border-left: 4px solid #ff7f00;
            padding: 20px;
            margin: 20px 0;
        }
        .info-box p {
            margin: 10px 0;
        }
        .info-box strong {
            color: #ff7f00;
        }
        .alert-box {
            background-color: #fce4ec;
            border-left: 4px solid #dc3545;
            padding: 20px;
            margin: 20px 0;
        }
        .alert-box p {
            margin: 10px 0;
        }
        .alert-box strong {
            color: #dc3545;
        }
        .btn {
            display: inline-block;
            background-color: #ff7f00;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            background-color: #333;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Projet supprimé</h1>
        </div>

        <div class="content">
            <h2>Bonjour {{ $prenom }},</h2>

            <p>Nous vous informons que le projet sur lequel vous travailliez a été <strong>supprimé</strong> par le chef de projet.</p>

            <div class="alert-box">
                <p><strong>Projet supprimé :</strong> {{ $projet_nom }}</p>
                <p><strong>Votre rôle :</strong> {{ $role_label }}</p>
            </div>

            <div class="info-box">
                <p><strong>Budget du projet :</strong> {{ number_format($projet_budget ?? 0, 0, ',', ' ') }} FCFA</p>
                <p><strong>Votre salaire était :</strong> {{ number_format($salaire ?? 0, 0, ',', ' ') }} FCFA</p>
            </div>

            <p>Si vous avez des questions concernant cette suppression, veuillez contacter votre chef de projet.</p>

            @if(isset($chef_projet_nom))
            <div class="info-box">
                <p><strong>Chef de Projet :</strong> {{ $chef_projet_prenom ?? '' }} {{ $chef_projet_nom }}</p>
                <p><strong>Email :</strong> {{ $chef_projet_email ?? 'N/A' }}</p>
                @if(isset($chef_projet_telephone))
                <p><strong>Téléphone :</strong> {{ $chef_projet_telephone }}</p>
                @endif
            </div>
            @endif

            <a href="{{ url('/login') }}" class="btn">Se connecter</a>

            <p>Cordialement,<br>L'équipe {{ config('app.name') }}</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }} - Tous droits réservés</p>
        </div>
    </div>
</body>
</html>
