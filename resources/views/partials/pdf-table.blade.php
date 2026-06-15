<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Export' }} - CNRST</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.4; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #009A44; padding-bottom: 10px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #777; border-top: 1px solid #eee; padding-top: 5px; }
        .logo { font-size: 20px; font-weight: bold; color: #009A44; margin-bottom: 3px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 10px; }
        th { background-color: #009A44; color: white; padding: 6px; text-align: left; font-size: 10px; }
        td { padding: 5px; border-bottom: 1px solid #eee; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 9px; font-weight: bold; color: #fff; }
        .bg-success { background-color: #009A44; }
        .bg-primary { background-color: #009A44; }
        .bg-warning { background-color: #eab308; color: #333; }
        .bg-danger { background-color: #ef4444; }
        .bg-secondary { background-color: #6b7280; }
        .bg-info { background-color: #06b6d4; }
        .bg-purple { background-color: #8b5cf6; }
        .bg-light { background-color: #f3f4f6; color: #333; }
        .section-title { color: #009A44; border-bottom: 2px solid #a5d6a7; margin-top: 20px; padding-bottom: 5px; font-size: 14px; font-weight: bold; }
        .role-block { border: 1px solid #a5d6a7; border-radius: 6px; padding: 10px; }
        .role-block + .role-block { margin-top: 14px; }
        .role-name { font-size: 13px; font-weight: bold; color: #111827; }
        .role-users { font-size: 10px; color: #374151; margin-top: 2px; }
        .module-title { font-size: 11px; font-weight: bold; color: #009A44; margin-top: 8px; }
        .perm-list { margin: 4px 0 0 14px; padding: 0; }
        .perm-list li { margin: 0 0 2px 0; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">CNRST SUIVI TRAVAUX</div>
        <div style="font-size: 16px; text-transform: uppercase; color: #009A44;">{{ $title ?? 'Export' }}</div>
        <div style="font-size: 12px; color: #666;">Généré le {{ now()->format('d/m/Y à H:i') }}</div>
    </div>

    @if(!empty($project))
    <div class="section">
        <h4 class="section-title">Informations du projet</h4>
        <table>
            <tbody>
                <tr><td style="width:200px;font-weight:bold">Nom du projet</td><td>{{ $project->nom }}</td></tr>
                <tr><td style="font-weight:bold">Description</td><td>{{ $project->description ?? '—' }}</td></tr>
                <tr><td style="font-weight:bold">Partenaire(s)</td>
                    <td>
                        @if(!empty($partenaires) && $partenaires->count() > 0)
                            @foreach($partenaires as $i => $c)
                                <div> N°{{ $i+1 }} {{ $c->nom ?? '—' }} {{ $c->prenom ?? '' }} {{ $c->email ?? '' }} {{ $c->telephone ?? '' }}</div>
                            @endforeach
                        @else
                            <div>Aucun partenaire associé</div>
                        @endif
                    </td>
                </tr>
                <tr><td style="font-weight:bold">Entreprise</td><td>{{ optional(optional($project->admin)->entreprise)->nom_entreprise ?? '—' }}</td></tr>
                <tr><td style="font-weight:bold">Date début</td><td>{{ optional($project->date_debut)->format('d/m/Y') ?? '—' }}</td></tr>
                <tr><td style="font-weight:bold">Date fin prévue</td><td>{{ optional($project->date_fin_prevue)->format('d/m/Y') ?? '—' }}</td></tr>
                <tr><td style="font-weight:bold">Date fin réelle</td><td>{{ optional($project->date_fin_reelle)->format('d/m/Y') ?? '—' }}</td></tr>
                <tr><td style="font-weight:bold">Budget</td><td>{{ $project->budget ? number_format($project->budget, 2, ',', ' ') : '—' }}</td></tr>
                <tr><td style="font-weight:bold">Budget consommé</td><td>{{ $project->budget_consomme ? number_format($project->budget_consomme, 2, ',', ' ') : '—' }}</td></tr>
                <tr><td style="font-weight:bold">Avancement</td><td>{{ $project->avancement ?? '—' }}%</td></tr>
                <tr><td style="font-weight:bold">Statut</td><td>{{ $project->statut ?? '—' }}</td></tr>
            </tbody>
        </table>
    </div>
    @endif

    @if(!empty($roles) && $roles->count() > 0)
    <div class="section">
        <h4 class="section-title">Rôles et permissions</h4>

        @foreach($roles as $role)
            <div class="role-block">
                <div class="role-name">{{ $role->nom }}</div>
                <div class="role-users">Nombre d’utilisateurs pour ce rôle : {{ $role->users_count ?? 0 }}</div>

                @php
                    $modules = $role->grouped_modules ?? collect();
                @endphp

                @if($modules->count())
                    @foreach($modules as $moduleData)
                        <div class="module-title">{{ $moduleData['module'] ?? 'Module' }}</div>
                        <ul class="perm-list">
                            @foreach(($moduleData['permissions'] ?? collect()) as $permName)
                                <li>{{ $permName }}</li>
                            @endforeach
                        </ul>
                    @endforeach
                @else
                    <p>—</p>
                @endif
            </div>
        @endforeach
    </div>
    @endif

    @if(empty($roles_only_export) && empty($project))
    <div class="section">
        <table>
            <thead>
                <tr>
                    @foreach($headers as $header)
                    <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php
                    $rowsToRender = collect($rows);
                    if(!empty($project)) {
                        $rowsToRender = $rowsToRender->reject(function($r) {
                            $s = is_array($r) ? implode(' ', $r) : (string) $r;
                            return stripos($s, 'NOM DU PROJET') !== false;
                        })->values();
                    }
                @endphp
                @foreach($rowsToRender as $row)
                <tr>
                    @foreach($row as $cell)
                    <td>{!! $cell !!}</td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        {{ $title ?? 'Export' }} - CNRST SUIVI TRAVAUX - &copy; {{ date('Y') }} CNRST - Document confidentiel
    </div>
</body>
</html>
