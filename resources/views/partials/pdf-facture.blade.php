<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture - {{ $facture->numero_facture }}</title>
    <style>
        @page { margin: 0; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1e293b; background-color: #f8fafc; margin: 0; padding: 40px; font-size: 11px; }
        .header { margin-bottom: 25px; }
        .logo { font-size: 24px; font-weight: bold; color: #009A44; margin-bottom: 5px; }
        .page-title { font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 2px; }
        .page-subtitle { color: #64748b; font-size: 13px; margin-bottom: 25px; }

        .stats-grid { width: 100%; margin-bottom: 25px; border-collapse: separate; border-spacing: 12px 0; margin-left: -12px; }
        .stat-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; text-align: center; width: 33%; }
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

        .footer { position: fixed; bottom: 20px; left: 40px; right: 40px; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        table { width: 100%; border-collapse: collapse; }

        .badge { padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: 600; }
        .bg-light { background-color: #f8fafc; }
        .text-primary { color: #3b82f6; }
        .border { border: 1px solid #e2e8f0; }
        .rounded-pill { border-radius: 50px; }
        .p-3 { padding: 15px; }
        .rounded { border-radius: 8px; }
        .bg-light { background-color: #f8fafc; }
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
        <div class="page-title">Facture: {{ $facture->numero_facture }}</div>
        <div class="page-subtitle">Détails et statut de la facture</div>
    </div>

    <table class="stats-grid">
        <tr>
            <td class="stat-card">
                <div class="stat-value">{{ number_format($facture->montant_ht, 0, ',', ' ') }}</div>
                <div class="stat-label">Montant HT (FCFA)</div>
            </td>
            <td class="stat-card" style="border-top: 3px solid #009A44;">
                <div class="stat-value">{{ number_format($facture->montant_ttc, 0, ',', ' ') }}</div>
                <div class="stat-label">Montant TTC (FCFA)</div>
            </td>
            <td class="stat-card">
                @php
                    $paiementText = [
                        'en_attente' => 'En attente',
                        'paye' => 'Payé',
                        'en_retard' => 'En retard',
                        'annule' => 'Annulé'
                    ];
                @endphp
                <div class="stat-value">{{ $paiementText[$facture->statut_paiement] ?? $facture->statut_paiement }}</div>
                <div class="stat-label">Statut Paiement</div>
            </td>
        </tr>
    </table>

    <table class="main-container">
        <tr>
            <td class="left-col">
                <div class="card">
                    <div class="card-header"> Informations de la Facture</div>
                    <div class="card-body">
                        <table style="width: 100%;">
                            <tr>
                                <td style="width: 50%;">
                                    <span class="label">Numéro de Facture</span>
                                    <span class="value">{{ $facture->numero_facture }}</span>
                                </td>
                                <td style="width: 50%;">
                                    <span class="label">Type de Facture</span>
                                    <span class="value">
                                        @php
                                            $types = ['acompte' => 'Acompte', 'solde' => 'Solde', 'avoir' => 'Avoir', 'regularisation' => 'Régularisation'];
                                        @endphp
                                        {{ $types[$facture->type] ?? $facture->type ?? 'Standard' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="label">Date d'émission</span>
                                    <span class="value">{{ $facture->date_emission ? date('d/m/Y', strtotime($facture->date_emission)) : 'Non définie' }}</span>
                                </td>
                                <td>
                                    <span class="label">Date d'échéance</span>
                                    <span class="value">{{ $facture->date_echeance ? date('d/m/Y', strtotime($facture->date_echeance)) : 'Non définie' }}</span>
                                </td>
                            </tr>
                        </table>

                        <table style="width: 100%; margin-top: 15px;">
                            <tr>
                                <td style="width: 33%;">
                                    <span class="label">Montant HT</span>
                                    <span class="value">{{ number_format($facture->montant_ht, 0, ',', ' ') }} FCAFA</span>
                                </td>
                                <td style="width: 33%;">
                                    <span class="label">TVA</span>
                                    <span class="value">{{ number_format($facture->montant_tva, 0, ',', ' ') }} FCAFA</span>
                                </td>
                                <td style="width: 34%;">
                                    <span class="label">Montant TTC</span>
                                    <span class="value" style="font-weight: 800; color: #198754;">{{ number_format($facture->montant_ttc, 0, ',', ' ') }} FCAFA</span>
                                </td>
                            </tr>
                        </table>

@if($facture->notes)
                        <span class="label">Notes</span>
                        <div style="background: #f8fafc; padding: 15px; border-radius: 8px; color: #475569; line-height: 1.6; min-height: 60px;">
                            {!! nl2br(e($facture->notes)) !!}
                        </div>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"> Projet Associé</div>
                    <div class="card-body">
                        @if($facture->projet)
                            <div style="font-weight: 600;">{{ $facture->projet->nom }}</div>
                            <div style="font-size: 9px; color: #64748b;">Projet #{{ $facture->projet->id }}</div>
                        @else
                            <div style="color: #94a3b8; text-align: center; padding: 15px;">Aucun projet associé</div>
                        @endif
                    </div>
                </div>
            </td>

<td class="right-col">
<div class="card">
                    <div class="card-header"> Partenaire</div>
                    <div class="card-body">
                        @if($facture->partenaire)
                            <div style="margin-bottom: 10px;">
                                <div style="font-weight: 600; font-size: 14px;">{{ trim($facture->partenaire->prenom . ' ' . $facture->partenaire->nom) }}</div>
                                <div style="font-size: 9px; color: #64748b;">{{ $facture->partenaire->email }}</div>
                            </div>
                            @if($facture->partenaire->telephone)
                            <div style="font-size: 9px; color: #64748b; margin-top: 5px;">
                                <i class="bi bi-phone"></i> {{ $facture->partenaire->telephone }}
                            </div>
                            @endif
                        @else
                            <div style="color: #94a3b8; text-align: center; padding: 15px;">Aucun partenaire associé</div>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"> Historique</div>
                    <div class="card-body">
                        <span class="label">Créé le</span>
                        <span class="value">{{ $facture->created_at->format('d/m/Y') }}</span>
                        @if($facture->createur && $facture->createur->name)
                        <span class="label">Créé par</span>
                        <span class="value">{{ $facture->createur->name }}</span>
                        @endif
                        <span class="label">Dernière modification</span>
                        <span class="value">{{ $facture->updated_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Document de facture généré par CNRST Suivi Travaux - &copy; {{ date('Y') }}
    </div>
</body>
</html>
