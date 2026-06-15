<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Historique {{ $annee }} - {{ config('app.name') }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.4; font-size: 12px; margin: 0; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #009A44; padding-bottom: 10px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #777; border-top: 1px solid #eee; padding-top: 5px; }
        .section { margin-bottom: 15px; }
        .section-title { color: #009A44; border-bottom: 2px solid #a5d6a7; padding-bottom: 5px; font-size: 14px; font-weight: bold; margin-bottom: 10px; }
        .logo { font-size: 20px; font-weight: bold; color: #009A44; margin-bottom: 3px; }
        .stats-grid { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 15px; }
        .stat-card { flex: 1; min-width: 80px; padding: 6px; border-radius: 5px; color: white; text-align: center; }
        .stat-card.blue { background-color: #3b82f6; }
        .stat-card.purple { background-color: #8b5cf6; }
        .stat-card.orange { background-color: #009A44; }
        .stat-card.teal { background-color: #14b8a6; }
        .stat-card.red { background-color: #ef4444; }
        .stat-card.indigo { background-color: #6366f1; }
        .stat-card.emerald { background-color: #009A44; }
        .stat-card.cyan { background-color: #06b6d4; }
        .stat-card strong { display: block; font-size: 14px; }
        .stat-card span { font-size: 9px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 10px; }
        th { background-color: #009A44; color: white; padding: 5px; text-align: left; font-size: 9px; }
        td { padding: 4px; border-bottom: 1px solid #eee; }
        .badge { display: inline-block; padding: 2px 5px; border-radius: 3px; font-size: 9px; font-weight: bold; color: #fff; }
        .bg-success { background-color: #009A44; }
        .bg-primary { background-color: #3b82f6; }
        .bg-warning { background-color: #eab308; color: #333; }
        .bg-danger { background-color: #ef4444; }
        .bg-secondary { background-color: #6b7280; }
        .bg-info { background-color: #06b6d4; }
        .bg-purple { background-color: #8b5cf6; }
        .bg-light { background-color: #f3f4f6; color: #333; }
        .hist-card { border: 1px solid #eee; border-radius: 5px; padding: 8px; margin-bottom: 8px; }
        .hist-card-header { display: flex; justify-content: space-between; align-items: center; }
        .progress { height: 6px; background-color: #eee; border-radius: 3px; }
        .progress-bar { height: 100%; border-radius: 3px; }
        .empty { font-size: 10px; color: #999; font-style: italic; padding: 10px; text-align: center; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">{{ config('app.name') }}</div>
        <div style="font-size: 16px; text-transform: uppercase; color: #009A44;">Historique {{ $annee }}</div>
        <div style="font-size: 11px; color: #666;"> Généré le {{ now()->format('d/m/Y à H:i') }}</div>
    </div>

    <!-- Stats Cards -->
    <div class="section">
        <div class="stats-grid">
            <div class="stat-card blue"><strong>{{ $stats['total_projets'] }}</strong><span>Projets</span></div>
            <div class="stat-card purple"><strong>{{ $stats['total_phases'] }}</strong><span>Phases</span></div>
            <div class="stat-card orange"><strong>{{ $stats['total_taches'] }}</strong><span>Tâches</span></div>
            <div class="stat-card teal"><strong>{{ $stats['total_sous_taches'] }}</strong><span>Sous-Tâches</span></div>
            <div class="stat-card red"><strong>{{ $stats['total_incidents'] }}</strong><span>Incidents</span></div>
            <div class="stat-card indigo"><strong>{{ $stats['total_rapports'] }}</strong><span>Rapports</span></div>
            <div class="stat-card emerald"><strong>{{ number_format($stats['budget_total'], 0, ',', ' ') }}</strong><span>Budget</span></div>
            <div class="stat-card cyan"><strong>{{ $stats['total_membres'] }}</strong><span>Membres</span></div>
        </div>
    </div>

    <!-- PROJETS -->
    <div class="section">
        <h4 class="section-title">{{ $projets->count() == 1 ? $projets->first()->nom : 'Projets (' . $projets->count() . ')' }}</h4>
        @if($projets->count() > 0)
        <table>
            <thead><tr><th>Nom</th><th>Statut</th><th>Partenaire</th><th>Avancement</th><th>Dates</th><th>Budget</th></tr></thead>
            <tbody>
                @foreach($projets as $projet)
                <tr>
                    <td><strong>{{ $projet->nom }}</strong></td>
                    <td>
                        @php $sc = ['termine'=>'bg-success','en_cours'=>'bg-primary','en_retard'=>'bg-danger','en_attente'=>'bg-warning'][$projet->statut ?? ''] ?? 'bg-secondary'; @endphp
                        <span class="badge {{ $sc }}">{{ $projet->statut ?? 'N/A' }}</span>
                    </td>
                    @php $projPartenaires = isset($partenaires) ? $partenaires->where('projet_id', $projet->id) : collect(); @endphp
                    <td>
                        @if($projPartenaires->count())
                            @foreach($projPartenaires as $i => $c)
                                <div style="margin-bottom:4px;">N°{{ $i+1 }} {{ $c->nom ?? '—' }} {{ $c->prenom ?? '' }} {{ $c->email ?? '' }} {{ $c->telephone ?? '' }}</div>
                            @endforeach
                        @else
                            {{ $projet->partenaire->nom ?? '—' }} {{ $projet->partenaire->prenom ?? '' }}
                        @endif
                    </td>
                    <td>{{ $projet->avancement ?? 0 }}%</td>
                    <td>{{ $projet->date_debut ? $projet->date_debut->format('d/m/Y') : '—' }}</td>
                    <td>{{ number_format($projet->budget ?? 0, 0, ',', ' ') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty">Cette section ne contient aucune donnée</div>
        @endif
    </div>

    <!-- PHASES -->
    <div class="section">
        <h4 class="section-title">{{ $phases->count() == 1 ? $phases->first()->nom : 'Phases (' . $phases->count() . ')' }}</h4>
        @if($phases->count() > 0)
        <table>
            <thead><tr><th>Nom</th><th>Projet</th><th>Statut</th><th>Tâches</th></tr></thead>
            <tbody>
                @foreach($phases as $phase)
                <tr>
                    <td><strong>{{ $phase->nom }}</strong></td>
                    <td>{{ $phase->projet->nom ?? 'N/A' }}</td>
                    <td><span class="badge bg-{{ $phase->statut === 'terminee' ? 'success' : ($phase->statut === 'en_cours' ? 'primary' : 'secondary') }}">{{ $phase->statut ?? 'N/A' }}</span></td>
                    <td>{{ $phase->taches->count() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty">Cette section ne contient aucune donnée</div>
        @endif
    </div>

    <!-- TÂCHES -->
    <div class="section">
        <h4 class="section-title">{{ $taches->count() == 1 ? $taches->first()->titre : 'Tâches (' . $taches->count() . ')' }}</h4>
        @if($taches->count() > 0)
        <table>
            <thead><tr><th>Titre</th><th>Projet</th><th>Statut</th><th>Priorité</th></tr></thead>
            <tbody>
                @foreach($taches as $tache)
                <tr>
                    <td><strong>{{ $tache->titre }}</strong></td>
                    <td>{{ $tache->projet->nom ?? 'N/A' }}</td>
                    <td><span class="badge bg-{{ ($tache->statut ?? '') === 'terminee' ? 'success' : (($tache->statut ?? '') === 'en_cours' ? 'primary' : 'secondary') }}">{{ $tache->statut ?? 'N/A' }}</span></td>
                    <td>{{ $tache->priorite ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty">Cette section ne contient aucune donnée</div>
        @endif
    </div>

    <!-- SOUS-TÂCHES -->
    <div class="section">
        <h4 class="section-title">{{ $sousTaches->count() == 1 ? ($sousTaches->first()->titre ?? $sousTaches->first()->description ?? 'Sous-Tâche') : 'Sous-Tâches (' . $sousTaches->count() . ')' }}</h4>
        @if($sousTaches->count() > 0)
        <table>
            <thead><tr><th>Titre</th><th>Tâche</th><th>Projet</th><th>Statut</th></tr></thead>
            <tbody>
                @foreach($sousTaches as $st)
                <tr>
                    <td><strong>{{ $st->titre ?? $st->description ?? 'Sous-tâche #' . $st->id }}</strong></td>
                    <td>{{ $st->tache->titre ?? 'N/A' }}</td>
                    <td>{{ $st->tache->projet->nom ?? 'N/A' }}</td>
                    <td><span class="badge bg-{{ ($st->fait ?? false) ? 'success' : 'warning' }}">{{ ($st->fait ?? false) ? 'Terminée' : 'En cours' }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty">Cette section ne contient aucune donnée</div>
        @endif
    </div>

    <!-- INCIDENTS -->
    <div class="section">
        <h4 class="section-title">{{ $incidents->count() == 1 ? ($incidents->first()->titre ?? 'Incident') : 'Incidents (' . $incidents->count() . ')' }}</h4>
        @if($incidents->count() > 0)
        <table>
            <thead><tr><th>Titre</th><th>Projet</th><th>Type</th><th>Gravité</th><th>Statut</th></tr></thead>
            <tbody>
                @foreach($incidents as $inc)
                <tr>
                    <td><strong>{{ $inc->titre ?? 'Incident #' . $inc->id }}</strong></td>
                    <td>{{ $projets->find($inc->projet_id)->nom ?? 'N/A' }}</td>
                    <td>{{ $inc->type ?? '—' }}</td>
                    <td><span class="badge bg-{{ ($inc->gravite ?? '') === 'critique' ? 'danger' : (($inc->gravite ?? '') === 'haute' ? 'danger' : (($inc->gravite ?? '') === 'moyenne' ? 'warning' : 'success')) }}">{{ $inc->gravite ?? '—' }}</span></td>
                    <td><span class="badge bg-{{ ($inc->statut ?? '') === 'resolu' ? 'success' : (($inc->statut ?? '') === 'en_cours' ? 'primary' : 'warning') }}">{{ $inc->statut ?? '—' }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty">Cette section ne contient aucune donnée</div>
        @endif
    </div>

    <!-- RAPPORTS -->
    <div class="section">
        <h4 class="section-title">{{ $rapports->count() == 1 ? $rapports->first()->titre : 'Rapports (' . $rapports->count() . ')' }}</h4>
        @if($rapports->count() > 0)
        <table>
            <thead><tr><th>Titre</th><th>Projet</th><th>Auteur</th><th>Type</th><th>Statut</th></tr></thead>
            <tbody>
                @foreach($rapports as $rapport)
                <tr>
                    <td><strong>{{ $rapport->titre }}</strong></td>
                    <td>{{ $projets->find($rapport->projet_id)->nom ?? 'N/A' }}</td>
                    <td>{{ $rapport->auteur->name ?? '—' }}</td>
                    <td>{{ $rapport->type ?? '—' }}</td>
                    <td><span class="badge bg-{{ ($rapport->statut ?? '') === 'valide' ? 'success' : (($rapport->statut ?? '') === 'en_attente' ? 'warning' : 'danger') }}">{{ $rapport->statut ?? '—' }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty">Cette section ne contient aucune donnée</div>
        @endif
    </div>

    <div class="page-break"></div>

    <!-- BUDGETS -->
    <div class="section">
        <h4 class="section-title">Budgets</h4>
        <div class="stats-grid">
            <div class="stat-card emerald"><strong>{{ number_format($stats['budget_total'], 0, ',', ' ') }}</strong><span>Budget Total</span></div>
            <div class="stat-card red"><strong>{{ number_format($stats['budget_consomme'], 0, ',', ' ') }}</strong><span>Consommé</span></div>
            <div class="stat-card orange"><strong>{{ number_format($stats['budget_restant'], 0, ',', ' ') }}</strong><span>Restant</span></div>
        </div>
    </div>

    <!-- FACTURES -->
    <div class="section">
        <h4 class="section-title">Factures ({{ $factures->count() == 1 ? ($factures->first()->numero_facture ?? 'Facture') : $factures->count() }})</h4>
        @if($factures->count() > 0)
        <table>
            <thead><tr><th>Référence</th><th>Montant TTC</th><th>Statut</th><th>Date</th></tr></thead>
            <tbody>
                @foreach($factures as $f)
                <tr>
                    <td><strong>{{ $f->numero_facture ?? '#' . $f->id }}</strong></td>
                    <td>{{ number_format($f->montant_ttc ?? 0, 0, ',', ' ') }} FAFSA</td>
                    <td><span class="badge bg-light">{{ $f->statut_paiement ?? '—' }}</span></td>
                    <td>{{ $f->date_emission ? $f->date_emission->format('d/m/Y') : ($f->created_at ? $f->created_at->format('d/m/Y') : '—') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty">Cette section ne contient aucune donnée</div>
        @endif
    </div>

    <!-- RÔLES -->
    <div class="section">
        <h4 class="section-title">{{ $roles->count() == 1 ? $roles->first()->nom : 'Rôles (' . $roles->count() . ')' }}</h4>
        @if($roles->count() > 0)
        <table>
            <thead><tr><th>Nom</th><th>Slug</th><th>Permissions</th><th>Utilisateurs</th></tr></thead>
            <tbody>
                @foreach($roles as $role)
                <tr>
                    <td><strong>{{ $role->nom }}</strong></td>
                    <td>{{ $role->slug ?? '—' }}</td>
                    <td>{{ $role->permissions->count() }} permission(s)</td>
                    <td>{{ $role->users_count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty">Cette section ne contient aucune donnée</div>
        @endif
    </div>

    <!-- UTILISATEURS -->
    <div class="section">
        <h4 class="section-title">{{ $users->count() == 1 ? $users->first()->name : 'Utilisateurs (' . $users->count() . ')' }}</h4>
        @if($users->count() > 0)
        <table>
            <thead><tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Statut</th></tr></thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td><strong>{{ $user->name }}</strong></td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role->nom ?? '—' }}</td>
                    <td><span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }}">{{ $user->is_active ? 'Actif' : 'Inactif' }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty">Cette section ne contient aucune donnée</div>
        @endif
    </div>

    <!-- ÉQUIPES -->
    <div class="section">
        <h4 class="section-title">{{ $equipes->count() == 1 ? $equipes->first()->nom : 'Équipes (' . $equipes->count() . ')' }}</h4>
        @if($equipes->count() > 0)
        <table>
            <thead><tr><th>Nom</th><th>Projet</th><th>Membres</th></tr></thead>
            <tbody>
                @foreach($equipes as $equipe)
                <tr>
                    <td><strong>{{ $equipe->nom }}</strong></td>
                    <td>{{ $equipe->projet->nom ?? 'N/A' }}</td>
                    <td>{{ $equipe->users->count() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty">Cette section ne contient aucune donnée</div>
        @endif
    </div>

    <!-- CLIENTS -->
    <div class="section">
        <h4 class="section-title">{{ $partenaires->count() == 1 ? ($partenaires->first()->nom . ' ' . $partenaires->first()->prenom) : 'Partenaires (' . $partenaires->count() . ')' }}</h4>
        @if($partenaires->count() > 0)
        <table>
            <thead><tr><th>Nom</th><th>Email</th><th>Téléphone</th><th>Projet</th></tr></thead>
            <tbody>
                @foreach($partenaires as $partenaire)
                <tr>
                    <td><strong>{{ $partenaire->nom }} {{ $partenaire->prenom ?? '' }}</strong></td>
                    <td>{{ $partenaire->email ?? '—' }}</td>
                    <td>{{ $partenaire->telephone ?? '—' }}</td>
                    <td>{{ $projets->find($partenaire->projet_id)->nom ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty">Cette section ne contient aucune donnée</div>
        @endif
    </div>

    <!-- DOCUMENTS -->
    <div class="section">
        <h4 class="section-title">{{ $documents->count() == 1 ? ($documents->first()->nom ?? $documents->first()->nom_fichier ?? 'Document') : 'Documents (' . $documents->count() . ')' }}</h4>
        @if($documents->count() > 0)
        <table>
            <thead><tr><th>Nom</th><th>Projet</th><th>Type</th><th>Date</th></tr></thead>
            <tbody>
                @foreach($documents as $doc)
                <tr>
                    <td><strong>{{ $doc->nom ?? $doc->nom_fichier ?? 'Document #' . $doc->id }}</strong></td>
                    <td>{{ $doc->projet->nom ?? 'N/A' }}</td>
                    <td>{{ $doc->type ?? '—' }}</td>
                    <td>{{ $doc->created_at ? $doc->created_at->format('d/m/Y') : '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty">Cette section ne contient aucune donnée</div>
        @endif
    </div>

    <!-- CONTRATS -->
    <div class="section">
        <h4 class="section-title">{{ $contrats->count() == 1 ? ($contrats->first()->projet->nom ?? 'Contrat') : 'Contrats (' . $contrats->count() . ')' }}</h4>
        @if($contrats->count() > 0)
        <table>
            <thead><tr><th>Projet</th><th>Type</th><th>Montant</th><th>Statut</th></tr></thead>
            <tbody>
                @foreach($contrats as $c)
                <tr>
                    <td>{{ $c->projet->nom ?? 'N/A' }}</td>
                    <td>{{ $c->type ?? '—' }}</td>
                    <td>{{ number_format($c->montant ?? 0, 0, ',', ' ') }} FAFSA</td>
                    <td><span class="badge bg-{{ ($c->statut ?? '') === 'actif' ? 'success' : 'secondary' }}">{{ $c->statut ?? '—' }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty">Cette section ne contient aucune donnée</div>
        @endif
    </div>

    <!-- INTERVENTIONS -->
    <div class="section">
        <h4 class="section-title">{{ $interventions->count() == 1 ? ($interventions->first()->projet->nom ?? 'Intervention') : 'Interventions (' . $interventions->count() . ')' }}</h4>
        @if($interventions->count() > 0)
        <table>
            <thead><tr><th>Projet</th><th>Type</th><th>Date</th><th>Statut</th></tr></thead>
            <tbody>
                @foreach($interventions as $i)
                <tr>
                    <td>{{ $i->projet->nom ?? 'N/A' }}</td>
                    <td>{{ $i->type ?? '—' }}</td>
                    <td>{{ $i->date_intervention ? $i->date_intervention->format('d/m/Y') : '—' }}</td>
                    <td><span class="badge bg-{{ ($i->statut ?? '') === 'terminee' ? 'success' : (($i->statut ?? '') === 'en_cours' ? 'primary' : 'secondary') }}">{{ $i->statut ?? '—' }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty">Cette section ne contient aucune donnée</div>
        @endif
    </div>

    <!-- SOUS-TRAITANCES -->
    <div class="section">
        <h4 class="section-title">{{ $sousTraitances->count() == 1 ? ($sousTraitances->first()->nom_entreprise ?? $sousTraitances->first()->nom_prestataire ?? 'Sous-Traitance') : 'Sous-Traitances (' . $sousTraitances->count() . ')' }}</h4>
        @if($sousTraitances->count() > 0)
        <table>
            <thead><tr><th>Prestataire</th><th>Projet</th><th>Montant</th><th>Statut</th></tr></thead>
            <tbody>
                @foreach($sousTraitances as $st)
                <tr>
                    <td><strong>{{ $st->nom_entreprise ?? $st->nom_prestataire ?? '—' }}</strong></td>
                    <td>{{ $st->projet->nom ?? 'N/A' }}</td>
                    <td>{{ number_format($st->montant_contrat ?? 0, 0, ',', ' ') }} FAFSA</td>
                    <td><span class="badge bg-{{ ($st->statut ?? '') === 'termine' ? 'success' : (($st->statut ?? '') === 'en_cours' ? 'primary' : 'secondary') }}">{{ $st->statut ?? '—' }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty">Cette section ne contient aucune donnée</div>
        @endif
    </div>

    <!-- FOURNISSEURS -->
    <div class="section">
        <h4 class="section-title">{{ $fournisseurs->count() == 1 ? $fournisseurs->first()->nom : 'Fournisseurs (' . $fournisseurs->count() . ')' }}</h4>
        @if($fournisseurs->count() > 0)
        <table>
            <thead><tr><th>Nom</th><th>Email</th><th>Téléphone</th><th>Adresse</th></tr></thead>
            <tbody>
                @foreach($fournisseurs as $f)
                <tr>
                    <td><strong>{{ $f->nom }}</strong></td>
                    <td>{{ $f->email ?? '—' }}</td>
                    <td>{{ $f->telephone ?? '—' }}</td>
                    <td>{{ $f->adresse ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty">Cette section ne contient aucune donnée</div>
        @endif
    </div>

    <!-- STOCKS -->
    <div class="section">
        <h4 class="section-title">{{ $stocks->count() == 1 ? ($stocks->first()->nom_article ?? $stocks->first()->designation ?? 'Stock') : 'Stocks (' . $stocks->count() . ')' }}</h4>
        @if($stocks->count() > 0)
        <table>
            <thead><tr><th>Article</th><th>Fournisseur</th><th>Quantité</th><th>Prix Unitaire</th></tr></thead>
            <tbody>
                @foreach($stocks as $s)
                <tr>
                    <td><strong>{{ $s->nom_article ?? $s->designation ?? 'Article #' . $s->id }}</strong></td>
                    <td>{{ $s->fournisseur->nom ?? '—' }}</td>
                    <td>{{ $s->quantite ?? 0 }}</td>
                    <td>{{ number_format($s->prix_unitaire ?? 0, 0, ',', ' ') }} FAFSA</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty">Cette section ne contient aucune donnée</div>
        @endif
    </div>

    <!-- RENDEZ-VOUS -->
    <div class="section">
        <h4 class="section-title">Rendez-vous ({{ $rendezvous->count() }})</h4>
        @if($rendezvous->count() > 0)
        <table>
            <thead><tr><th>Projet</th><th>Date</th><th>Statut</th></tr></thead>
            <tbody>
                @foreach($rendezvous as $r)
                <tr>
                    <td>{{ $r->projet->nom ?? 'N/A' }}</td>
                    <td>{{ $r->date_heure ? $r->date_heure->format('d/m/Y H:i') : '—' }}</td>
                    <td><span class="badge bg-{{ ($r->statut ?? '') === 'confirme' ? 'success' : (($r->statut ?? '') === 'annule' ? 'danger' : 'warning') }}">{{ $r->statut ?? '—' }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty">Cette section ne contient aucune donnée</div>
        @endif
    </div>

    <div class="footer">
        Historique {{ $annee }} - {{ config('app.name') }} - &copy; {{ date('Y') }} {{ config('app.name') }} - Document confidentiel
    </div>
</body>
</html>
