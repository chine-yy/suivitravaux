<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Satisfaction - Enquête #{{ $satisfaction->id }}</title>
    <style>
        @page { margin: 0; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1e293b; background-color: #f8fafc; margin: 0; padding: 40px; font-size: 11px; }
        .header { margin-bottom: 25px; }
        .logo { font-size: 24px; font-weight: bold; color: #009A44; margin-bottom: 5px; }
        .page-title { font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 2px; }
        .page-subtitle { color: #64748b; font-size: 13px; margin-bottom: 25px; }

        .stats-grid { width: 100%; margin-bottom: 25px; border-collapse: separate; border-spacing: 12px 0; margin-left: -12px; }
        .stat-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; text-align: center; width: 50%; }
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
        <div class="page-title">Enquête de Satisfaction #{{ $satisfaction->id }}</div>
        <div class="page-subtitle">Retour partenaire</div>
    </div>

    <table class="stats-grid">
        <tr>
            <td class="stat-card" style="border-top: 3px solid #009A44;">
                <div class="stat-value">
                    @for($i = 1; $i <= 5; $i++)
                        <span style="color: {{ $i <= $satisfaction->note ? '#ffc107' : '#e2e8f0' }};"></span>
                    @endfor
                </div>
                <div class="stat-label">Note: {{ $satisfaction->note }}/5</div>
            </td>
            <td class="stat-card">
                @php
                    $statutText = [
                        'envoye' => 'Envoyé',
                        'repondu' => 'Répondu',
                        'expire' => 'Expiré'
                    ];
                @endphp
                <div class="stat-value">{{ $statutText[$satisfaction->statut] ?? $satisfaction->statut }}</div>
                <div class="stat-label">Statut</div>
            </td>
        </tr>
    </table>

    <table class="main-container">
        <tr>
            <td class="left-col">
                <div class="card">
                    <div class="card-header"> Détails de l'Enquête</div>
                    <div class="card-body">
                        <table style="width: 100%;">
                            <tr>
                                <td style="width: 50%;">
                                    <span class="label">ID Enquête</span>
                                    <span class="value">#{{ $satisfaction->id }}</span>
                                </td>
                                <td style="width: 50%;">
                                    <span class="label">Note</span>
                                    <span class="value">{{ $satisfaction->note }}/5</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="label">Date d'envoi</span>
                                    <span class="value">{{ $satisfaction->date_envoi ? date('d/m/Y', strtotime($satisfaction->date_envoi)) : 'Non envoyée' }}</span>
                                </td>
                                <td>
                                    <span class="label">Date de réponse</span>
                                    <span class="value">{{ $satisfaction->date_reponse ? date('d/m/Y', strtotime($satisfaction->date_reponse)) : 'En attente' }}</span>
                                </td>
                            </tr>
                        </table>

                        @if($satisfaction->commentaire)
                        <span class="label">Commentaire</span>
                        <div style="background: #f8fafc; padding: 15px; border-radius: 8px; color: #475569; line-height: 1.6; min-height: 60px; font-style: italic;">
                            "{!! nl2br(e($satisfaction->commentaire)) !!}"
                        </div>
                        @endif
                    </div>
                </div>
            </td>

            <td class="right-col">
                <div class="card">
                    <div class="card-header"> Partenaire</div>
                    <div class="card-body">
                        @if($satisfaction->partenaire)
                            <div style="margin-bottom: 10px;">
                                <div style="font-weight: 600; font-size: 14px;">{{ trim($satisfaction->partenaire->prenom . ' ' . $satisfaction->partenaire->nom) }}</div>
                                <div style="font-size: 9px; color: #64748b;">{{ $satisfaction->partenaire->email }}</div>
                            </div>
                        @else
                            <div style="color: #94a3b8; text-align: center; padding: 15px;">Aucun partenaire associé</div>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"> Projet</div>
                    <div class="card-body">
                        @if($satisfaction->projet)
                            <div style="font-weight: 600;">{{ $satisfaction->projet->nom }}</div>
                            <div style="font-size: 9px; color: #64748b;">
                                <span class="badge {{ $satisfaction->projet->statut == 'termine' ? 'bg-success' : 'bg-primary' }}">
                                    {{ ucfirst($satisfaction->projet->statut) }}
                                </span>
                            </div>
                        @else
                            <div style="color: #94a3b8; text-align: center; padding: 15px;">Aucun projet associé</div>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"> Historique</div>
                    <div class="card-body">
                        <span class="label">Créé le</span>
                        <span class="value">{{ $satisfaction->created_at->format('d/m/Y') }}</span>
                        <span class="label">Dernière modification</span>
                        <span class="value">{{ $satisfaction->updated_at->format('d/m/Y') }}</span>
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
