@extends('layouts.super-admin')

@section('title', 'Gestion des Interventions')

@section('breadcrumb')
<span class="text-muted">Interventions</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-tools me-2"></i>Gestion des Interventions</h1>
                <p class="cp-page-subtitle">Gérez vos interventions</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-danger" onclick="exportToPdf('interventionsTable', 'Liste des interventions', 'interventions_export')">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Exporter tout
                </button>
                <a href="{{ route('super-admin.interventions.create') }}" class="btn btn-primary px-4">
                    <i class="bi bi-plus-circle me-2"></i>Nouvelle Intervention
                </a>
            </div>
        </div>


        <!-- Filtre -->
        <div class="cp-chart-card mb-4">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-filter me-2"></i>Filtres de recherche</h6>
            </div>
            <div class="p-4">
                <form action="{{ route('super-admin.interventions.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Mot-clé / Description</label>
                        <input type="text" name="search" class="form-control form-control-sm"
                            placeholder="Rechercher..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Technicien</label>
                        <select name="technicien_id" class="form-select form-select-sm">
                            <option value="">Tous les techniciens</option>
                            @foreach($techniciens as $tech)
                            <option value="{{ $tech->id }}" {{ request('technicien_id')==$tech->id ? 'selected' : '' }}>
                                {{ $tech->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Statut</label>
                        <select name="statut" class="form-select form-select-sm">
                            <option value="">Tous</option>
                            <option value="planifie" {{ request('statut')=='planifie' ? 'selected' : '' }}>Planifié
                            </option>
                            <option value="en_cours" {{ request('statut')=='en_cours' ? 'selected' : '' }}>En cours
                            </option>
                            <option value="termine" {{ request('statut')=='termine' ? 'selected' : '' }}>Terminé
                            </option>
                            <option value="annule" {{ request('statut')=='annule' ? 'selected' : '' }}>Annulé</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Date</label>
                        <input type="date" name="date_intervention" class="form-control form-control-sm"
                            value="{{ request('date_intervention') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="bi bi-search me-1"></i> Filtrer
                        </button>
                        <a href="{{ route('super-admin.interventions.index') }}"
                            class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-list-ul me-2"></i>Liste des Interventions</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="interventionsTable">
                    <thead>
                        <tr style="background: rgba(99,102,241,.08);">
                            <th>Date</th>
                            <th>Type</th>
                            <th>Projet</th>
                            <th>Technicien</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($interventions as $intervention)
                        <tr>
                            <td>{{ $intervention->date_intervention ? date('d/m/Y H:i',
                                strtotime($intervention->date_intervention)) : 'N/A' }}</td>
                            <td>
                                @php
                                $types = ['installation' => 'Installation', 'maintenance' => 'Maintenance', 'reparation'
                                => 'Réparation', 'inspection' => 'Inspection', 'autre' => 'Autre'];
                                @endphp
                                <span class="badge bg-light text-dark">{{ $types[$intervention->type] ??
                                    $intervention->type }}</span>
                            </td>
                            <td>{{ $intervention->projet->nom ?? 'N/A' }}</td>
                            <td>{{ $intervention->technicien->name ?? 'N/A' }}</td>
                            <td>
                                @php
                                $statutClass = ['planifie' => 'bg-info', 'en_cours' => 'bg-primary', 'termine' =>
                                'bg-success', 'annule' => 'bg-danger'];
                                $statutText = ['planifie' => 'Planifié', 'en_cours' => 'En cours', 'termine' =>
                                'Terminé', 'annule' => 'Annulé'];
                                @endphp
                                <span class="badge {{ $statutClass[$intervention->statut] ?? 'bg-secondary' }}">{{
                                    $statutText[$intervention->statut] ?? $intervention->statut }}</span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="{{ route('super-admin.interventions.show', $intervention->id) }}"
                                        class="btn btn-sm btn-outline-info" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('super-admin.interventions.edit', $intervention->id) }}"
                                        class="btn btn-sm btn-outline-primary" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="{{ route('super-admin.export.pdf.direct', ['type' => 'intervention', 'id' => $intervention->id]) }}"
                                        class="btn btn-sm btn-outline-secondary" title="Télécharger PDF">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <form action="{{ route('super-admin.interventions.destroy', $intervention->id) }}"
                                        method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Supprimer cette intervention ?')"
                                            title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-tools display-4"></i>
                                <p class="mt-3">Aucune intervention trouvée</p>
                <button class="btn btn-outline-danger" onclick="exportToPdf('id="interventionsTable"', 'Liste des interventions', 'interventions_export')">                    <i class="bi bi-file-earmark-pdf me-2"></i> Exporter                </button>
                                <a href="{{ route('super-admin.interventions.create') }}">Créer une intervention</a>
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
