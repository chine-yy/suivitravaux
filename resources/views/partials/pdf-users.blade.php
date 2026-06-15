<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Liste des Utilisateurs' }} - CNRST</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.4; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #009A44; padding-bottom: 10px; }
        .logo { font-size: 20px; font-weight: bold; color: #009A44; margin-bottom: 3px; }
        .section-title { color: #009A44; border-bottom: 2px solid #a5d6a7; margin-top: 20px; padding-bottom: 5px; font-size: 14px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #009A44; color: white; padding: 10px; text-align: left; font-size: 11px; }
        td { padding: 8px; border-bottom: 1px solid #eee; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 9px; font-weight: bold; background-color: #3b82f6; color: #fff; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #777; border-top: 1px solid #eee; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">CNRST SUIVI TRAVAUX</div>
        <div style="font-size: 16px; text-transform: uppercase; color: #009A44;">{{ $title ?? 'Liste des Utilisateurs' }}</div>
        <div style="font-size: 12px; color: #666;">Généré le {{ now()->format('d/m/Y à H:i') }}</div>
    </div>

    <div class="section">
        <h4 class="section-title">Synthèse des Utilisateurs</h4>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Rôle</th>
                    <th>Type de compte</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td><strong>{{ $user->name }} {{ $user->prenom }}</strong></td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->telephone ?? '—' }}</td>
                    <td><span class="badge">{{ $user->role->nom ?? '—' }}</span></td>
                    <td>{{ ucfirst($user->type_compte) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        {{ $title ?? 'Liste des Utilisateurs' }} - CNRST SUIVI TRAVAUX - &copy; {{ date('Y') }} CNRST
    </div>
</body>
</html>
