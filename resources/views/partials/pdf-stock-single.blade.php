<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Détails Stock - {{ $stock->nom }}</title>
    <style>
        @page { margin: 0; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1e293b; background-color: #f8fafc; margin: 0; padding: 40px; font-size: 11px; }
        .header { margin-bottom: 25px; }
        .logo { font-size: 24px; font-weight: bold; color: #009A44; margin-bottom: 5px; }
        .page-title { font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 2px; }
        .page-subtitle { color: #64748b; font-size: 13px; margin-bottom: 25px; }

        .stats-grid { width: 100%; margin-bottom: 25px; border-collapse: separate; border-spacing: 12px 0; margin-left: -12px; }
        .stat-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; text-align: center; width: 25%; }
        .stat-value { font-size: 18px; font-weight: bold; color: #0f172a; margin-bottom: 4px; }
        .stat-label { font-size: 9px; color: #64748b; text-transform: uppercase; font-weight: 800; }

        .main-container { width: 100%; border-collapse: separate; border-spacing: 20px 0; margin-left: -20px; }
        .left-col { width: 66%; vertical-align: top; }
        .right-col { width: 34%; vertical-align: top; }

        .card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px; overflow: hidden; }
        .card-header { background: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 12px 20px; font-weight: 700; color: #334155; font-size: 12px; }
        .card-header-icon { color: #009A44; margin-right: 8px; }
        .card-body { padding: 20px; }

        .label { color: #64748b; font-weight: 700; font-size: 9px; text-transform: uppercase; margin-bottom: 5px; display: block; }
        .value { color: #0f172a; font-weight: 600; font-size: 11px; margin-bottom: 15px; display: block; }

        .badge { padding: 4px 8px; border-radius: 6px; font-weight: 700; font-size: 10px; display: inline-block; }
        .badge-success { background: #c8e6c9; color: #005a28; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-primary { background: #dbeafe; color: #1e40af; }
        .badge-secondary { background: #f1f5f9; color: #475569; }

        .footer { position: fixed; bottom: 20px; left: 40px; right: 40px; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        table { width: 100%; border-collapse: collapse; }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td><div class="logo">CNRST</div></td>
                <td style="text-align: right; color: #94a3b8; font-size: 9px;">Généré le {{ now()->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
        <div class="page-title">{{ $stock->nom }}</div>
        <div class="page-subtitle">Fiche détaillée du stock matériel</div>
    </div>

    @php
    $statutClass = [
        'disponible' => 'badge-success',
        'epuise' => 'badge-danger',
        'en_reapprovisionnement' => 'badge-primary'
    ];
    $statutText = [
        'disponible' => 'Disponible',
        'epuise' => 'Épuisé',
        'en_reapprovisionnement' => 'En réapprovisionnement'
    ];
    @endphp

    <table class="stats-grid">
        <tr>
            <td class="stat-card">
                <div class="stat-value">{{ $stock->quantite }}</div>
                <div class="stat-label">Quantité</div>
            </td>
            <td class="stat-card">
                <div class="stat-value">{{ number_format($stock->prix_unitaire, 0, ',', ' ') }}</div>
                <div class="stat-label">Prix Unitaire (FCFA)</div>
            </td>
            <td class="stat-card">
                <div class="stat-value">{{ number_format($stock->quantite * $stock->prix_unitaire, 0, ',', ' ') }}</div>
                <div class="stat-label">Valeur Totale</div>
            </td>
            <td class="stat-card" style="border-top: 3px solid #009A44;">
                <div class="stat-value">
                    <span class="badge {{ $statutClass[$stock->statut] ?? 'badge-secondary' }}">
                        {{ $statutText[$stock->statut] ?? $stock->statut }}
                    </span>
                </div>
                <div class="stat-label">Statut</div>
            </td>
        </tr>
    </table>

    <table class="main-container">
        <tr>
            <td class="left-col">
                <div class="card">
                    <div class="card-header"> Informations générales</div>
                    <div class="card-body">
                        <table style="width: 100%;">
                            <tr>
                                <td style="width: 50%;">
                                    <span class="label">Nom</span>
                                    <span class="value">{{ $stock->nom }}</span>
                                </td>
                                <td style="width: 50%;">
                                    <span class="label">Référence</span>
                                    <span class="value">{{ $stock->reference ?? 'N/A' }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="label">Catégorie</span>
                                    <span class="value">{{ $stock->categorie ?? 'Non définie' }}</span>
                                </td>
                                <td>
                                    <span class="label">Fournisseur</span>
                                    <span class="value">{{ $stock->fournisseur->nom ?? 'Non renseigné' }}</span>
                                </td>
                            </tr>
                        </table>

                        @if($stock->description)
                        <span class="label">Description</span>
                        <div style="background: #f8fafc; padding: 15px; border-radius: 8px; color: #475569; line-height: 1.6; min-height: 60px;">
                            {!! nl2br(e($stock->description)) !!}
                        </div>
                        @endif
                    </div>
                </div>
            </td>

            <td class="right-col">
                <div class="card">
                    <div class="card-header"> Informations financières</div>
                    <div class="card-body">
                        <span class="label">Prix Unitaire</span>
                        <div class="stat-value text-success">{{ number_format($stock->prix_unitaire, 0, ',', ' ') }} FCFA</div>
                        <br>
                        <span class="label">Quantité en stock</span>
                        <div class="stat-value text-primary">{{ $stock->quantite }} unité(s)</div>
                        <br>
                        <span class="label">Valeur totale</span>
                        <div class="stat-value" style="color: #0f172a;">{{ number_format($stock->quantite * $stock->prix_unitaire, 0, ',', ' ') }} FCFA</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"> Historique</div>
                    <div class="card-body">
                        <span class="label">Créé le</span>
                        <span class="value">{{ $stock->created_at->format('d/m/Y à H:i') }}</span>
                        <span class="label">Dernière modification</span>
                        <span class="value">{{ $stock->updated_at->format('d/m/Y à H:i') }}</span>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Document généré par CNRST Suivi Travaux - &copy; {{ date('Y') }}
    </div>
</body>
</html>
