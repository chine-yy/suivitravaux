<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Détails de la Tâche - {{ $tache->titre }}</title>
    <style>
        @page { margin: 0; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1e293b; background-color: #f8fafc; margin: 0; padding: 40px; font-size: 11px; }
        .header { margin-bottom: 25px; }
        .logo { font-size: 24px; font-weight: bold; color: #009A44; margin-bottom: 5px; }
        .page-title { font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 5px; }
        
        .stats-grid { width: 100%; margin-bottom: 20px; border-collapse: separate; border-spacing: 12px 0; margin-left: -12px; }
        .stat-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; text-align: center; width: 25%; }
        .stat-value { font-size: 18px; font-weight: bold; color: #0f172a; margin-bottom: 4px; }
        .stat-label { font-size: 9px; color: #64748b; text-transform: uppercase; font-weight: 800; }
        
        .main-container { width: 100%; border-collapse: separate; border-spacing: 20px 0; margin-left: -20px; }
        .left-col { width: 68%; vertical-align: top; }
        .right-col { width: 32%; vertical-align: top; }
        
        .card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px; overflow: hidden; }
        .card-header { border-bottom: 1px solid #e2e8f0; padding: 12px 20px; font-weight: 700; color: #334155; font-size: 12px; }
        .card-header-icon { color: #009A44; margin-right: 8px; }
        .card-body { padding: 20px; }
        
        .label { color: #64748b; font-weight: 700; font-size: 9px; text-transform: uppercase; margin-bottom: 4px; display: block; }
        .value { color: #0f172a; font-weight: 600; font-size: 11px; margin-bottom: 12px; display: block; }
        
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 9px; font-weight: 800; color: #ffffff; text-transform: uppercase; }
        .bg-primary { background-color: #3b82f6; }
        .bg-success { background-color: #009A44; }
        .bg-warning { background-color: #84cc16; }
        .bg-danger { background-color: #ef4444; }
        .bg-secondary { background-color: #64748b; }
        
        .progress-box { margin-bottom: 15px; }
        .progress-container { background: #e2e8f0; height: 10px; border-radius: 5px; overflow: hidden; margin-top: 5px; }
        .progress-bar { height: 100%; border-radius: 5px; }
        
        table.data-table { width: 100%; border-collapse: collapse; }
        table.data-table th { background: #f8fafc; text-align: left; padding: 8px 12px; font-size: 9px; color: #64748b; border-bottom: 1px solid #e2e8f0; }
        table.data-table td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-size: 10px; }
        
        .footer { position: fixed; bottom: 20px; left: 40px; right: 40px; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
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
        <div class="page-title">Tâche : {{ $tache->titre }}</div>
    </div>

    <table class="stats-grid">
        <tr>
            <td class="stat-card">
                <div class="stat-value">{{ $tache->sousTaches->count() }}</div>
                <div class="stat-label">Sous-tâches</div>
            </td>
            <td class="stat-card">
                <div class="stat-value">{{ $personnels->count() }}</div>
                <div class="stat-label">Personnels</div>
            </td>
            <td class="stat-card">
                <div class="stat-value">{{ $tache->avancement ?? 0 }}%</div>
                <div class="stat-label">Avancement</div>
            </td>
            <td class="stat-card" style="border-top: 3px solid #009A44;">
                <div class="stat-value" style="font-size: 12px;">{{ ucfirst(str_replace('_', ' ', $tache->statut)) }}</div>
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
                                    <span class="label">Titre</span>
                                    <span class="value" style="font-size: 13px; font-weight: 800;">{{ $tache->titre }}</span>
                                </td>
                                <td style="width: 50%;">
                                    <span class="label">Projet</span>
                                    <span class="value">{{ $tache->projet->nom ?? 'Non défini' }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="label">Phase</span>
                                    <span class="value">{{ $tache->phase->nom ?? 'Aucune phase' }}</span>
                                </td>
                                <td>
                                    <span class="label">Priorité</span>
                                    <span class="value">{{ ucfirst($tache->priorite ?? 'normale') }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="label">Date de début</span>
                                    <span class="value">{{ $tache->date_debut_prevue ? $tache->date_debut_prevue->format('d/m/Y') : 'Non définie' }}</span>
                                </td>
                                <td>
                                    <span class="label">Date de fin prévue</span>
                                    <span class="value">{{ $tache->date_fin_prevue ? $tache->date_fin_prevue->format('d/m/Y') : 'Non définie' }}</span>
                                </td>
                            </tr>
                        </table>
                        <span class="label">Description</span>
                        <div style="background: #f8fafc; padding: 15px; border-radius: 8px; color: #475569; line-height: 1.6;">
                            {!! nl2br(e($tache->description ?: 'Aucune description renseignée.')) !!}
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"> Sous-tâches</div>
                    <div class="card-body" style="padding: 0;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>SOUS-TÂCHE</th>
                                    <th>PERSONNE ASSIGNÉE</th>
                                    <th>STATUT</th>
                                    <th>AVANCEMENT</th>
                                    <th>DATE DE FIN</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tache->sousTaches as $sousTache)
                                    <tr>
                                        <td>
                                            <div style="font-weight: 700; color: #0f172a;">{{ $sousTache->titre }}</div>
                                            <div style="font-size: 8px; color: #64748b;">{{ $sousTache->description ?: 'Sans description' }}</div>
                                        </td>
                                        <td>
                                            @php
                                                $allPersonnels = collect();
                                                if ($sousTache->user) $allPersonnels->push($sousTache->user);
                                                if ($sousTache->personnels->isNotEmpty()) {
                                                    $allPersonnels = $allPersonnels->merge($sousTache->personnels)->unique('id');
                                                }
                                            @endphp
                                            @if($allPersonnels->isNotEmpty())
                                                @foreach($allPersonnels as $p)
                                                    <div style="font-weight: 600; font-size: 10px; margin-bottom: 3px;">{{ $p->name }} {{ $p->prenom ?? '' }}</div>
                                                    @if($p->role)
                                                        <div style="font-size: 8px; color: #64748b; margin-bottom: 4px;">{{ $p->role->nom }}</div>
                                                    @endif
                                                @endforeach
                                            @else
                                                <span style="color: #94a3b8; font-style: italic; font-size: 9px;">Non assignée</span>
                                            @endif
                                        </td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $sousTache->statut)) }}</td>
                                        <td>{{ $sousTache->avancement ?? 0 }}%</td>
                                        <td>{{ $sousTache->date_fin_prevue ? $sousTache->date_fin_prevue->format('d/m/Y') : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" style="text-align: center; color: #94a3b8; padding: 20px;">Aucune sous-tâche.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </td>

            <td class="right-col">
                <div class="card">
                    <div class="card-header"> Suivi rapide</div>
                    <div class="card-body">
                        <div class="progress-box">
                            <span class="label">Progression : {{ $tache->avancement ?? 0 }}%</span>
                            <div class="progress-container">
                                <div class="progress-bar bg-primary" style="width: {{ $tache->avancement ?? 0 }}%;"></div>
                            </div>
                        </div>
                        <div style="margin-bottom: 20px;">
                            <span class="label">Partenaire</span>
                            <span class="value">{{ $tache->projet->partenaire->nom ?? $tache->projet->partenaire->name ?? 'N/A' }}</span>
                            
                            <span class="label">Personnels assignés</span>
                            <span class="value">{{ $personnels->count() + (isset($sousTachePersonnels) ? $sousTachePersonnels->count() : 0) }}</span>
                        </div>
                        @if($personnels->isNotEmpty())
                            <span class="label">Personnel direct</span>
                            @foreach($personnels as $p)
                                <div style="margin-bottom: 6px; font-weight: 600;">• {{ $p->name }} {{ $p->prenom ?? '' }}
                                    @if(isset($p->role)) <span style="font-size: 9px; color: #64748b;">({{ $p->role->nom ?? '' }})</span>@endif
                                </div>
                            @endforeach
                        @endif
                        @if(isset($sousTachePersonnels) && $sousTachePersonnels->isNotEmpty())
                            <span class="label" style="margin-top: 10px;">Personnel par sous-tâche</span>
                            @foreach($sousTachePersonnels as $p)
                                <div style="margin-bottom: 6px; font-weight: 600;">• {{ $p->name }} {{ $p->prenom ?? '' }}
                                    @if($p->role) <span style="font-size: 9px; color: #64748b;">({{ $p->role->nom }})</span>@endif
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"> Historique</div>
                    <div class="card-body">
                        <span class="label">Créée le</span>
                        <span class="value">{{ $tache->created_at ? $tache->created_at->format('d/m/Y à H:i') : '-' }}</span>
                        <span class="label">Dernière modification</span>
                        <span class="value">{{ $tache->updated_at ? $tache->updated_at->format('d/m/Y à H:i') : '-' }}</span>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Document de suivi généré par CNRST - &copy; {{ date('Y') }}
    </div>
</body>
</html>
