<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Liste des Fournisseurs - CNRST</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.4; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #009A44; padding-bottom: 10px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #777; border-top: 1px solid #eee; padding-top: 5px; }
        .logo { font-size: 20px; font-weight: bold; color: #009A44; margin-bottom: 3px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 10px; }
        th { background-color: #009A44; color: white; padding: 6px; text-align: left; }
        td { padding: 5px; border-bottom: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">CNRST SUIVI TRAVAUX</div>
        <div style="font-size: 16px; text-transform: uppercase; color: #009A44;">Liste des Fournisseurs</div>
        <div style="font-size: 12px; color: #666;">Généré le {{ date('d/m/Y à H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Catégorie</th>
                <th>Contact</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($fournisseurs as $fournisseur)
            <tr>
                <td><strong>{{ $fournisseur->nom }}</strong></td>
                <td>{{ $fournisseur->email ?? '—' }}</td>
                <td>{{ $fournisseur->telephone ?? '—' }}</td>
                <td>{{ $fournisseur->categorie ?? '—' }}</td>
                <td>{{ $fournisseur->contact_nom ?? '—' }} {{ $fournisseur->contact_prenom ?? '' }}</td>
                <td>{{ ucfirst($fournisseur->statut) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Liste des Fournisseurs - CNRST SUIVI TRAVAUX - &copy; {{ date('Y') }} CNRST
    </div>
</body>
</html>
