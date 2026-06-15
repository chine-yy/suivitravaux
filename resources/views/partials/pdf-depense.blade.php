<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fiche Dépense - {{ $depense->id }}</title>
    <style>
        @page { margin: 0; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1e293b; background-color: #f8fafc; margin: 0; padding: 40px; font-size: 11px; }
        .header { margin-bottom: 25px; display: flex; align-items: center; gap: 15px; }
        .header-icon { color: #009A44; font-size: 28px; flex-shrink: 0; }
        .header-text { flex-grow: 1; }
        .logo { font-size: 20px; font-weight: bold; color: #009A44; margin-bottom: 2px; }
        .page-title { font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 2px; }
        .page-subtitle { color: #64748b; font-size: 13px; margin-bottom: 0; }

        .card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px; overflow: hidden; }
        .card-header { background: #ffffff; border-bottom: 2px solid #009A44; padding: 12px 20px; font-weight: 700; color: #334155; font-size: 12px; }
        .card-header-icon { color: #009A44; margin-right: 8px; }
        .card-body { padding: 20px; }

        .label { color: #64748b; font-weight: 700; font-size: 9px; text-transform: uppercase; margin-bottom: 5px; display: block; letter-spacing: 0.5px; }
        .value { color: #0f172a; font-weight: 600; font-size: 11px; margin-bottom: 15px; display: block; }

        .grid-2col { display: table; width: 100%; border-collapse: collapse; }
        .grid-2col-cell { display: table-cell; width: 50%; padding-right: 20px; }
        .grid-2col-cell:last-child { padding-right: 0; }

        .main-container { width: 100%; border-collapse: collapse; }
        .left-col { width: 65%; vertical-align: top; padding-right: 20px; }
        .right-col { width: 35%; vertical-align: top; }

        .status-badge { display: inline-block; padding: 8px 12px; border-radius: 4px; font-size: 10px; font-weight: bold; color: white; }
        .status-en_attente { background-color: #009A44; }
        .status-validee { background-color: #009A44; }
        .status-rejetee { background-color: #dc3545; }

        .amount-danger { color: #dc3545; font-weight: 800; font-size: 18px; }
        .amount-success { color: #009A44; font-weight: 800; font-size: 18px; }

        .description-box { background: #f8fafc; padding: 15px; border-radius: 8px; color: #475569; line-height: 1.6; min-height: 80px; border-left: 4px solid #009A44; white-space: pre-wrap; word-wrap: break-word; }

        .info-card { background: #f8fafc; padding: 15px; border-radius: 8px; border-left: 4px solid #009A44; text-align: center; margin-bottom: 15px; }
        .info-name { font-size: 13px; font-weight: 700; color: #0f172a; margin-bottom: 15px; }
        .info-item { margin-bottom: 12px; display: flex; align-items: flex-start; }
        .info-icon { width: 28px; height: 28px; background: white; border: 1px solid #e2e8f0; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #009A44; margin-right: 10px; flex-shrink: 0; font-size: 12px; }
        .info-data { flex-grow: 1; }
        .info-label { font-size: 9px; color: #64748b; font-weight: 700; text-transform: uppercase; margin-bottom: 3px; }
        .info-value { font-size: 10px; color: #334155; font-weight: 600; }

        .footer { position: fixed; bottom: 20px; left: 40px; right: 40px; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        table { width: 100%; border-collapse: collapse; }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-icon"><i class="bi bi-receipt" style="font-size: 28px; color: #009A44;"></i></div>
        <div class="header-text">
            <div class="logo">CNRST</div>
            <div class="page-title">Fiche Dépense</div>
            <div class="page-subtitle">{{ $depense->projet?->nom ?? 'Projet inconnu' }} - {{ $depense->created_at->format('d/m/Y') }}</div>
        </div>
    </div>

    <table class="main-container">
        <tr>
            <td class="left-col">
                <div class="card">
                    <div class="card-header">
                         Informations de la Dépense
                        @php
                        $statutClass = [
                            'en_attente' => 'status-en_attente',
                            'validee' => 'status-validee',
                            'rejetee' => 'status-rejetee',
                        ][$depense->statut] ?? 'status-en_attente';
                        $statutText = [
                            'en_attente' => 'En attente',
                            'validee' => 'Validée',
                            'rejetee' => 'Rejetée',
                        ][$depense->statut] ?? ucfirst($depense->statut ?? 'N/A');
                        @endphp
                        <span style="float: right;"><span class="status-badge {{ $statutClass }}">{{ $statutText }}</span></span>
                    </div>
                    <div class="card-body">
                        <table class="grid-2col">
                            <tr>
                                <td class="grid-2col-cell">
                                    <span class="label">Projet</span>
                                    <span class="value">{{ $depense->projet?->nom ?? 'N/A' }}</span>
                                </td>
                                <td class="grid-2col-cell">
                                    <span class="label">Catégorie</span>
                                    <span class="value">{{ $depense->getCategorieLabel() ?? 'N/A' }}</span>
                                </td>
                            </tr>
                        </table>
                        <table class="grid-2col">
                            <tr>
                                <td class="grid-2col-cell">
                                    <span class="label">Date de Dépense</span>
                                    <span class="value">{{ $depense->date_depense ? \Carbon\Carbon::parse($depense->date_depense)->format('d/m/Y') : 'N/A' }}</span>
                                </td>
                                <td class="grid-2col-cell">
                                    <span class="label">Mode de Paiement</span>
                                    <span class="value">{{ $depense->getTypePaiementLabel() ?? 'N/A' }}</span>
                                </td>
                            </tr>
                        </table>
                        <table class="grid-2col">
                            <tr>
                                <td class="grid-2col-cell">
                                    <span class="label">Référence / N°Facture</span>
                                    <span class="value">{{ $depense->reference ?: 'N/A' }}</span>
                                </td>
                                <td class="grid-2col-cell">
                                    <span class="label">Montant</span>
                                    <span class="amount-danger">{{ number_format($depense->montant, 0, ',', ' ') }} FCFA</span>
                                </td>
                            </tr>
                        </table>
                        @if($depense->description)
                        <span class="label">Description</span>
                        <div class="description-box">{{ $depense->description }}</div>
                        @endif
                    </div>
                </div>
            </td>

            <td class="right-col">
                <div class="card">
                    <div class="card-header"><i class="card-header-icon bi bi-cash-stack"></i> Montant</div>
                    <div class="card-body" style="text-align: center;">
                        <div class="amount-danger">{{ number_format($depense->montant, 0, ',', ' ') }}</div>
                        <div style="color: #64748b; font-size: 10px; font-weight: 700; text-transform: uppercase;">FCFA</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><i class="card-header-icon bi bi-clock-history"></i> Historique</div>
                    <div class="card-body">
                        <span class="label">Créé le</span>
                        <span class="value">{{ $depense->created_at?->format('d/m/Y à H:i') ?? 'N/A' }}</span>
                        @if($depense->updated_at && $depense->updated_at != $depense->created_at)
                        <span class="label">Dernière modification</span>
                        <span class="value">{{ $depense->updated_at->format('d/m/Y à H:i') }}</span>
                        @endif
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Document confidentiel - Service de Suivi des Travaux | Généré le {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>