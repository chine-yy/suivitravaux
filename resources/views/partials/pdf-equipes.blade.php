<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Liste des Équipes</title>
    <style>
        @page { margin: 0; size: A4 landscape; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1e293b; background-color: #f8fafc; margin: 0; padding: 30px 40px; font-size: 10px; }

        .header { margin-bottom: 20px; }
        .logo { font-size: 22px; font-weight: bold; color: #009A44; margin-bottom: 4px; }
        .page-title { font-size: 20px; font-weight: 800; color: #0f172a; margin-bottom: 2px; }
        .page-subtitle { color: #64748b; font-size: 11px; margin-bottom: 20px; }

        .stats-grid { width: 100%; margin-bottom: 20px; border-collapse: separate; border-spacing: 12px 0; margin-left: -12px; }
        .stat-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px; text-align: center; width: 25%; }
        .stat-card.highlight { border-top: 3px solid #009A44; }
        .stat-value { font-size: 16px; font-weight: bold; color: #0f172a; margin-bottom: 3px; }
        .stat-label { font-size: 8px; color: #64748b; text-transform: uppercase; font-weight: 800; letter-spacing: 0.5px; }

        table.main-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.main-table thead tr { background: #009A44; color: #ffffff; }
        table.main-table thead th { padding: 8px 10px; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; text-align: left; }
        table.main-table tbody tr:nth-child(even) { background: #f8fafc; }
        table.main-table tbody tr:nth-child(odd) { background: #ffffff; }
        table.main-table tbody td { padding: 7px 10px; border-bottom: 1px solid #e2e8f0; font-size: 9.5px; vertical-align: middle; }

        .badge { padding: 3px 8px; border-radius: 4px; font-size: 8.5px; font-weight: 700; color: white; display: inline-block; }
        .badge-success { background-color: #009A44; }
        .badge-secondary { background-color: #64748b; }
        .badge-primary { background-color: #009A44; }

        .footer { position: fixed; bottom: 20px; left: 40px; right: 40px; text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; }
        .no-data { text-align: center; padding: 40px; color: #94a3b8; font-style: italic; }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td><div class="logo">Suivi Travaux</div></td>
                <td style="text-align: right; color: #94a3b8; font-size: 8px;">Généré le {{ now()->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
        <div class="page-title">Liste des Équipes</div>
        <div class="page-subtitle">Récapitulatif de toutes les équipes de projet — {{ $equipes->count() }} équipe(s) trouvée(s)</div>
    </div>

    <table class="stats-grid">
        <tr>
            <td class="stat-card highlight">
                <div class="stat-value">{{ $equipes->count() }}</div>
                <div class="stat-label">Total Équipes</div>
            </td>
            <td class="stat-card">
                <div class="stat-value">{{ $equipes->where('statut', 'active')->count() }}</div>
                <div class="stat-label">Équipes Actives</div>
            </td>
            <td class="stat-card">
                <div class="stat-value">{{ $equipes->sum(fn($e) => $e->users->count()) }}</div>
                <div class="stat-label">Total Membres</div>
            </td>
            <td class="stat-card">
                <div class="stat-value">{{ $equipes->pluck('projet_id')->unique()->count() }}</div>
                <div class="stat-label">Projets Couverts</div>
            </td>
        </tr>
    </table>

    @if($equipes->isEmpty())
        <div class="no-data">Aucune équipe trouvée.</div>
    @else
        <table class="main-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Équipe</th>
                    <th>Projet</th>
                    <th>Chef d'Équipe</th>
                    <th>Membres</th>
                    <th>Statut</th>
                    <th>Créé le</th>
                </tr>
            </thead>
            <tbody>
                @foreach($equipes as $i => $equipe)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><strong>{{ $equipe->nom }}</strong></td>
                        <td>{{ $equipe->projet->nom ?? 'N/A' }}</td>
                        <td>{{ $equipe->chef->name ?? 'N/A' }}</td>
                        <td>{{ $equipe->users->count() }}</td>
                        <td>
                            <span class="badge {{ $equipe->statut === 'active' ? 'badge-success' : 'badge-secondary' }}">
                                {{ ucfirst($equipe->statut) }}
                            </span>
                        </td>
                        <td>{{ $equipe->created_at->format('d/m/Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        Liste des Équipes générée par Suivi Travaux — &copy; {{ date('Y') }}
    </div>
</body>
</html>
