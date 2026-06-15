<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Détails de l'Incident - #{{ $incident->id }}</title>
    <style>
        @page { margin: 0; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1e293b; background-color: #f8fafc; margin: 0; padding: 40px; font-size: 11px; }
        .header { margin-bottom: 25px; }
        .logo { font-size: 24px; font-weight: bold; color: #009A44; margin-bottom: 5px; }
        .page-title { font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 5px; }
        
        .main-container { width: 100%; border-collapse: separate; border-spacing: 20px 0; margin-left: -20px; }
        .left-col { width: 62%; vertical-align: top; }
        .right-col { width: 38%; vertical-align: top; }
        
        .card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px; overflow: hidden; }
        .card-header { border-bottom: 1px solid #e2e8f0; padding: 12px 20px; font-weight: 800; color: #334155; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
        .card-body { padding: 20px; }
        
        .label { color: #64748b; font-weight: 700; font-size: 9px; text-transform: uppercase; margin-bottom: 6px; display: block; }
        .value { color: #0f172a; font-weight: 600; font-size: 11px; margin-bottom: 20px; display: block; }
        
        .badge { display: inline-block; padding: 4px 12px; border-radius: 6px; font-size: 9px; font-weight: 800; color: #ffffff; text-transform: uppercase; }
        .badge-outline { border: 1px solid; background: transparent; }
        
        .bg-info { background-color: #06b6d4; }
        .bg-warning { background-color: #84cc16; color: #1e293b; }
        .bg-danger { background-color: #ef4444; }
        .bg-success { background-color: #009A44; }
        .bg-secondary { background-color: #64748b; }
        
        .status-badge { padding: 4px 12px; border-radius: 6px; font-size: 9px; font-weight: 800; text-transform: uppercase; border: 1px solid; }
        .status-ouvert { color: #ef4444; border-color: #ef4444; background-color: rgba(239, 68, 68, 0.05); }
        .status-traitement { color: #84cc16; border-color: #84cc16; background-color: rgba(245, 158, 11, 0.05); }
        .status-resolu { color: #009A44; border-color: #009A44; background-color: rgba(16, 185, 129, 0.05); }

        .incident-title { font-size: 16px; font-weight: 800; color: #0f172a; margin-bottom: 15px; }
        .incident-box { background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; color: #334155; line-height: 1.8; white-space: pre-line; min-height: 200px; }
        
        .meta-item { display: flex; align-items: flex-start; margin-bottom: 20px; }
        .meta-icon { width: 32px; height: 32px; background: rgba(0, 154, 68, 0.1); color: #009A44; border-radius: 6px; text-align: center; line-height: 32px; font-weight: bold; margin-right: 12px; font-size: 14px; }
        
        .footer { position: fixed; bottom: 20px; text-align: center; width: 100%; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td><div class="logo">CNRST</div></td>
                <td style="text-align: right; color: #94a3b8; font-size: 10px;">Généré le {{ now()->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
        <div class="page-title">Détails de l'Incident</div>
    </div>

    <table class="main-container">
        <tr>
            <td class="left-col">
                <div class="card">
                    <div class="card-header">Description de l'Incident</div>
                    <div class="card-body">
                        <div class="incident-title">{{ $incident->titre }}</div>
                        <div class="incident-box">{{ $incident->description }}</div>
                    </div>
                </div>
            </td>

            <td class="right-col">
                <div class="card">
                    <div class="card-header">Informations Clés</div>
                    <div class="card-body">
                        <span class="label">Projet</span>
                        <div class="meta-item">
                            <div class="meta-icon" style="background: rgba(0, 154, 68, 0.1); color: #009A44;">⌂</div>
                            <div style="font-weight: 700; font-size: 12px; margin-top: 6px;">{{ $incident->projet->nom ?? 'N/A' }}</div>
                        </div>

                        <span class="label">Signalé par</span>
                        <div class="meta-item">
                            <div class="meta-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;"></div>
                            <div>
                                <div style="font-weight: 700; font-size: 11px;">{{ $incident->signalePar->name ?? 'Système' }}</div>
                                <div style="font-size: 9px; color: #64748b;">{{ $incident->signalePar->role->nom ?? '' }}</div>
                            </div>
                        </div>

                        <span class="label">Gravité</span>
                        <div style="margin-bottom: 20px;">
                            @php
                                $gravityClass = [
                                    'faible' => 'bg-info',
                                    'moyen' => 'bg-warning',
                                    'critique' => 'bg-danger'
                                ][$incident->gravite] ?? 'bg-secondary';
                            @endphp
                            <span class="badge {{ $gravityClass }}">{{ $incident->gravite }}</span>
                        </div>

                        <span class="label">Statut Actuel</span>
                        <div style="margin-bottom: 20px;">
                            @php
                                $statusClass = [
                                    'ouvert' => 'status-ouvert',
                                    'en_traitement' => 'status-traitement',
                                    'resolu' => 'status-resolu'
                                ][$incident->statut] ?? 'bg-secondary';
                            @endphp
                            <span class="status-badge {{ $statusClass }}">{{ str_replace('_', ' ', $incident->statut) }}</span>
                        </div>

                        <span class="label">Date de Signalement</span>
                        <div style="font-weight: 700;">
                             {{ $incident->created_at->format('d/m/Y à H:i') }}
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Rapport d'incident généré par CNRST - &copy; {{ date('Y') }}
    </div>
</body>
</html>
