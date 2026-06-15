@extends('layouts.role-dynamique')

@section('title', 'Gestion des Équipes')

@section('breadcrumb')
<span class="text-muted">Équipes</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-people-fill me-2"></i>Gestion des Équipes</h1>
                <p class="cp-page-subtitle">Visualisez et gérez toutes les équipes de projet</p>
            </div>
            <div class="d-flex gap-2">
                @if($has('exporter-pdf-equipes'))
                <a href="{{ route('role-dynamique.equipes.export-pdf', request()->all()) }}" class="btn btn-outline-danger px-4">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Exporter PDF
                </a>
                @endif
                @if($has('create-equipes'))
                <a href="{{ route('role-dynamique.equipes.create') }}" class="btn btn-primary px-4">
                    <i class="bi bi-plus-circle me-2"></i>Nouvelle Équipe
                </a>
                @endif
            </div>
        </div>


        <div class="cp-chart-card mb-4">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-filter me-2"></i>Filtres de recherche</h6>
            </div>
            <div class="p-4">
                <form action="{{ route('role-dynamique.equipes.index') }}" method="GET" class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label small fw-bold">Nom de l'équipe</label>
                        <input type="text" name="search" class="form-control form-control-sm"
                            placeholder="Rechercher..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Projet</label>
                        <select name="projet_id" class="form-select form-select-sm">
                            <option value="">Tous les projets</option>
                            @foreach($projets as $projet)
                            <option value="{{ $projet->id }}" {{ request('projet_id')==$projet->id ? 'selected' : '' }}>
                                {{ $projet->nom }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="bi bi-search me-1"></i> Filtrer
                        </button>
                        <a href="{{ route('role-dynamique.equipes.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-list-ul me-2"></i>Liste des Équipes</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="equipesTable">
                    <thead>
                        <tr style="background: rgba(99,102,241,.08);">
                            <th>Équipe</th>
                            <th>Projet</th>
                            <th>Membres</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($equipes as $equipe)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $equipe->nom }}</div>
                                <small class="text-muted">{{ Str::limit($equipe->description, 40) }}</small>
                            </td>
                            <td>
                                @if($equipe->projet)
                                <span class="badge bg-light text-primary border border-primary">
                                    {{ $equipe->projet->nom }}
                                </span>
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @foreach($equipe->users->take(4) as $user)
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center border border-2 border-white"
                                        style="width: 32px; height: 32px; margin-left: -8px; font-size: 11px;"
                                        title="{{ $user->name }}">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    @endforeach
                                    @if($equipe->users->count() > 4)
                                    <div class="rounded-circle bg-light text-dark d-flex align-items-center justify-content-center border border-2 border-white"
                                        style="width: 32px; height: 32px; margin-left: -8px; font-size: 11px;">
                                        +{{ $equipe->users->count() - 4 }}
                                    </div>
                                    @endif
                                    @if($equipe->users->count() == 0)
                                    <span class="text-muted small">Aucun membre</span>
                                    @endif
                                    <span class="ms-2 small text-muted">({{ $equipe->users->count() }})</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $equipe->statut === 'active' ? 'success' : 'secondary' }}">
                                    {{ $equipe->statut === 'active' ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    @if($has('view-equipes'))
                                    <a href="{{ route('role-dynamique.equipes.show', $equipe->id) }}"
                                        class="btn btn-sm btn-outline-info" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @endif
                                    @if($has('exporter-pdf-equipes'))
                                    @include('partials.row-export', ['id' => $equipe->id, 'prefix' => 'equipe', 'title' => 'Équipe - ' . ($equipe->nom ?? $equipe->id)])
                                    @endif
                                    @if($has('edit-equipes'))
                                    <a href="{{ route('role-dynamique.equipes.edit', $equipe->id) }}"
                                        class="btn btn-sm btn-outline-primary" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endif
                                    @if($has('delete-equipes'))
                                    <form action="{{ route('role-dynamique.equipes.destroy', $equipe->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Supprimer cette équipe ?')" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-people display-4"></i>
                                <p class="mt-3">Aucune équipe trouvée</p>
                                @if($has('create-equipes'))
                                <a href="{{ route('role-dynamique.equipes.create') }}">Créer une équipe</a>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($equipes->hasPages())
            <div class="card-footer bg-white border-0">
                {{ $equipes->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection