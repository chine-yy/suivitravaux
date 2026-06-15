<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Intervention - #{{ $intervention->id }}</title>
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
        
        .badge { display: inline-block; padding: 4px 12px; border-radius: 6px; font-size: 9px; font-weight: 800; color: #ffffff; text-transform: uppercase; }
        
        .status-badge { padding: 4px 12px; border-radius: 6px; font-size: 9px; font-weight: 800; text-transform: uppercase; border: 1px solid; }
        .status-en_attente { color: #64748b; border-color: #e2e8f0; background-color: #f1f5f9; }
        .status-en_cours { color: #3b82f6; border-color: #3b82f6; background-color: rgba(59, 130, 246, 0.05); }
        .status-termine { color: #009A44; border-color: #009A44; background-color: rgba(16, 185, 129, 0.05); }

        .section-title { font-size: 14px; font-weight: 800; color: #0f172a; margin-bottom: 10px; border-bottom: 2px solid #009A44; padding-bottom: 5px; display: inline-block; }
        .content-box { background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0; color: #334155; line-height: 1.6; white-space: pre-line; min-height: 100px; margin-bottom: 20px; }
        
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
        <div class="page-title">Fiche d'Intervention #{{ $intervention->numero ?? $intervention->id }}</div>
    </div>

    <table class="main-container">
        <tr>
            <td class="left-col">
                <div class="card">
                    <div class="card-header">Détails de l'Intervention</div>
                    <div class="card-body">
                        <div class="section-title">Description des besoins</div>
                        <div class="content-box">{{ $intervention->description ?? 'Aucune description fournie.' }}</div>

                        @if($intervention->rapport)
                        <div class="section-title">Rapport d'intervention</div>
                        <div class="content-box" style="border-left: 4px solid #009A44;">{{ $intervention->rapport }}</div>
                        @endif
                    </div>
                </div>
            </td>

            <td class="right-col">
                <div class="card">
                    <div class="card-header">Informations Générales</div>
                    <div class="card-body">
                        <span class="label">Projet & Partenaire</span>
                        <div class="meta-item">
                            <div class="meta-icon">⌂</div>
                            <div>
                                <div style="font-weight: 700; font-size: 11px;">{{ $intervention->projet->nom ?? 'N/A' }}</div>
                                <div style="font-size: 9px; color: #64748b;">{{ $intervention->partenaire->name ?? $intervention->partenaire->nom ?? 'Partenaire N/A' }}</div>
                            </div>
                        </div>

                        <span class="label">Technicien assigné</span>
                        <div class="meta-item">
                            <div class="meta-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;"></div>
                            <div>
                                <div style="font-weight: 700; font-size: 11px;">{{ $intervention->technicien->name ?? '' }}</div>
                                <div style="font-size: 9px; color: #64748b;">{{ $intervention->technicien->role->nom ?? 'Technicien' }}</div>
                            </div>
                        </div>

                        <span class="label">Type d'Intervention</span>
                        <div class="meta-item">
                            <div class="meta-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;"></div>
                            <div style="font-weight: 700; font-size: 11px; margin-top: 4px;">
                                {{ $intervention->type === 'autre' ? ($intervention->type_autre ?? 'Autre') : ucfirst($intervention->type) }}
                            </div>
                        </div>

                        <span class="label">Statut & Date</span>
                        <div style="margin-bottom: 15px;">
                            @php
                                $statusClass = [
                                    'en_attente' => 'status-en_attente',
                                    'en_cours' => 'status-en_cours',
                                    'termine' => 'status-termine'
                                ][$intervention->statut] ?? 'status-en_attente';
                            @endphp
                            <span class="status-badge {{ $statusClass }}">{{ str_replace('_', ' ', $intervention->statut) }}</span>
                        </div>
                        <div style="font-weight: 700; color: #334155;">
                             {{ $intervention->date_intervention ? $intervention->date_intervention->format('d/m/Y à H:i') : 'N/A' }}
                        </div>
                    </div>
                </div>

                @if($intervention->tache || $intervention->sousTache)
                <div class="card">
                    <div class="card-header">Lien Hiérarchique</div>
                    <div class="card-body">
                        @if($intervention->tache)
                        <span class="label">Tâche</span>
                        <div style="font-weight: 600; font-size: 10px; margin-bottom: 10px;">{{ $intervention->tache->titre }}</div>
                        @endif

                        @if($intervention->sousTache)
                        <span class="label">Sous-Tâche</span>
                        <div style="font-weight: 600; font-size: 10px;">{{ $intervention->sousTache->titre }}</div>
                        @endif
                    </div>
                </div>
                @endif
            </td>
        </tr>
    </table>

    <div class="footer">
        Fiche d'intervention générée par CNRST Suivi Travaux - &copy; {{ date('Y') }}
    </div>
</body>
</html>
