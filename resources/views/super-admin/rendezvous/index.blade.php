@extends('layouts.super-admin')

@section('title', 'Planification des Rendez-vous')

@section('breadcrumb')
<span class="text-muted">Rendez-vous</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-calendar-event me-2"></i>Planification des Rendez-vous</h1>
                <p class="cp-page-subtitle">Gérez vos rendez-vous</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-danger" onclick="exportToPdf('rendezvousTable', 'Liste des rendez-vous', 'rendezvous_export')">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Exporter tout
                </button>
                <a href="{{ route('super-admin.rendezvous.create') }}" class="btn btn-primary px-4">
                    <i class="bi bi-plus-circle me-2"></i>Nouveau Rendez-vous
                </a>
            </div>
        </div>


        <div class="cp-chart-card mb-4">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-filter me-2"></i>Filtres de recherche</h6>
            </div>
            <div class="p-4">
                <form action="{{ route('super-admin.rendezvous.index') }}" method="GET" class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Date</label>
                        <input type="date" name="date" class="form-control form-control-sm"
                            value="{{ request('date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Projet</label>
                        <select name="projet_id" class="form-select form-select-sm">
                            <option value="">Tous les projets</option>
                            @foreach($projets as $projet)
                            <option value="{{ $projet->id }}" {{ request('projet_id')==$projet->id ? 'selected' : ''
                                }}>{{ $projet->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Lieu</label>
                        <input type="text" name="lieu" class="form-control form-control-sm"
                            placeholder="Ville, bureau..." value="{{ request('lieu') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Statut</label>
                        <select name="statut" class="form-select form-select-sm">
                            <option value="">Tous les statuts</option>
                            <option value="planifie" {{ request('statut')=='planifie' ? 'selected' : '' }}>Planifié
                            </option>
                            <option value="confirme" {{ request('statut')=='confirme' ? 'selected' : '' }}>Confirmé
                            </option>
                            <option value="termine" {{ request('statut')=='termine' ? 'selected' : '' }}>Terminé
                            </option>
                            <option value="annule" {{ request('statut')=='annule' ? 'selected' : '' }}>Annulé</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-sm btn-primary text-nowrap">
                            <i class="bi bi-search me-1"></i> Filtrer
                        </button>
                        <a href="{{ route('super-admin.rendezvous.index') }}"
                            class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>


        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-list-ul me-2"></i>Liste des Rendez-vous</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="rendezvousTable">
                    <thead>
                        <tr style="background: rgba(99,102,241,.08);">
                            <th>Titre</th>
                            <th>Projet</th>
                            <th>Date & Heure</th>
                            <th>Durée</th>
                            <th>Lieu</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rendezvous as $rdv)
                        <tr>
                            <td><strong>{{ $rdv->titre }}</strong></td>
                            <td><span class="text-primary">{{ $rdv->projet->nom ?? 'N/A' }}</span></td>
                            <td>
                                @if($rdv->date_heure)
                                {{ date('d/m/Y', strtotime($rdv->date_heure)) }} à {{ date('H:i',
                                strtotime($rdv->date_heure)) }}
                                @else
                                N/A
                                @endif
                            </td>
                            <td>{{ $rdv->duree_minutes }} min</td>
                            <td>{{ $rdv->lieu ?? 'N/A' }}</td>
                            <td>{{ Str::limit($rdv->description, 50) ?? 'N/A' }}</td>
                            <td>
                                @php $types = ['reunion' => 'Réunion', 'visite' => 'Visite', 'appel' => 'Appel', 'autre'
                                => 'Autre']; @endphp
                                <span class="badge bg-light text-dark">
                                    {{ $rdv->type == 'autre' ? ($rdv->type_autre ?? 'Autre') : ($types[$rdv->type] ??
                                    $rdv->type) }}
                                </span>
                            </td>
                            <td>
                                @php
                                $statutClass = ['planifie' => 'bg-info', 'confirme' => 'bg-success', 'termine' =>
                                'bg-secondary', 'annule' => 'bg-danger'];
                                $statutText = ['planifie' => 'Planifié', 'confirme' => 'Confirmé', 'termine' =>
                                'Terminé', 'annule' => 'Annulé'];
                                @endphp
                                <span class="badge {{ $statutClass[$rdv->statut] ?? 'bg-secondary' }}">{{
                                    $statutText[$rdv->statut] ?? $rdv->statut }}</span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="{{ route('super-admin.rendezvous.show', $rdv->id) }}"
                                        class="btn btn-sm btn-outline-info" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('super-admin.rendezvous.edit', $rdv->id) }}"
                                        class="btn btn-sm btn-outline-primary" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="{{ route('super-admin.export.pdf.direct', ['type' => 'rendezvous', 'id' => $rdv->id]) }}"
                                        class="btn btn-sm btn-outline-secondary" title="Télécharger PDF">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <form action="{{ route('super-admin.rendezvous.destroy', $rdv->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Supprimer ce rendez-vous ?')" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-event display-4"></i>
                                <p class="mt-3">Aucun rendez-vous trouvé</p>
                <button class="btn btn-outline-danger" onclick="exportToPdf('id="rendezvousTable"', 'Liste des rendezvous', 'rendezvous_export')">                    <i class="bi bi-file-earmark-pdf me-2"></i> Exporter                </button>
                                <a href="{{ route('super-admin.rendezvous.create') }}">Créer un rendez-vous</a>
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
