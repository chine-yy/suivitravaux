@extends('layouts.super-admin')

@section('title', 'Gestion des Sous-Tâches')

@section('breadcrumb')
    <span class="text-muted">Sous-Tâches</span>
@endsection

@section('content')
    <div class="cp-dashboard">
        <div class="cp-content">
            <div class="cp-page-header">
                <div>
                    <h1 class="cp-page-title"><i class="bi bi-list-check me-2"></i>Gestion des Sous-Tâches</h1>
                    <p class="cp-page-subtitle">Visualisez et gérez les sous-tâches</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-danger"
                        onclick="exportToPdf('sous-tachesTable', 'Liste des sous-taches', 'sous-taches_export')">
                        <i class="bi bi-file-earmark-pdf me-2"></i>Exporter tout
                    </button>
                    <a href="{{ route('super-admin.sous-taches.create') }}" class="btn btn-primary px-4">
                        <i class="bi bi-plus-circle me-2"></i>Créer une Sous-Tâche
                    </a>
                </div>
            </div>


            <div class="cp-chart-card mb-4">
                <div class="cp-chart-header">
                    <h6 class="cp-chart-title"><i class="bi bi-filter me-2"></i>Filtres de recherche</h6>
                </div>
                <div class="p-4">
                    <form action="{{ route('super-admin.sous-taches.index') }}" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Titre de la Sous-Tâche</label>
                            <input type="text" name="titre" class="form-control form-control-sm"
                                placeholder="Ex: Ponçage, Deuxième couche..." value="{{ request('titre') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Tâche Parente</label>
                            <select name="tache_id" class="form-select form-select-sm">
                                <option value="">Toutes les tâches</option>
                                @foreach($taches as $tache)
                                                        <option value="{{ $tache->id }}" {{ request('tache_id') == $tache->id ? 'selected' : '' }}>{{
                                    $tache->titre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Statut</label>
                            <select name="statut" class="form-select form-select-sm">
                                <option value="">Tous les statuts</option>
                                <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En
                                    attente</option>
                                <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En cours
                                </option>
                                <option value="terminee" {{ request('statut') == 'terminee' ? 'selected' : '' }}>Terminée
                                </option>
                                <option value="bloquee" {{ request('statut') == 'bloquee' ? 'selected' : '' }}>Bloquée
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-sm btn-primary w-100">
                                <i class="bi bi-search me-1"></i> Filtrer
                            </button>
                            <a href="{{ route('super-admin.sous-taches.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>


            <div class="cp-chart-card">
                <div class="cp-chart-header">
                    <h6 class="cp-chart-title"><i class="bi bi-list-ul me-2"></i>Liste des Sous-Tâches</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="sous-tachesTable">
                        <thead>
                            <tr style="background: rgba(99,102,241,.08);">
                                <th>Sous-Tâche</th>
                                <th>Tâche Parente</th>
                                <th>Projet / Phase</th>
                                <th>Personne assignée</th>
                                <th>Statut</th>
                                <th>Avancement</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sousTaches as $sousTache)
                                                <tr>
                                                    <td><strong>{{ $sousTache->titre }}</strong></td>
                                                    <td>
                                                        <span class="badge bg-light text-dark">{{ $sousTache->tache ? ($sousTache->tache->titre
                                ?? 'Tâche #' . $sousTache->tache->id) : 'N/A' }}</span>
                                                    </td>
                                                    <td>
                                                         <div class="text-muted small">
                                                             <i class="bi bi-briefcase me-1"></i>{{ $sousTache->tache &&
 $sousTache->tache->projet ? $sousTache->tache->projet->nom : 'N/A' }}
                                                             @if($sousTache->tache && $sousTache->tache->phase)
                                                                 / {{ $sousTache->tache->phase->nom }}
                                                             @endif
                                                         </div>
                                                     </td>
                                                    <td>
                                                        @if($sousTache->user)
                                                            <div class="d-flex align-items-center gap-2">
                                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold"
                                                                    style="width:24px;height:24px;font-size:0.7rem;">
                                                                    {{ strtoupper(substr($sousTache->user->name, 0, 1)) }}
                                                                </div>
                                                                <span class="small">{{ $sousTache->user->name }}</span>
                                                            </div>
                                                        @else
                                                            <span class="text-muted small"></span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                            $statusBadge = [
                                                                'en_attente' => 'bg-secondary',
                                                                'en_cours' => 'bg-primary',
                                                                'terminee' => 'bg-success',
                                                                'bloquee' => 'bg-danger'
                                                            ][$sousTache->statut] ?? 'bg-secondary';
                                                            $statusText = [
                                                                'en_attente' => 'En attente',
                                                                'en_cours' => 'En cours',
                                                                'terminee' => 'Terminée',
                                                                'bloquee' => 'Bloquée'
                                                            ][$sousTache->statut] ?? ucfirst($sousTache->statut ?? 'N/A');
                                                        @endphp
                                                        <span class="badge {{ $statusBadge }}">{{ $statusText }}</span>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                                <div class="progress-bar bg-primary" role="progressbar"
                                                                    style="width: {{ $sousTache->avancement ?? 0 }}%;"></div>
                                                            </div>
                                                            <span class="small fw-bold" style="min-width: 40px;">{{ $sousTache->avancement ?? 0
                                                                }}%</span>
                                                        </div>
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="d-flex gap-1 justify-content-end">
                                                            <a href="{{ route('super-admin.sous-taches.show', $sousTache->id) }}"
                                                                class="btn btn-sm btn-outline-secondary" title="Voir">
                                                                <i class="bi bi-eye"></i>
                                                            </a>
                                                            @include('partials.row-export', ['id' => $sousTache->id, 'prefix' => 'sous-tache', 'title' => 'Sous-Tâche - ' . ($sousTache->titre ?? $sousTache->id)])
                                                            <a href="{{ route('super-admin.sous-taches.edit', $sousTache->id) }}"
                                                                class="btn btn-sm btn-outline-primary" title="Modifier">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                            <form action="{{ route('super-admin.sous-taches.destroy', $sousTache->id) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                    onclick="return confirm('Supprimer cette sous-tâche ?')" title="Supprimer">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-list-check display-4"></i>
                                        <p class="mt-3">Aucune sous-tâche trouvée</p>
                                        <button class="btn btn-outline-danger" onclick="exportToPdf('id="
                                            sous-tachesTable"', 'Liste des sous-taches' , 'sous-taches_export' )"> <i
                                                class="bi bi-file-earmark-pdf me-2"></i> Exporter </button>
                                        <a href="{{ route('super-admin.sous-taches.create') }}">Créer une sous-tâche</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
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