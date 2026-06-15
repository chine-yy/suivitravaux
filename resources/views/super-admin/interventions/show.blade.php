@extends('layouts.super-admin')

@section('title', 'Détails de l\'Intervention')

@section('breadcrumb')
    <a href="{{ route('super-admin.interventions.index') }}" class="text-muted text-decoration-none">Interventions</a>
    <span class="mx-2 text-muted">/</span>
    <span class="text-dark">Détails</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-tools me-2"></i>Intervention #{{ $intervention->id }}</h1>
                <p class="cp-page-subtitle">Détails complets de l'intervention</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('super-admin.interventions.edit', $intervention->id) }}" class="btn btn-outline-primary px-4">
                    <i class="bi bi-pencil me-2"></i>Modifier
                </a>
                <a href="{{ route('super-admin.interventions.index') }}" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>

        <div class="row g-4">
            <!-- Informations Principales -->
            <div class="col-lg-8">
                <div class="cp-chart-card h-100">
                    <div class="cp-chart-header d-flex justify-content-between align-items-center">
                        <h6 class="cp-chart-title"><i class="bi bi-info-circle me-2"></i>Informations générales</h6>
                        @php
                            $statutClass = ['planifie' => 'bg-info', 'en_cours' => 'bg-primary', 'termine' => 'bg-success', 'annule' => 'bg-danger'];
                            $statutText = ['planifie' => 'Planifiée', 'en_cours' => 'En cours', 'termine' => 'Terminée', 'annule' => 'Annulée'];
                        @endphp
                        <span class="badge {{ $statutClass[$intervention->statut] ?? 'bg-secondary' }} fs-6 px-3 py-2">
                            {{ $statutText[$intervention->statut] ?? $intervention->statut }}
                        </span>
                    </div>
                    <div class="p-4">
                        <div class="row g-4 overflow-hidden">
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Projet</label>
                                <p class="fs-5 fw-semibold mb-0 text-truncate">{{ $intervention->projet->nom ?? 'Non défini' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Mission</label>
                                <p class="fs-5 fw-semibold mb-0 text-truncate">
                                    @if($intervention->tache_id)
                                        Tâche: {{ $intervention->tache->titre ?? 'N/A' }}
                                    @elseif($intervention->sous_tache_id)
                                        Sous-tâche: {{ $intervention->sousTache->titre ?? 'N/A' }}
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Type d'Intervention</label>
                                <p class="mb-0">
                                    <span class="badge bg-light text-dark fs-6">
                                        {{ ucfirst($intervention->type == 'autre' ? $intervention->type_autre : $intervention->type) }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Date & Heure</label>
                                <p class="fs-5 mb-0"><i class="bi bi-calendar3 me-2 text-primary"></i>{{ $intervention->date_intervention ? date('d/m/Y H:i', strtotime($intervention->date_intervention)) : 'N/A' }}</p>
                            </div>
                            <div class="col-12 mt-4 pt-4 border-top">
                                <label class="text-muted small text-uppercase fw-bold mb-2">Description</label>
                                <div class="bg-light p-3 rounded" style="min-height: 100px; white-space: pre-wrap;">{{ $intervention->description ?? 'Aucune description fournie.' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Intervenant et Autres -->
            <div class="col-lg-4">
                <div class="row g-4">
                    <!-- Personnel assigné -->
                    <div class="col-12">
                        <div class="cp-chart-card">
                            <div class="cp-chart-header">
                                <h6 class="cp-chart-title"><i class="bi bi-person me-2"></i>Personnel Assigné</h6>
                            </div>
                            <div class="p-4 text-center">
                                <div class="mx-auto mb-3 bg-primary text-white d-flex align-items-center justify-content-center rounded-circle shadow-sm" style="width: 80px; height: 80px; font-size: 2rem;">
                                    {{ strtoupper(substr($intervention->technicien->name ?? '?', 0, 1)) }}
                                </div>
                                <h5 class="fw-bold mb-1 text-truncate">{{ $intervention->technicien->name ?? '' }} {{ $intervention->technicien->prenom ?? '' }}</h5>
                                <p class="text-muted mb-0"><i class="bi bi-briefcase me-1"></i>{{ $intervention->technicien->role->nom ?? 'Sans rôle' }}</p>
                                @if($intervention->technicien->email ?? false)
                                    <p class="mt-2 small"><i class="bi bi-envelope me-1"></i>{{ $intervention->technicien->email }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Métadonnées -->
                    <div class="col-12">
                        <div class="cp-chart-card">
                            <div class="cp-chart-header">
                                <h6 class="cp-chart-title"><i class="bi bi-clock-history me-2"></i>Historique</h6>
                            </div>
                            <div class="p-4">
                                <div class="mb-3">
                                    <label class="text-muted small">Créé le</label>
                                    <p class="mb-0 fw-medium">{{ $intervention->created_at->format('d/m/Y à H:i') }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted small">Par</label>
                                    <p class="mb-0 fw-medium">{{ $intervention->creator->name ?? 'Système' }}</p>
                                </div>
                                <div class="mb-0">
                                    <label class="text-muted small">Dernière modification</label>
                                    <p class="mb-0 fw-medium">{{ $intervention->updated_at->format('d/m/Y à H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
