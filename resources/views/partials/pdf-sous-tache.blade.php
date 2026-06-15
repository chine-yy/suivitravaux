<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Détails de la Sous-Tâche - {{ $sousTache->titre }}</title>
    <style>
        @page { margin: 0; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1e293b; background-color: #f8fafc; margin: 0; padding: 40px; font-size: 11px; }
        .header { margin-bottom: 25px; }
        .logo { font-size: 24px; font-weight: bold; color: #009A44; margin-bottom: 5px; }
        .page-title { font-size: 20px; font-weight: 800; color: #0f172a; margin-bottom: 5px; }
        
        .card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px; overflow: hidden; }
        .card-header { border-bottom: 1px solid #e2e8f0; padding: 12px 20px; font-weight: 700; color: #334155; font-size: 12px; }
        .card-header-icon { color: #009A44; margin-right: 8px; }
        .card-body { padding: 20px; }
        
        .label { color: #64748b; font-weight: 700; font-size: 9px; text-transform: uppercase; margin-bottom: 4px; display: block; }
        .value { color: #0f172a; font-weight: 600; font-size: 11px; margin-bottom: 15px; display: block; }
        
        .badge { display: inline-block; padding: 3px 10px; border-radius: 6px; font-size: 9px; font-weight: 800; color: #ffffff; }
        .bg-primary { background-color: #3b82f6; }
        .bg-success { background-color: #009A44; }
        .bg-danger { background-color: #ef4444; }
        .bg-secondary { background-color: #64748b; }
        
        .progress-box { margin-bottom: 15px; }
        .progress-container { background: #e2e8f0; height: 10px; border-radius: 5px; overflow: hidden; margin-top: 8px; }
        .progress-bar { height: 100%; border-radius: 5px; }
        
        .footer { position: fixed; bottom: 20px; text-align: center; width: 100%; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        table { width: 100%; border-collapse: collapse; }
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
        <div class="page-title">Sous-Tâche : {{ $sousTache->titre }}</div>
    </div>

    <div class="card">
        <div class="card-header"> Informations générales</div>
        <div class="card-body">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 50%;">
                        <span class="label">Titre</span>
                        <span class="value" style="font-size: 14px; font-weight: 800;">{{ $sousTache->titre }}</span>
                    </td>
                    <td style="width: 50%;">
                        <span class="label">Statut</span>
                        @php
                            $statusClass = [
                                'en_attente' => 'bg-secondary',
                                'en_cours' => 'bg-primary',
                                'terminee' => 'bg-success',
                                'bloquee' => 'bg-danger',
                            ][$sousTache->statut] ?? 'bg-secondary';
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $sousTache->statut)) }}</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label">Tâche parente</span>
                        <span class="value">{{ $sousTache->tache->titre ?? 'N/A' }}</span>
                    </td>
                    <td>
                        <span class="label">Projet</span>
                        <span class="value">{{ $sousTache->tache->projet->nom ?? 'N/A' }}</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label">Projet / Phase</span>
                        <span class="value">{{ $sousTache->tache->projet->nom ?? 'N/A' }}{{ $sousTache->tache && $sousTache->tache->phase ? ' / ' . $sousTache->tache->phase->nom : '' }}</span>
                    </td>
                    <td>
                        <div class="progress-box">
                            <span class="label">Avancement : {{ $sousTache->avancement ?? 0 }}%</span>
                            <div class="progress-container">
                                <div class="progress-bar bg-primary" style="width: {{ $sousTache->avancement ?? 0 }}%;"></div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label">Date de début</span>
                        <span class="value">{{ optional($sousTache->date_debut)->format('d/m/Y') ?? 'Non définie' }}</span>
                    </td>
                    <td>
                        <span class="label">Date de fin prévue</span>
                        <span class="value">{{ optional($sousTache->date_fin_prevue)->format('d/m/Y') ?? 'Non définie' }}</span>
                    </td>
                </tr>
            </table>
            <span class="label">Description</span>
            <div style="background: #f1f5f9; padding: 15px; border-radius: 8px; color: #475569; line-height: 1.6;">
                {{ $sousTache->description ?: 'Aucune description renseignée.' }}
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"> Personnels affectés</div>
        <div class="card-body">
            @php
                $allPersonnels = collect();
                if ($sousTache->user) $allPersonnels->push($sousTache->user);
                if ($personnels->isNotEmpty()) {
                    $allPersonnels = $allPersonnels->merge($personnels)->unique('id');
                }
            @endphp
            @forelse($allPersonnels as $personnel)
                <div style="margin-bottom: 10px; padding: 8px 12px; background: #f8fafc; border-radius: 6px; border-left: 3px solid #009A44;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 24px; vertical-align: top;">
                                <div style="width: 20px; height: 20px; border-radius: 50%; background: #009A44; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 700;">
                                    {{ strtoupper(substr($personnel->prenom ?? $personnel->name, 0, 1)) }}{{ strtoupper(substr($personnel->name, 0, 1)) }}
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 11px; font-weight: 700; color: #1e293b;">{{ $personnel->name }} {{ $personnel->prenom ?? '' }}</div>
                                @if($personnel->role)
                                    <div style="font-size: 9px; color: #64748b; margin-top: 2px;">{{ $personnel->role->nom }}</div>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            @empty
                <div style="color: #94a3b8; font-style: italic; text-align: center; padding: 20px;">Aucun personnel affecté.</div>
            @endforelse
        </div>
    </div>

    <div class="footer">
        Document de suivi généré par CNRST - &copy; {{ date('Y') }}
    </div>
</body>
</html>
