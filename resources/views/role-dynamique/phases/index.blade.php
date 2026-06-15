@extends('layouts.role-dynamique')

@section('title', 'Phases de Projet')

@section('breadcrumb')
    <span class="text-muted">Liste des Phases</span>
@endsection

@section('content')
    <div class="cp-phases">
        <div class="cp-content">
            <!-- Page Header -->
            <div class="cp-page-header">
                <div>
                    <h1 class="cp-page-title"><i class="bi bi-kanban me-2"></i>Phases</h1>
                    <p class="cp-page-subtitle">Suivi des phases de projet</p>
                </div>
                <div class="d-flex gap-2">
                    @if($canPermission('exporter-pdf-phases'))
                        <button class="btn btn-outline-danger"
                            onclick="exportToPdf('phasesTable', 'Liste des Phases', 'phases_export')">
                            <i class="bi bi-file-earmark-pdf me-2"></i>Exporter PDF
                        </button>
                    @endif
                    @if($canPermission('create-phases'))
                        <a href="{{ route('role-dynamique.phases.create') }}" class="btn btn-primary px-4">
                            <i class="bi bi-plus-circle me-2"></i>Nouvelle Phase
                        </a>
                    @endif
                </div>
            </div>


            <div class="cp-chart-card mb-4">
                <div class="cp-chart-header">
                    <h6 class="cp-chart-title"><i class="bi bi-filter me-2"></i>Rechercher un Projet</h6>
                </div>
                <div class="p-4">
                    <form action="{{ route('role-dynamique.phases.index') }}" method="GET" class="row g-3">
                        <div class="col-md-5">
                            <input type="text" name="nom" class="form-control" placeholder="Rechercher par nom de projet..."
                                value="{{ request('nom') }}">
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search me-1"></i> Filtrer
                            </button>
                            <a href="{{ route('role-dynamique.phases.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>


            <!-- Projects Table -->
            <div class="cp-card">
                <div class="cp-card-header">
                    <h5 class="cp-card-title">
                        <i class="bi bi-list-ul me-2"></i>Liste des Phases
                    </h5>
                </div>
                <div class="cp-card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="phasesTable">
                            <thead>
                                <tr style="background: rgba(99,102,241,.08);">
                                    <th>Nom du Projet</th>
                                    <th>Nom de phase</th>
                                    <th>Tâches</th>
                                    <th>Membres</th>
                                    <th>Sous-traitance</th>
                                    <th>Avancement</th>
                                    <th>Statut</th>
                                    <th class="text-end" style="min-width: 130px;">
                                        @if($canPermission('exporter-pdf-phases'))
                                            <button class="btn btn-sm btn-outline-danger"
                                                onclick="exportToPdf('phasesTable', 'Liste des Phases', 'phases_export')"
                                                title="Exporter tout">
                                                <i class="bi bi-download me-1"></i>Exporter tout
                                            </button>
                                        @endif
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($phases as $phase)
                                    <tr>
                                        <td><strong>{{ $phase->projet->nom }}</strong></td>
                                        <td>{{ $phase->nom }}</td>

                                        <td><span class="badge bg-light text-dark">{{ $phase->taches->count() }} tâches</span>
                                        </td>
                                        <td><span class="badge bg-light text-dark">{{ $phase->projet->membresCount() }}
                                                membres</span></td>
                                        <td><span class="badge bg-light text-dark">{{ $phase->projet->sousTraitances->count() }}
                                                entreprises</span></td>
                                        <td>
                                            <div class="progress" style="height: 10px; width: 100px;">
                                                <div class="progress-bar bg-primary" role="progressbar"
                                                    style="width: {{ $phase->avancement }}%"
                                                    aria-valuenow="{{ $phase->avancement }}" aria-valuemin="0"
                                                    aria-valuemax="100"></div>
                                            </div>
                                            <small>{{ $phase->avancement }}%</small>
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = [
                                                    'en_attente' => 'bg-secondary',
                                                    'en_cours' => 'bg-primary',
                                                    'termine' => 'bg-success',
                                                    'en_pause' => 'bg-primary',
                                                    'annule' => 'bg-danger'
                                                ][$phase->statut] ?? 'bg-secondary';
                                            @endphp
                                            <span
                                                class="badge {{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $phase->statut)) }}</span>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex gap-1 justify-content-end">
                                                @if($canPermission('view-phases'))
                                                    <a href="{{ route('role-dynamique.phases.show', $phase->id) }}"
                                                        class="btn btn-sm btn-outline-info" title="Voir">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                @endif
                                                @if($canPermission('exporter-pdf-phases'))
                                                    @include('partials.row-export', ['id' => $phase->id, 'prefix' => 'phase', 'title' => 'Phase - ' . ($phase->nom ?? $phase->id)])
                                                @endif
                                                @if($canPermission('edit-phases'))
                                                    <a href="{{ route('role-dynamique.phases.edit', $phase->id) }}"
                                                        class="btn btn-sm btn-outline-primary" title="Modifier">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                @endif
                                                @if($canPermission('delete-phases'))
                                                    <form action="{{ route('role-dynamique.phases.destroy', $phase->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            onclick="return confirm('Supprimer cette phase ?')" title="Supprimer">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <i class="bi bi-inbox display-4"></i>
                                            <p class="mt-3">Aucun projet trouvé</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($phases->hasPages())
                    <div class="cp-card-footer">
                        {{ $phases->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

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
        link.download = (filename || 'export') + '_' + new Date().toISOString().slice(0, 10) + '.csv';
        link.click();
        }
    </script>
@endpush