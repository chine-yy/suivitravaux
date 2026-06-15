<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Liste des Stocks - CNRST</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.4; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #009A44; padding-bottom: 10px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #777; border-top: 1px solid #eee; padding-top: 5px; }
        .logo { font-size: 20px; font-weight: bold; color: #009A44; margin-bottom: 3px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 10px; }
        th { background-color: #009A44; color: white; padding: 6px; text-align: left; }
        td { padding: 5px; border-bottom: 1px solid #eee; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 9px; font-weight: bold; color: #fff; }
        .bg-success { background-color: #009A44; }
        .bg-danger { background-color: #ef4444; }
        .bg-primary { background-color: #3b82f6; }
        .text-end { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">CNRST SUIVI TRAVAUX</div>
        <div style="font-size: 16px; text-transform: uppercase; color: #009A44;">Inventaire des Stocks</div>
        <div style="font-size: 12px; color: #666;">Généré le {{ date('d/m/Y à H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Référence</th>
                <th>Catégorie</th>
                <th>Quantité</th>
                <th>Prix Unit.</th>
                <th>Fournisseur</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stocks as $stock)
            <tr>
                <td><strong>{{ $stock->nom }}</strong></td>
                <td>{{ $stock->reference ?? '—' }}</td>
                <td>{{ $stock->categorie ?? '—' }}</td>
                <td>{{ $stock->quantite }}</td>
                <td>{{ number_format($stock->prix_unitaire, 0, ',', ' ') }} FCF</td>
                <td>{{ $stock->fournisseur->nom ?? '—' }}</td>
                <td>
                    @php
                        $statutText = ['disponible' => 'Disponible', 'epuise' => 'Épuisé', 'en_reapprovisionnement' => 'Réappro.'];
                    @endphp
                    {{ $statutText[$stock->statut] ?? $stock->statut }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Inventaire des Stocks - CNRST SUIVI TRAVAUX - &copy; {{ date('Y') }} CNRST
    </div>
</body>
</html>
