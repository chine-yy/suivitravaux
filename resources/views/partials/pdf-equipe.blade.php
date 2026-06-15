<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Détails de l'Équipe - {{ $equipe->nom }}</title>
    <style>
        @page { margin: 0; size: A4; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1e293b; background-color: #ffffff; margin: 0; padding: 40px; font-size: 11px; line-height: 1.5; }

        .header { border-bottom: 2px solid #009A44; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: bold; color: #009A44; margin-bottom: 5px; }
        .page-title { font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 5px; }
        .timestamp { text-align: right; color: #94a3b8; font-size: 9px; }

        .section-title { font-size: 14px; font-weight: 700; color: #1e293b; border-left: 4px solid #009A44; padding-left: 10px; margin: 25px 0 15px 0; background: #f8fafc; padding-top: 5px; padding-bottom: 5px; }

        .info-grid { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-grid td { padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
        .label { font-weight: 700; color: #64748b; width: 30%; }
        .value { color: #0f172a; }

        .chef-card { background: #fef3c7; border: 1px solid #84cc16; border-radius: 8px; padding: 15px; margin-bottom: 20px; }
        .chef-title { color: #d97706; font-weight: 800; font-size: 10px; text-transform: uppercase; margin-bottom: 5px; }
        .chef-name { font-size: 16px; font-weight: 700; color: #92400e; }

        table.members-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.members-table thead tr { background: #f8fafc; }
        table.members-table thead th { padding: 10px; font-size: 10px; font-weight: 700; color: #475569; text-align: left; border-bottom: 2px solid #e2e8f0; }
        table.members-table tbody td { padding: 10px; border-bottom: 1px solid #f1f5f9; }

        .badge { padding: 2px 8px; border-radius: 4px; font-size: 9px; font-weight: 700; color: white; }
        .badge-success { background-color: #009A44; }
        .badge-warning { background-color: #84cc16; }

        .footer { position: fixed; bottom: 30px; left: 40px; right: 40px; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td><div class="logo">Suivi Travaux</div></td>
                <td class="timestamp">Exporté le {{ now()->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
        <div class="page-title">Fiche Équipe : {{ $equipe->nom }}</div>
    </div>

    <div class="section-title">Informations Générales</div>
    <table class="info-grid">
        <tr>
            <td class="label">Projet Associé</td>
            <td class="value">{{ $equipe->projet->nom ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Statut</td>
            <td class="value">
                <span class="badge {{ $equipe->statut === 'active' ? 'badge-success' : '' }}" style="color: {{ $equipe->statut === 'active' ? 'white' : '#64748b' }}; background-color: {{ $equipe->statut === 'active' ? '#009A44' : '#e2e8f0' }}">
                    {{ ucfirst($equipe->statut) }}
                </span>
            </td>
        </tr>
        <tr>
            <td class="label">Description</td>
            <td class="value">{{ $equipe->description ?? 'Aucune description.' }}</td>
        </tr>
        <tr>
            <td class="label">Date de Création</td>
            <td class="value">{{ $equipe->created_at->format('d/m/Y') }}</td>
        </tr>
    </table>

    <div class="section-title">Responsable</div>
    @if($equipe->chef)
        <div class="chef-card">
            <div class="chef-title">Chef d'Équipe</div>
            <div class="chef-name">{{ $equipe->chef->name }} {{ $equipe->chef->prenom ?? '' }}</div>
            <div style="margin-top: 5px; color: #92400e;">
                <span style="margin-right: 15px;">Email: {{ $equipe->chef->email }}</span>
                <span>Tél: {{ $equipe->chef->telephone ?? 'N/A' }}</span>
            </div>
        </div>
    @else
        <p style="color: #ef4444;">Aucun chef d'équipe assigné.</p>
    @endif

    <div class="section-title">Membres de l'Équipe ({{ $equipe->users->count() }})</div>
    <table class="members-table">
        <thead>
            <tr>
                <th>Nom & Prénom</th>
                <th>Email</th>
                <th>Téléphone</th>
            </tr>
        </thead>
        <tbody>
            @foreach($equipe->users as $member)
                <tr>
                    <td>
                        <strong>{{ $member->name }} {{ $member->prenom ?? '' }}</strong>
                        @if($equipe->chef_equipe_id == $member->id)
                            <span class="badge badge-warning" style="margin-left: 5px;">CHEF</span>
                        @endif
                    </td>
                    <td>{{ $member->email }}</td>
                    <td>{{ $member->telephone ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Fiche Équipe générée par Suivi Travaux — &copy; {{ date('Y') }}
    </div>
</body>
</html>
