@extends('layouts.role-dynamique')

@section('title', 'Gestion des Incidents')

@section('content')
<div class="cp-incidents">
    <div class="cp-content">
        <!-- Page Header -->
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-exclamation-triangle me-2"></i>Incidents</h1>
                <p class="cp-page-subtitle">Vue d'ensemble de tous les incidents signalés sur la plateforme</p>
            </div>
            <div class="d-flex gap-2">
                @if($canPermission('exporter-pdf-incidents'))
                <button class="btn btn-outline-danger" onclick="exportToPdf('incidentsTable', 'Liste des incidents', 'incidents_export')">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Exporter PDF
                </button>
                @endif
                @if($canPermission('create-incidents'))
                <a href="{{ route('role-dynamique.incidents.create') }}" class="btn btn-primary px-4">
                    <i class="bi bi-plus-circle me-2"></i>Enregistrer un Incident
                </a>
                @endif
            </div>
        </div>


        <div class="cp-chart-card mb-4">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-filter me-2"></i>Filtres de recherche</h6>
            </div>
            <div class="p-4">
                <form action="{{ route('role-dynamique.incidents.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Titre de l'Incident</label>
                        <input type="text" name="titre" class="form-control form-control-sm" placeholder="Rechercher..." value="{{ request('titre') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Projet</label>
                        <select name="projet_id" class="form-select form-select-sm">
                            <option value="">Tous les projets</option>
                            @foreach($projets as $projet)
                                <option value="{{ $projet->id }}" {{ request('projet_id') == $projet->id ? 'selected' : '' }}>{{ $projet->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Statut</label>
                        <select name="statut" class="form-select form-select-sm">
                            <option value="">Tous les statuts</option>
                            <option value="ouvert" {{ request('statut') == 'ouvert' ? 'selected' : '' }}>Ouvert</option>
                            <option value="en_traitement" {{ request('statut') == 'en_traitement' ? 'selected' : '' }}>En cours</option>
                            <option value="resolu" {{ request('statut') == 'resolu' ? 'selected' : '' }}>Résolu</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Gravité</label>
                        <select name="gravite" class="form-select form-select-sm">
                            <option value="">Toutes les gravités</option>
                            <option value="faible" {{ request('gravite') == 'faible' ? 'selected' : '' }}>Faible</option>
                            <option value="moyen" {{ request('gravite') == 'moyen' ? 'selected' : '' }}>Moyen</option>
                            <option value="critique" {{ request('gravite') == 'critique' ? 'selected' : '' }}>Critique</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="bi bi-search me-1"></i> Filtrer
                        </button>
                        <a href="{{ route('role-dynamique.incidents.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>


        <!-- Statistics -->
        <div class="cp-stats-grid mb-4">
            <div class="cp-stat-card">
                <div class="cp-stat-icon bg-soft-orange text-green"><i class="bi bi-exclamation-triangle"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ $totalIncidents }}</div>
                    <div class="cp-stat-label">Total Incidents</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-warning"><i class="bi bi-hourglass-split"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ $openIncidents }}</div>
                    <div class="cp-stat-label">Ouverts / En cours</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-success"><i class="bi bi-check-circle"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ $resolvedIncidents }}</div>
                    <div class="cp-stat-label">Résolus</div>
                </div>
            </div>
        </div>

        <!-- Incidents Table -->
        <div class="cp-chart-card">
            <div class="cp-chart-header d-flex justify-content-between align-items-center">
                <h6 class="cp-chart-title">
                    <i class="bi bi-list-ul me-2"></i>Liste Globale des Incidents
                </h6>
            </div>
            <div class="p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="incidentsTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Incident</th>
                                <th>Projet</th>
                                <th>Signalé par</th>
                                <th>Gravité</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th class="text-end pe-4" style="min-width: 130px;">
                                    @if($canPermission('exporter-pdf-incidents'))
                                    <button class="btn btn-sm btn-outline-danger" onclick="exportToPdf('incidentsTable', 'Liste des incidents', 'incidents_export')" title="Exporter tout">
                                        <i class="bi bi-download me-1"></i>Exporter tout
                                    </button>
                                    @endif
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($incidents as $incident)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold">{{ $incident->titre }}</div>
                                    <div class="text-muted small text-truncate" style="max-width: 250px;">{{ $incident->description }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-soft-info text-info">
                                        {{ $incident->projet->nom ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-soft-orange text-green d-flex align-items-center justify-content-center me-2" style="width: 28px; height: 28px; font-size: 0.8rem;">
                                            {{ substr($incident->signalePar->name ?? '?', 0, 1) }}
                                        </div>
                                        <span class="small">{{ $incident->signalePar->name ?? 'Système' }}</span>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $graviteClass = [
                                            'critique' => 'bg-danger',
                                            'moyen' => 'bg-warning text-dark',
                                            'faible' => 'bg-info'
                                        ][$incident->gravite] ?? 'bg-secondary';
                                    @endphp
                                    <span class="badge {{ $graviteClass }}">{{ ucfirst($incident->gravite) }}</span>
                                </td>
                                <td>
                                    @php
                                        $statutClass = [
                                            'resolu' => 'bg-success',
                                            'en_traitement' => 'bg-primary',
                                            'ouvert' => 'bg-warning text-dark'
                                        ][$incident->statut] ?? 'bg-secondary';
                                        $statutLabel = [
                                            'resolu' => 'Résolu',
                                            'en_traitement' => 'En cours',
                                            'ouvert' => 'Ouvert'
                                        ][$incident->statut] ?? $incident->statut;
                                    @endphp
                                    <span class="badge {{ $statutClass }}">{{ $statutLabel }}</span>
                                </td>
                                <td>{{ $incident->created_at->format('d/m/Y') }}</td>
<td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('role-dynamique.incidents.show', $incident->id) }}" class="btn btn-sm btn-outline-info" title="Voir détails">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($canPermission('exporter-pdf-incidents'))
                                        @include('partials.row-export', ['id' => $incident->id, 'prefix' => 'incident', 'title' => 'Incident - ' . ($incident->titre ?? $incident->id)])
                                        @endif
                                        @if($canPermission('edit-incidents'))
                                        <a href="{{ route('role-dynamique.incidents.edit', $incident->id) }}" class="btn btn-sm btn-outline-primary" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @endif
                                        @if($canPermission('delete-incidents'))
                                        <form action="{{ route('role-dynamique.incidents.destroy', $incident->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet incident ?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
@endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <div class="mb-3">
                                        <i class="bi bi-shield-check display-4 text-green opacity-50"></i>
                                    </div>
                                    <h5>Aucun incident à signaler</h5>
                                    <p class="small">Tous les incidents résolus ou aucun incident n'a été créé.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="p-3 border-top">
                {{ $incidents->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-soft-orange { background-color: rgba(0, 154, 68, 0.1); color: #009A44; }
    .bg-soft-info { background-color: rgba(13, 202, 240, 0.1); color: #0dcaf0; }
    .text-green { color: #009A44 !important; }
    .btn-outline-green { color: #009A44; border-color: #009A44; }
    .btn-outline-green:hover { background-color: #009A44; color: white; }
    .cp-bg-green { background: #009A44; color: white; }
    .btn-green { background: #009A44; color: white; border: none; transition: all 0.3s ease; }
    .btn-green:hover { background: #007a35; color: white; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0, 154, 68, 0.3); }
    .btn-with-border { border: 2px solid #009A44 !important; }
    .btn-with-border:hover { border-color: #007a35 !important; }
</style>
@endpush

@push('scripts')
<script>
    var table = document.getElementById(tableId);
    if (!table) return;
    var csv = [];
    var rows = table.querySelectorAll('tr');
    for (var i = 0; i < rows.length; i++) {
        var cols = rows[i].querySelectorAll('th, td');
        var row = [];
        for (var j = 0; j < cols.length; j++) {
            var text = cols[j].innerText.replace(/(||)/gm, ' ').replace(/"/g, '""').trim();
            row.push('"' + text + '"');
        }
        csv.push(row.join(';'));
    }
    var blob = new Blob(['\uFEFF' + csv.join('')], { type: 'text/csv;charset=utf-8;' });
    var link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = (filename || 'export') + '_' + new Date().toISOString().slice(0,10) + '.csv';
    link.click();
}
</script>
@endpush
