<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste Complète des Sous-Traitances</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #fff;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            margin-bottom: 30px;
            border-radius: 5px;
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: #f0f0f0;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            border-left: 4px solid #667eea;
        }
        
        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #667eea;
        }
        
        .stat-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 5px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
            text-transform: uppercase;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        table thead {
            background: #667eea;
            color: white;
            border-bottom: 3px solid #5568d3;
        }
        
        table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 13px;
        }
        
        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        table tbody tr:hover {
            background-color: #f0f0f0;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
            color: white;
        }
        
        .status-en_attente { background-color: #6c757d; }
        .status-en_cours { background-color: #007bff; }
        .status-terminee { background-color: #28a745; }
        .status-annule { background-color: #dc3545; }
        .status-actif { background-color: #17a2b8; }
        
        .amount {
            font-weight: bold;
            color: #667eea;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            text-align: center;
            color: #999;
            font-size: 12px;
        }
        
        .date-generated {
            color: #999;
            font-size: 11px;
            margin-top: 10px;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="header">
            <h1>Liste Complète des Sous-Traitances</h1>
            <p>Rapport de tous les services de sous-traitance enregistrés</p>
        </div>
        
        <!-- Statistiques -->
        <div class="stats">
            <div class="stat-card">
                <div class="stat-value">{{ count($sousTraitances) }}</div>
                <div class="stat-label">Total</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $sousTraitances->where('statut', 'en_cours')->count() }}</div>
                <div class="stat-label">En Cours</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $sousTraitances->where('statut', 'terminee')->count() }}</div>
                <div class="stat-label">Terminées</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ number_format($sousTraitances->sum('montant_contrat'), 0, ',', ' ') }} FCFA</div>
                <div class="stat-label">Budget Total</div>
            </div>
        </div>
        
        <!-- Tableau des Sous-Traitances -->
        <div class="section-title">Détail des Sous-Traitances</div>
        
        @if($sousTraitances->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Entreprise</th>
                        <th>Projet</th>
                        <th>Contact</th>
                        <th>Employés</th>
                        <th>Montant</th>
                        <th>Statut</th>
                        <th>Dates</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sousTraitances as $st)
                    <tr>
                        <td>
                            <strong>{{ $st->nom_entreprise }}</strong>
                            @if($st->description_tache)
                            <br><small style="color: #999;">{{ Str::limit($st->description_tache, 40) }}</small>
                            @endif
                        </td>
                        <td>{{ $st->projet?->nom ?? 'N/A' }}</td>
                        <td>
                            @if($st->contact_nom)
                            {{ $st->contact_prenom ?? '' }} {{ $st->contact_nom }}<br>
                            <small style="color: #999;">{{ $st->contact_email ?? 'N/A' }}</small>
                            @else
                            N/A
                            @endif
                        </td>
                        <td style="text-align: center;">{{ $st->nombre_employes ?? '-' }}</td>
                        <td class="amount">
                            {{ $st->montant_contrat ? number_format($st->montant_contrat, 0, ',', ' ') . ' FCFA' : '-' }}
                        </td>
                        <td>
                            <span class="status-badge status-{{ $st->statut ?? 'actif' }}">
                                @php
                                $statusText = [
                                    'en_attente' => 'En attente',
                                    'en_cours' => 'En cours',
                                    'terminee' => 'Terminée',
                                    'annule' => 'Annulée',
                                    'actif' => 'Actif'
                                ][$st->statut] ?? ucfirst($st->statut ?? 'N/A');
                                @endphp
                                {{ $statusText }}
                            </span>
                        </td>
                        <td style="font-size: 12px;">
                            @if($st->date_debut)
                            Du {{ \Carbon\Carbon::parse($st->date_debut)->format('d/m/Y') }}
                            @if($st->date_fin)
                            au {{ \Carbon\Carbon::parse($st->date_fin)->format('d/m/Y') }}
                            @endif
                            @else
                            -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <p>Aucune sous-traitance enregistrée</p>
            </div>
        @endif
        
        <div class="footer">
            <p>Document confidentiel - Service de Suivi des Travaux</p>
            <div class="date-generated">
                Généré le {{ now()->format('d/m/Y à H:i:s') }}
            </div>
        </div>
    </div>
</body>
</html>
