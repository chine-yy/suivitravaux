@extends('layouts.role-dynamique')

@section('title', 'Gestion des Documents')

@section('breadcrumb')
<span class="text-muted">Documents</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-file-earmark me-2"></i>Gestion des Documents</h1>
                <p class="cp-page-subtitle">Gérez vos documents</p>
            </div>
            <div class="d-flex gap-2">
                @if($has('exporter-pdf-documents'))
                <button class="btn btn-outline-danger" onclick="exportToPdf('documentsTable', 'Liste des documents', 'documents_export')">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Exporter tout
                </button>
                @endif
                @if($has('create-documents'))
                <a href="{{ route('role-dynamique.documents.create') }}" class="btn btn-primary px-4">
                    <i class="bi bi-plus-circle me-2"></i>Nouveau Document
                </a>
                @endif
            </div>
        </div>


        <!-- Filtre -->
        <div class="cp-chart-card mb-4">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-filter me-2"></i>Filtres de recherche</h6>
            </div>
            <div class="p-4">
                <form action="{{ route('role-dynamique.documents.index') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Nom / Description</label>
                        <input type="text" name="search" class="form-control form-control-sm"
                            placeholder="Rechercher..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
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
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Type de Document</label>
                        <select name="type" class="form-select form-select-sm">
                            <option value="">Tous les types</option>
                            <option value="contrat" {{ request('type')=='contrat' ? 'selected' : '' }}>Contrat</option>
                            <option value="facture" {{ request('type')=='facture' ? 'selected' : '' }}>Facture</option>
                            <option value="rapport" {{ request('type')=='rapport' ? 'selected' : '' }}>Rapport</option>
                            <option value="photo" {{ request('type')=='photo' ? 'selected' : '' }}>Photo</option>
                            <option value="plan" {{ request('type')=='plan' ? 'selected' : '' }}>Plan</option>
                            <option value="autre" {{ request('type')=='autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="bi bi-search me-1"></i> Filtrer
                        </button>
                        <a href="{{ route('role-dynamique.documents.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-list-ul me-2"></i>Liste des Documents</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="documentsTable">
                    <thead>
                        <tr style="background: rgba(99,102,241,.08);">
                            <th>Nom</th>
                            <th>Type</th>
                            <th>Projet</th>
                            <th>Catégorie</th>
                            @if($has('download-documents'))<th>Fichier</th>@endif
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documents as $document)
                        <tr>
                            <td><strong>{{ $document->nom }}</strong></td>
                            <td>
                                @php
                                $types = ['contrat' => 'Contrat', 'facture' => 'Facture', 'rapport' => 'Rapport',
                                'photo' => 'Photo', 'plan' => 'Plan', 'autre' => 'Autre'];
                                $displayType = $document->type === 'autre' && $document->type_personnalise
                                ? $document->type_personnalise
                                : ($types[$document->type] ?? $document->type);
                                @endphp
                                <span class="badge bg-light text-dark">{{ $displayType }}</span>
                            </td>
                            <td>{{ $document->projet->nom ?? 'N/A' }}</td>
                            <td>{{ $document->categorie ?? 'Non spécifiée' }}</td>
                            @if($has('download-documents'))
                            <td>
                                @if($document->fichier)
                                <a href="{{ asset('storage/' . $document->fichier) }}" target="_blank"
                                    class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-download"></i>
                                </a>
                                @else
                                <span class="text-muted">Aucun</span>
                                @endif
                            </td>
                            @endif
                            <td>
                                <span class="badge {{ $document->statut == 'actif' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $document->statut == 'actif' ? 'Actif' : 'Archivé' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="{{ route('role-dynamique.documents.show', $document->id) }}"
                                        class="btn btn-sm btn-outline-info" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($has('edit-documents'))
                                    <a href="{{ route('role-dynamique.documents.edit', $document->id) }}"
                                        class="btn btn-sm btn-outline-primary" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endif
                                    @if($has('exporter-pdf-documents'))
                                        @include('partials.row-export', ['id' => $document->id, 'prefix' => 'document', 'title' => 'Télécharger'])
                                    @endif
                                    @if($has('delete-documents'))
                                    <form action="{{ route('role-dynamique.documents.destroy', $document->id) }}"
                                        method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Supprimer ce document ?')" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $has('download-documents') ? 7 : 6 }}" class="text-center py-5 text-muted">
                                <i class="bi bi-file-earmark display-4"></i>
                                <p class="mt-3">Aucun document trouvé</p>
                                @if($has('create-documents'))
                                <a href="{{ route('role-dynamique.documents.create') }}">Créer un document</a>
                                @endif
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