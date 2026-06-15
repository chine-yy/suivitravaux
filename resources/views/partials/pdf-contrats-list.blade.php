<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Liste des Contrats</title>
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
        table.main-table thead tr { background: #1e293b; color: #ffffff; }
        table.main-table thead th { padding: 8px 10px; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; text-align: left; }
        table.main-table tbody tr:nth-child(even) { background: #f8fafc; }
        table.main-table tbody tr:nth-child(odd) { background: #ffffff; }
        table.main-table tbody td { padding: 7px 10px; border-bottom: 1px solid #e2e8f0; font-size: 9.5px; vertical-align: middle; }

        .badge { padding: 3px 8px; border-radius: 4px; font-size: 8.5px; font-weight: 700; color: white; display: inline-block; }
        .badge-secondary { background-color: #6c757d; }
        .badge-info { background-color: #0dcaf0; color: #000 !important; }
        .badge-primary { background-color: #0d6efd; }
        .badge-success { background-color: #198754; }
        .badge-danger { background-color: #dc3545; }
        .badge-light { background-color: #e2e8f0; color: #334155 !important; }

        .footer { position: fixed; bottom: 20px; left: 40px; right: 40px; text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; }
        .montant { font-weight: 700; color: #198754; }
        .no-data { text-align: center; padding: 40px; color: #94a3b8; font-style: italic; }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td><div class="logo">CNRST</div></td>
                <td style="text-align: right; color: #94a3b8; font-size: 8px;">Généré le {{ now()->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
        <div class="page-title">Liste des Contrats</div>
        <div class="page-subtitle">Récapitulatif complet de tous les contrats — {{ $contrats->count() }} contrat(s) trouvé(s)</div>
    </div>

    @php
        $statusText = ['brouillon' => 'Brouillon', 'signe' => 'Signé', 'en_cours' => 'En cours', 'termine' => 'Terminé', 'annule' => 'Annulé'];
        $statusBadge = ['brouillon' => 'badge-secondary', 'signe' => 'badge-info', 'en_cours' => 'badge-primary', 'termine' => 'badge-success', 'annule' => 'badge-danger'];
        $types = ['prestation' => 'Prestation', 'marche' => 'Marché', 'sous_traitance' => 'Sous-traitance', 'autre' => 'Autre'];
        $totalMontant = $contrats->sum('montant');
        $signes = $contrats->where('statut', 'signe')->count();
        $enCours = $contrats->where('statut', 'en_cours')->count();
        $termines = $contrats->where('statut', 'termine')->count();
    @endphp

    <table class="stats-grid">
        <tr>
            <td class="stat-card">
                <div class="stat-value">{{ $contrats->count() }}</div>
                <div class="stat-label">Total Contrats</div>
            </td>
            <td class="stat-card highlight">
                <div class="stat-value">{{ number_format($totalMontant, 0, ',', ' ') }}</div>
                <div class="stat-label">Montant Total (FCFA)</div>
            </td>
            <td class="stat-card">
                <div class="stat-value">{{ $enCours }}</div>
                <div class="stat-label">En Cours</div>
            </td>
            <td class="stat-card">
                <div class="stat-value">{{ $termines }}</div>
                <div class="stat-label">Terminés</div>
            </td>
        </tr>
    </table>

    @if($contrats->isEmpty())
        <div class="no-data">Aucun contrat trouvé.</div>
    @else
        <table class="main-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>N° Contrat</th>
                    <th>Partenaire</th>
                    <th>Projet</th>
                    <th>Type</th>
                    <th>Montant (FCFA)</th>
                    <th>Date Début</th>
                    <th>Date Fin</th>
                    <th>Statut</th>
                    <th>Créé le</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contrats as $i => $contrat)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><strong>{{ $contrat->numero_contrat ?? 'N/A' }}</strong></td>
                        <td>
                            @if($contrat->partenaire)
                                {{ $contrat->partenaire->prenom ?? '' }} {{ $contrat->partenaire->name ?? $contrat->partenaire->nom ?? '' }}
                            @else
                                <span style="color:#94a3b8;">N/A</span>
                            @endif
                        </td>
                        <td>{{ $contrat->projet->nom ?? 'N/A' }}</td>
                        <td><span class="badge badge-light">{{ $types[$contrat->type] ?? $contrat->type }}</span></td>
                        <td class="montant">{{ number_format($contrat->montant, 0, ',', ' ') }}</td>
                        <td>{{ $contrat->date_debut ? date('d/m/Y', strtotime($contrat->date_debut)) : 'N/A' }}</td>
                        <td>{{ $contrat->date_fin ? date('d/m/Y', strtotime($contrat->date_fin)) : 'N/A' }}</td>
                        <td>
                            <span class="badge {{ $statusBadge[$contrat->statut] ?? 'badge-secondary' }}">
                                {{ $statusText[$contrat->statut] ?? $contrat->statut }}
                            </span>
                        </td>
                        <td>{{ $contrat->created_at->format('d/m/Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        Liste des Contrats générée par CNRST Suivi Travaux — &copy; {{ date('Y') }}
    </div>
</body>
</html>
