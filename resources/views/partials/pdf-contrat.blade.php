<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Détails du Contrat - {{ $contrat->numero_contrat }}</title>
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
        <div class="page-title">Contrat: {{ $contrat->numero_contrat }}</div>
        <div class="page-subtitle">Informations détaillées, type et statut du contrat</div>
    </div>

    <table class="stats-grid">
        <tr>
            <td class="stat-card">
                <div class="stat-value">{{ number_format($contrat->montant, 0, ',', ' ') }}</div>
                br
                <div class="stat-label">Montant (FCFA)</div>
            </td>
            <td class="stat-card" style="border-top: 3px solid #009A44;">
                @php
                    $statusText = [
                        'brouillon' => 'Brouillon',
                        'signe' => 'Signé',
                        'en_cours' => 'En cours',
                        'termine' => 'Terminé',
                        'annule' => 'Annulé'
                    ];
                @endphp
                <div class="stat-value">{{ $statusText[$contrat->statut] ?? $contrat->statut }}</div>
                <div class="stat-label">Statut</div>
            </td>
            br
            <td class="stat-card">
                <div class="stat-value">{{ $contrat->date_debut ? date('d/m/Y', strtotime($contrat->date_debut)) : 'N/A' }}</div>
                <div class="stat-label">Date début</div>
            </td>
        </tr>
    </table>

    <table class="main-container">
        <tr>
            <td class="left-col">
                <div class="card">
                    <div class="card-header"> Informations du Contrat</div>
                    <div class="card-body">
                        <table style="width: 100%;">
                            <tr>
                                <td style="width: 50%;">
                                    <span class="label">Numéro de Contrat</span>
                                    <span class="value">{{ $contrat->numero_contrat }}</span>
                                </td>
                                <td style="width: 50%;">
                                    <span class="label">Type de Contrat</span>
                                    <span class="value">
                                        @php
                                            $types = ['prestation' => 'Prestation', 'marche' => 'Marché', 'sous_traitance' => 'Sous-traitance', 'autre' => 'Autre'];
                                        @endphp
                                        {{ $types[$contrat->type] ?? $contrat->type }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="label">Date de Début</span>
                                    <span class="value">{{ $contrat->date_debut ? date('d/m/Y', strtotime($contrat->date_debut)) : 'Non définie' }}</span>
                                </td>
                                <td>
                                    <span class="label">Date de Fin</span>
                                    <span class="value">{{ $contrat->date_fin ? date('d/m/Y', strtotime($contrat->date_fin)) : 'Non définie' }}</span>
                                </td>
                            </tr>
                        </table>
                        <span class="label">Objet / Description</span>
                        <div style="background: #f8fafc; padding: 15px; border-radius: 8px; color: #475569; line-height: 1.6; min-height: 80px;">
                            {!! nl2br(e($contrat->objet ?? 'Aucun objet spécifié.')) !!}
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"> Conditions Particulières</div>
                    <div class="card-body">
                        @if($contrat->conditions)
                            <div style="color: #475569; line-height: 1.6;">
                                {!! nl2br(e($contrat->conditions)) !!}
                            </div>
                        @else
                            <div style="color: #94a3b8; font-style: italic;">Aucune condition particulière définie.</div>
                        @endif
                    </div>
                </div>
            </td>

            <td class="right-col">
                <div class="card">
                    <div class="card-header"> Aperçu & Statut</div>
                    <div class="card-body">
                        <div class="mb-4 text-center">
                            @php
                                $statusClass = [
                                    'brouillon' => '#6c757d',
                                    'signe' => '#0dcaf0',
                                    'en_cours' => '#0d6efd',
                                    'termine' => '#198754',
                                    'annule' => '#dc3545'
                                ];
                            @endphp
                            <span class="badge" style="background-color: {{ $statusClass[$contrat->statut] ?? '#6c757d' }}; color: white; padding: 8px 16px; font-size: 14px;">
                                {{ $statusText[$contrat->statut] ?? $contrat->statut }}
                            </span>
                        </div>
                             <br>
                        <span class="label">Montant</span>
                        <div style="font-size: 16px; font-weight: 800; color: #198754; margin-bottom: 15px;">
                            {{ number_format($contrat->montant, 0, ',', ' ') }} FCFA
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"> Partenaire Associé</div>
                    <div class="card-body">
                @if($contrat->partenaire)
                    <div>
                        <div style="font-weight: 600;">{{ $contrat->partenaire->prenom ?? '' }} {{ $contrat->partenaire->name ?? $contrat->partenaire->nom ?? '' }}</div>
                        <div style="font-size: 9px; color: #64748b;"><i class="bi bi-envelope"></i> {{ $contrat->partenaire->email }}</div>
                    </div>
                @else
                            <div style="color: #94a3b8; text-align: center; padding: 15px;">N/A</div>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"> Projet Associé</div>
                    <div class="card-body">
                        @if($contrat->projet)
                            <div style="font-weight: 600; margin-bottom: 10px;">{{ $contrat->projet->nom }}</div>
                        @else
                            <div style="color: #94a3b8; text-align: center; padding: 15px;">N/A</div>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"> Historique</div>
                    <div class="card-body">
                        <span class="label">Créé le</span>
                        <span class="value">{{ $contrat->created_at->format('d/m/Y') }}</span>
                        <span class="label">Dernière modification</span>
                        <span class="value">{{ $contrat->updated_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Document de contrat généré par CNRST Suivi Travaux - &copy; {{ date('Y') }}
    </div>
</body>
</html>
