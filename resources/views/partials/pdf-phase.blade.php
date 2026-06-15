<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Détails de la Phase - {{ $phase->nom }}</title>
    <style>
        @page { margin: 0; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1e293b; background-color: #f8fafc; margin: 0; padding: 40px; font-size: 12px; }
        .header { margin-bottom: 25px; }
        .logo { font-size: 24px; font-weight: bold; color: #009A44; margin-bottom: 5px; }
        .page-title { font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 2px; }
        .page-subtitle { color: #64748b; font-size: 13px; margin-bottom: 20px; }
        .card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); overflow: hidden; }
        .card-header { background: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 12px 20px; font-weight: 700; color: #334155; font-size: 13px; display: flex; align-items: center; }
        .card-header-icon { color: #009A44; margin-right: 8px; font-weight: bold; }
        .card-body { padding: 20px; }
        .grid-3 { width: 100%; border-collapse: collapse; }
        .grid-3 td { width: 33.33%; vertical-align: top; padding: 0 10px 15px 0; }
        .label { color: #64748b; font-weight: 700; font-size: 10px; text-transform: uppercase; margin-bottom: 4px; display: block; }
        .value { color: #0f172a; font-weight: 600; font-size: 12px; }
        .description-box { background: #f1f5f9; padding: 15px; border-radius: 8px; color: #475569; line-height: 1.6; margin-top: 10px; }
        .task-item { border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 15px; margin-bottom: 10px; }
        .task-title { font-weight: 700; color: #0f172a; margin-bottom: 2px; }
        .task-meta { color: #64748b; font-size: 10px; }
        .footer { position: fixed; bottom: 20px; left: 40px; right: 40px; text-align: center; font-size: 10px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
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
        <div class="page-title">Phase : {{ $phase->nom }}</div>
        <div class="page-subtitle">Projet : {{ $phase->projet->nom ?? 'N/A' }}</div>
    </div>

    <div class="card">
        <div class="card-header">
             Informations
        </div>
        <div class="card-body">
            <table class="grid-3">
                <tr>
                    <td>
                        <span class="label">Date de début</span>
                        <span class="value">{{ optional($phase->date_debut)->format('d/m/Y') ?? '-' }}</span>
                    </td>
                    <td>
                        <span class="label">Date fin prévue</span>
                        <span class="value">{{ optional($phase->date_fin_prevue)->format('d/m/Y') ?? '-' }}</span>
                    </td>
                    <td>
                        <span class="label">Avancement</span>
                        <span class="value">{{ $phase->avancement ?? 0 }}%</span>
                    </td>
                </tr>
            </table>
            <div class="label" style="margin-top: 10px;">Description</div>
            <div class="description-box">
                {{ $phase->description ?: 'Aucune description renseignée.' }}
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
             Tâches liées
        </div>
        <div class="card-body">
            @forelse($phase->taches as $tache)
                <div class="task-item">
                    <div class="task-title">{{ $tache->titre }}</div>
                    <div class="task-meta">Projet: {{ $tache->projet->nom ?? 'N/A' }} | Statut: {{ ucfirst(str_replace('_', ' ', $tache->statut)) }}</div>
                </div>
            @empty
                <div style="color: #94a3b8; font-style: italic;">Aucune tâche liée à cette phase.</div>
            @endforelse
        </div>
    </div>

    <div class="footer">
        Document officiel généré par CNRST Suivi Travaux - &copy; {{ date('Y') }}
    </div>
</body>
</html>
