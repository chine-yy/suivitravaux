<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rendez-vous - #{{ $rendezvous->id }}</title>
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
        .card-header { border-bottom: 1px solid #e2e8f0; padding: 12px 20px; font-weight: 800; color: #334155; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
        .card-body { padding: 20px; }
        
        .label { color: #64748b; font-weight: 700; font-size: 9px; text-transform: uppercase; margin-bottom: 6px; display: block; }
        .value { color: #0f172a; font-weight: 600; font-size: 11px; margin-bottom: 20px; display: block; }
        
        .status-badge { padding: 4px 12px; border-radius: 6px; font-size: 9px; font-weight: 800; text-transform: uppercase; border: 1px solid; }
        .status-en_attente { color: #64748b; border-color: #e2e8f0; background-color: #f1f5f9; }
        .status-confirme { color: #3b82f6; border-color: #3b82f6; background-color: rgba(59, 130, 246, 0.05); }
        .status-termine { color: #009A44; border-color: #009A44; background-color: rgba(16, 185, 129, 0.05); }
        .status-annule { color: #ef4444; border-color: #ef4444; background-color: rgba(239, 68, 68, 0.05); }

        .rendezvous-title { font-size: 16px; font-weight: 800; color: #0f172a; margin-bottom: 15px; border-left: 4px solid #009A44; padding-left: 10px; }
        .content-box { background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; color: #334155; line-height: 1.8; white-space: pre-line; min-height: 150px; }
        
        .meta-item { display: flex; align-items: flex-start; margin-bottom: 15px; }
        .meta-icon { width: 24px; height: 24px; background: rgba(0, 154, 68, 0.1); color: #009A44; border-radius: 4px; text-align: center; line-height: 24px; font-weight: bold; margin-right: 10px; font-size: 12px; }
        
        .footer { position: fixed; bottom: 20px; text-align: center; width: 100%; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td><div class="logo">CNRST</div></td>
                <td style="text-align: right; color: #94a3b8; font-size: 10px;">{{ now()->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
        <div class="page-title">Fiche de Rendez-vous #{{ $rendezvous->id }}</div>
    </div>

    <table class="main-container">
        <tr>
            <td class="left-col">
                <div class="card">
                    <div class="card-header">Objet du Rendez-vous</div>
                    <div class="card-body">
                        <div class="rendezvous-title">{{ $rendezvous->titre }}</div>
                        <div class="content-box">{{ $rendezvous->description ?? 'Aucun détail supplémentaire.' }}</div>
                    </div>
                </div>
            </td>

            <td class="right-col">
                <div class="card">
                    <div class="card-header">Informations de Planification</div>
                    <div class="card-body">
                        <span class="label">Projet</span>
                        <div class="meta-item">
                            <div class="meta-icon"></div>
                            <div>
                                <div style="font-weight: 700; font-size: 11px;">{{ $rendezvous->projet->nom ?? 'N/A' }}</div>
                            </div>
                        </div>

                        <span class="label">Date & Heure</span>
                        <div class="meta-item">
                            <div class="meta-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;"></div>
                            <div>
                                <div style="font-weight: 700; font-size: 11px;">{{ $rendezvous->date_heure ? $rendezvous->date_heure->format('d/m/Y à H:i') : 'N/A' }}</div>
                                <div style="font-size: 9px; color: #64748b;">Durée estimée: {{ $rendezvous->duree_minutes ?? 0 }} min</div>
                            </div>
                        </div>

                        <span class="label">Lieu / Modalité</span>
                        <div class="meta-item">
                            <div class="meta-icon" style="background: rgba(16, 185, 129, 0.1); color: #009A44;"></div>
                            <div style="font-weight: 700; font-size: 11px; margin-top: 4px;">
                                {{ $rendezvous->lieu ?? 'Non spécifié' }}
                            </div>
                        </div>

                        <span class="label">Type de RDV</span>
                        <div class="meta-item">
                            <div class="meta-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">ℹ</div>
                            <div style="font-weight: 700; font-size: 11px; margin-top: 4px;">
                                {{ $rendezvous->type === 'autre' ? ($rendezvous->type_autre ?? 'Autre') : ucfirst($rendezvous->type) }}
                            </div>
                        </div>

                        <span class="label">Statut Actuel</span>
                        <div style="margin-bottom: 5px;">
                            @php
                                $statusClass = [
                                    'en_attente' => 'status-en_attente',
                                    'confirme' => 'status-confirme',
                                    'termine' => 'status-termine',
                                    'annule' => 'status-annule'
                                ][$rendezvous->statut] ?? 'status-en_attente';
                            @endphp
                            <span class="status-badge {{ $statusClass }}">{{ str_replace('_', ' ', $rendezvous->statut) }}</span>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Fiche de rendez-vous générée par CNRST Suivi Travaux - &copy; {{ date('Y') }}
    </div>
</body>
</html>
