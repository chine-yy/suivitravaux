<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mise à jour du budget</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #009A44, #007a35); color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; }
        .info-box { background-color: #e8f5e9; border-left: 4px solid #009A44; padding: 20px; margin: 20px 0; border-radius: 0 8px 8px 0; }
        .info-box p { margin: 10px 0; }
        .info-box strong { color: #009A44; }
        .btn { display: inline-block; background-color: #009A44; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        .footer { background-color: #333; color: white; padding: 20px; text-align: center; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Mise à jour du budget</h1>
        </div>

        <div class="content">
            <h2>Bonjour {{ $user->name ?? $user->prenom }},</h2>

            <p>Le budget du projet <strong>{{ $projet->nom }}</strong> a été défini ou mis à jour.</p>

            <div class="info-box">
                <p><strong>Projet :</strong> {{ $projet->nom }}</p>
                <p><strong>Nouveau budget alloué :</strong> {{ number_format($amount, 0, ',', ' ') }} FCF</p>
            </div>

            <p>Vous pouvez consulter les détails du budget et le suivi des dépenses dans votre espace de gestion.</p>

            <a href="{{ url('/login') }}" class="btn">Accéder à mon espace</a>

            <p>Cordialement,<br>L'équipe {{ config('app.name') }}</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }} - Tous droits réservés</p>
        </div>
    </div>
</body>
</html>
