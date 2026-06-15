@extends('layouts.role-dynamique')

@section('title', 'Sous-Traitances')

@section('breadcrumb')
<span class="cp-breadcrumb-item">Sous-Traitances</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-people me-2"></i>Sous-Traitances</h1>
                <p class="cp-page-subtitle">Gérez les interventions de sous-traitance sur vos projets</p>
            </div>
            <div class="d-flex gap-2">
                @if($has('create-sous-traitances'))
                <a href="{{ route('role-dynamique.sous-traitances.create') }}" class="btn btn-primary px-4">
                    <i class="bi bi-plus-circle me-2"></i>Nouvelle Sous-Traitance
                </a>
                @endif
                @if($has('exporter-pdf-sous-traitances'))
                <button class="btn btn-outline-danger" onclick="exportToPdf('sous-traitancesTable', 'Liste des sous-traitances', 'sous-traitances_export')">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Exporter tout
                </button>
                @endif
                <a href="{{ route('role-dynamique.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>

        <div class="cp-stats-grid mb-4">
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-primary"><i class="bi bi-tools"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ collect($sousTraitances)->count() }}</div>
                    <div class="cp-stat-label">Total</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-green"><i class="bi bi-hourglass-split"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ collect($sousTraitances)->where('statut', 'en_cours')->count() }}</div>
                    <div class="cp-stat-label">En cours</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-success"><i class="bi bi-check-lg"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ collect($sousTraitances)->where('statut', 'terminee')->count() }}</div>
                    <div class="cp-stat-label">Terminées</div>
                </div>
            </div>
        </div>

        <!-- Filtre -->
        <div class="cp-chart-card mb-4">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-filter me-2"></i>Filtres de recherche</h6>
            </div>
            <div class="p-4">
                <form action="{{ route('role-dynamique.sous-traitances.index') }}" method="GET" class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label small fw-bold">Entreprise / Contact</label>
                        <input type="text" name="search" class="form-control form-control-sm"
                            placeholder="Rechercher..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Statut</label>
                        <select name="statut" class="form-select form-select-sm">
                            <option value="">Tous</option>
                            <option value="en_attente" {{ request('statut')=='en_attente' ? 'selected' : '' }}>En attente</option>
                            <option value="en_cours" {{ request('statut')=='en_cours' ? 'selected' : '' }}>En cours</option>
                            <option value="terminee" {{ request('statut')=='terminee' ? 'selected' : '' }}>Terminée</option>
                            <option value="annule" {{ request('statut')=='annule' ? 'selected' : '' }}>Annulée</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="bi bi-search me-1"></i> Filtrer
                        </button>
                        <a href="{{ route('role-dynamique.sous-traitances.index') }}"
                            class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-list-ul me-2"></i>Liste des Sous-Traitances</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="sous-traitancesTable">
                    <thead>
                        <tr>
                            <th>Entreprise</th>
                            <th>Projet</th>
                            <th>Contact</th>
                            <th>Employés</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sousTraitances as $st)
                        <tr>
                            <td>
                                <strong>{{ $st->nom_entreprise }}</strong>
                                @if($st->description_tache)
                                <br><small class="text-muted">{{ Str::limit($st->description_tache, 50) }}</small>
                                @endif
                            </td>
                            <td>{{ $st->projet?->nom ?? 'N/A' }}</td>
                            <td>
                                @if($st->contact_nom)
                                {{ $st->contact_prenom }} {{ $st->contact_nom }}
                                @if($st->contact_telephone)
                                <br><small class="text-muted"><i class="bi bi-telephone me-1"></i>{{ $st->contact_telephone }}</small>
                                @endif
                                @else
                                N/A
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ $st->nombre_employes }}</span>
                            </td>
                            <td>
                                @php
                                $statusBadge = [
                                'en_attente' => 'bg-secondary',
                                'en_cours' => 'bg-primary',
                                'terminee' => 'bg-success',
                                'annule' => 'bg-danger'
                                ][$st->statut] ?? 'bg-secondary';
                                $statusText = [
                                'en_attente' => 'En attente',
                                'en_cours' => 'En cours',
                                'terminee' => 'Terminée',
                                'annule' => 'Annulé'
                                ][$st->statut] ?? ucfirst($st->statut ?? 'N/A');
                                @endphp
                                <span class="badge {{ $statusBadge }}">{{ $statusText }}</span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    @if($has('view-sous-traitances'))
                                    <a href="{{ route('role-dynamique.sous-traitances.show', $st->id) }}"
                                        class="btn btn-sm btn-outline-info" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @endif
                                    
                                    @if($has('edit-sous-traitances'))
                                    <a href="{{ route('role-dynamique.sous-traitances.edit', $st->id) }}" class="btn btn-sm btn-outline-primary" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endif
                                    
                                    @if($has('exporter-pdf-sous-traitances'))
                                    <a href="{{ route('role-dynamique.export.pdf.direct', ['type' => 'soustraitance', 'id' => $st->id]) }}" class="btn btn-sm btn-outline-secondary" title="Télécharger">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    @endif
                                    
                                    @if($has('delete-sous-traitances'))
                                    <form action="{{ route('role-dynamique.sous-traitances.destroy', $st->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer cette sous-traitance ?')" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-people display-4"></i>
                                <p class="mt-3">Aucune sous-traitance enregistrée</p>
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
