@extends('layouts.role-dynamique')

@section('title', 'Détail du Contrat - ' . $contrat->numero_contrat)

@section('breadcrumb')
    <span class="text-muted"><a href="{{ route('role-dynamique.contrats.index') }}">Contrats</a></span>
    <span class="mx-2 text-muted">/</span>
    <span class="text-muted">Détails</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <!-- Header -->
        <div class="cp-page-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-file-earmark-text-fill me-2"></i>Contrat: {{ $contrat->numero_contrat }}</h1>
                <p class="cp-page-subtitle">Informations détaillées, type et statut du contrat</p>
            </div>
            <div class="d-flex gap-2">
                @if($has('exporter-pdf-contrats'))
                @include('partials.row-export', ['id' => $contrat->id, 'prefix' => 'contrat', 'title' => 'Détail - Contrat'])
                @endif
                @if($has('edit-contrats'))
                <a href="{{ route('role-dynamique.contrats.edit', $contrat->id) }}" class="btn btn-primary px-4">
                    <i class="bi bi-pencil me-2"></i>Modifier
                </a>
                @endif
                <a href="{{ route('role-dynamique.contrats.index') }}" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>

        <div class="row g-4">
            <!-- Informations Globales -->
            <div class="col-lg-8">
                <div class="cp-chart-card mb-4">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-info-circle me-2"></i>Informations du Contrat</h6>
                    </div>
                    <div class="p-4">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Numéro de Contrat</label>
                                <div class="fs-5 fw-bold text-dark">{{ $contrat->numero_contrat }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Type de Contrat</label>
                                <div>
                                    @php
                                        $types = ['prestation' => 'Prestation', 'marche' => 'Marché', 'sous_traitance' => 'Sous-traitance', 'autre' => 'Autre'];
                                    @endphp
                                    <span class="badge bg-light text-primary border px-3 py-2 rounded-pill mt-1">
                                        {{ $types[$contrat->type] ?? $contrat->type }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="text-muted small fw-bold">Objet / Description</label>
                            <div class="bg-light p-3 rounded" style="min-height: 80px;">
                                {!! nl2br(e($contrat->objet ?? 'Aucun objet spécifié pour ce contrat.')) !!}
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold"><i class="bi bi-currency-dollar text-warning"></i> Montant</label>
                                <div class="fs-5 fw-bold text-success">{{ number_format($contrat->montant, 0, ',', ' ') }} FCFA</div>
                            </div>
                            <div class="col-md-6">
                            </div>
                        </div>

                        <hr>
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold"><i class="bi bi-calendar-event text-primary"></i> Date de Début</label>
                                <div>{{ $contrat->date_debut ? date('d/m/Y', strtotime($contrat->date_debut)) : 'Non définie' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold"><i class="bi bi-calendar-check text-success"></i> Date de Fin</label>
                                <div>{{ $contrat->date_fin ? date('d/m/Y', strtotime($contrat->date_fin)) : 'Non définie' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Conditions -->
                <div class="cp-chart-card">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-card-text me-2"></i>Conditions Particulières</h6>
                    </div>
                    <div class="p-4">
                        <div style="min-height: 100px;">
                            @if($contrat->conditions)
                                {!! nl2br(e($contrat->conditions)) !!}
                            @else
                                <span class="text-muted fst-italic">Aucune condition particulière définie.</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Details Latéraux -->
            <div class="col-lg-4">
                <div class="cp-chart-card mb-4 h-100">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-bookmark-star me-2"></i>Aperçu & Statut</h6>
                    </div>
                    <div class="p-4">
                        <div class="mb-4 text-center">
                            @php
                                $statusClass = [
                                    'brouillon' => 'bg-secondary', 
                                    'signe' => 'bg-info', 
                                    'en_cours' => 'bg-primary', 
                                    'termine' => 'bg-success', 
                                    'annule' => 'bg-danger'
                                ];
                                $statusText = [
                                    'brouillon' => 'Brouillon', 
                                    'signe' => 'Signé', 
                                    'en_cours' => 'En cours', 
                                    'termine' => 'Terminé', 
                                    'annule' => 'Annulé'
                                ];
                            @endphp
                            <label class="text-muted small fw-bold d-block mb-2">Statut Actuel</label>
                            <span class="badge {{ $statusClass[$contrat->statut] ?? 'bg-secondary' }} px-4 py-2" style="font-size: 1rem;">
                                {{ $statusText[$contrat->statut] ?? $contrat->statut }}
                            </span>
                        </div>
                        
                        <hr>

                        <div class="mb-4 mt-4">
                            <label class="text-muted small fw-bold">Partenaire Associé</label>
                            @if($contrat->partenaire)
                                <div class="d-flex align-items-center mt-2 border rounded p-3">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold me-3" style="width:40px;height:40px;font-size:0.9rem;">
                                        {{ strtoupper(substr($contrat->partenaire->prenom ?? $contrat->partenaire->name, 0, 1)) }}{{ strtoupper(substr($contrat->partenaire->name ?? $contrat->partenaire->nom ?? '', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $contrat->partenaire->prenom ?? '' }} {{ $contrat->partenaire->name ?? $contrat->partenaire->nom ?? '' }}</div>
                                        <small class="text-muted"><i class="bi bi-envelope me-1"></i>{{ $contrat->partenaire->email }}</small>
                                    </div>
                                    <a href="{{ route('role-dynamique.partenaires.show', $contrat->partenaire->id) }}" class="ms-auto text-primary" title="Voir Partenaire">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                </div>
                            @else
                                <div class="text-muted border rounded p-3 mt-2 bg-light text-center">
                                    N/A
                                </div>
                            @endif
                        </div>

                        <div class="mb-4">
                            <label class="text-muted small fw-bold">Projet Associé</label>
                            @if($contrat->projet)
                                <div class="mt-2 border rounded p-3">
                                    <div class="fw-semibold">{{ $contrat->projet->nom }}</div>
                                    <a href="{{ route('role-dynamique.projets.show', $contrat->projet->id) }}" class="btn btn-sm btn-outline-primary mt-2 w-100">
                                        Voir Projet
                                    </a>
                                </div>
                            @else
                                <div class="text-muted border rounded p-3 mt-2 bg-light text-center">
                                    N/A
                                </div>
                            @endif
                        </div>

                        <hr>
                        <div class="text-center mt-3">
                            <small class="text-muted">Créé le {{ $contrat->created_at->format('d/m/Y') }}</small>
                            <br>
                            <small class="text-muted">Dernière modification le {{ $contrat->updated_at->format('d/m/Y') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
