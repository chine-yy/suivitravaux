@extends('layouts.super-admin')

@section('title', 'Gestion des Fournisseurs')

@section('breadcrumb')
<span class="text-muted">Fournisseurs</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-truck me-2"></i>Gestion des Fournisseurs</h1>
                <p class="cp-page-subtitle">Gérez vos fournisseurs</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('super-admin.fournisseurs.export') }}" class="btn btn-outline-danger">
                    <i class="bi bi-file-earmark-pdf me-2"></i> Exporter
                </a>
                <a href="{{ route('super-admin.fournisseurs.create') }}" class="btn btn-primary px-4">
                    <i class="bi bi-plus-circle me-2"></i>Nouveau Fournisseur
                </a>
            </div>
        </div>


        <!-- Filtre -->
        <div class="cp-chart-card mb-4">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-filter me-2"></i>Filtres de recherche</h6>
            </div>
            <div class="p-4">
                <form action="{{ route('super-admin.fournisseurs.index') }}" method="GET" class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label small fw-bold">Nom / Email / Contact</label>
                        <input type="text" name="search" class="form-control form-control-sm"
                            placeholder="Rechercher..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Catégorie</label>
                        <select name="categorie" class="form-select form-select-sm">
                            <option value="">Toutes les catégories</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('categorie')==$cat ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Statut</label>
                        <select name="statut" class="form-select form-select-sm">
                            <option value="">Tous</option>
                            <option value="actif" {{ request('statut')=='actif' ? 'selected' : '' }}>Actif</option>
                            <option value="inactif" {{ request('statut')=='inactif' ? 'selected' : '' }}>Inactif
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="bi bi-search me-1"></i> Filtrer
                        </button>
                        <a href="{{ route('super-admin.fournisseurs.index') }}"
                            class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-list-ul me-2"></i>Liste des Fournisseurs</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="fournisseursTable">
                    <thead>
                        <tr style="background: rgba(99,102,241,.08);">
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Catégorie</th>
                            <th>Contact</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fournisseurs as $fournisseur)
                        <tr>
                            <td><strong>{{ $fournisseur->nom }}</strong></td>
                            <td>{{ $fournisseur->email ?? 'N/A' }}</td>
                            <td>{{ $fournisseur->telephone ?? 'N/A' }}</td>
                            <td>{{ $fournisseur->categorie ?? 'N/A' }}</td>
                            <td>
                                @if($fournisseur->contact_nom)
                                {{ $fournisseur->contact_prenom }} {{ $fournisseur->contact_nom }}
                                @else
                                N/A
                                @endif
                            </td>
                            <td>
                                <span
                                    class="badge {{ $fournisseur->statut == 'actif' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $fournisseur->statut == 'actif' ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="{{ route('super-admin.fournisseurs.show', $fournisseur->id) }}"
                                        class="btn btn-sm btn-outline-info" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('super-admin.export.pdf.direct', ['type' => 'fournisseur_list', 'id' => $fournisseur->id]) }}"
                                        class="btn btn-sm btn-outline-secondary" title="Télécharger">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <a href="{{ route('super-admin.fournisseurs.edit', $fournisseur->id) }}"
                                        class="btn btn-sm btn-outline-primary" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <form action="{{ route('super-admin.fournisseurs.destroy', $fournisseur->id) }}"
                                        method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Supprimer ce fournisseur ?')" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">

                                <i class="bi bi-truck display-4"></i>
                                <p class="mt-3">Aucun fournisseur trouvé</p>
                <button class="btn btn-outline-danger" onclick="exportToPdf('id="fournisseursTable"', 'Liste des fournisseurs', 'fournisseurs_export')">                    <i class="bi bi-file-earmark-pdf me-2"></i> Exporter                </button>
                                <a href="{{ route('super-admin.fournisseurs.create') }}">Créer un fournisseur</a>
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
