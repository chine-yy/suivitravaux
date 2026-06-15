@extends('layouts.super-admin')

@section('title', 'Détails de l\'Incident')

@section('breadcrumb')
    <a href="{{ route('super-admin.incidents.index') }}" class="text-decoration-none">Incidents</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Détails #{{ $incident->id }}</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-info-circle me-2 text-green"></i>Détails de l'Incident</h1>
                <p class="cp-page-subtitle">Consultez les informations complètes sur cet incident</p>
            </div>
            <div class="d-flex gap-2">
                @include('partials.row-export', ['id' => $incident->id, 'prefix' => 'incident', 'title' => 'Détails de l\'Incident'])
                <a href="{{ route('super-admin.incidents.edit', $incident->id) }}" class="btn btn-green btn-with-border">
                    <i class="bi bi-pencil me-2"></i>Modifier
                </a>
                <a href="{{ route('super-admin.incidents.index') }}" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>


        <div class="row g-4">
            <!-- Main Content: Description & Details -->
            <div class="col-lg-8">
                <div class="cp-chart-card mb-4">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title">Description de l'Incident</h6>
                    </div>
                    <div class="p-4">
                        <h4 class="fw-bold mb-3">{{ $incident->titre }}</h4>
                        <div class="incident-description bg-light p-4 rounded border">
                            {{ $incident->description }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar: Metadata & Status -->
            <div class="col-lg-4">
                <div class="cp-chart-card mb-4">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title">Informations Clés</h6>
                    </div>
                    <div class="p-4">
                        <div class="mb-4">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-1">Projet</label>
                            <div class="d-flex align-items-center">
                                <div class="bg-green-soft p-2 rounded me-3">
                                    <i class="bi bi-building text-green"></i>
                                </div>
                                <span class="fw-semibold">{{ $incident->projet->nom ?? 'N/A' }}</span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-1">Signalé par</label>
                            <div class="d-flex align-items-center">
                                <div class="bg-blue-soft p-2 rounded me-3">
                                    <i class="bi bi-person text-primary"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $incident->signalePar->name ?? 'Système' }}</div>
                                    <div class="small text-muted">{{ $incident->signalePar->role->nom ?? '' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-1">Gravité</label>
                            @php
                                $gravityClass = [
                                    'faible' => 'bg-info',
                                    'moyen' => 'bg-warning text-dark',
                                    'critique' => 'bg-danger'
                                ][$incident->gravite] ?? 'bg-secondary';
                            @endphp
                            <span class="badge {{ $gravityClass }} px-3 py-2 text-uppercase fw-bold">
                                {{ $incident->gravite }}
                            </span>
                        </div>

                        <div class="mb-4">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-1">Statut Actuel</label>
                            @php
                                $statusClass = [
                                    'ouvert' => 'bg-danger-soft text-danger border-danger',
                                    'en_traitement' => 'bg-warning-soft text-warning border-warning',
                                    'resolu' => 'bg-success-soft text-success border-success'
                                ][$incident->statut] ?? 'bg-secondary-soft text-secondary border-secondary';
                            @endphp
                            <span class="badge border {{ $statusClass }} px-3 py-2 text-uppercase fw-bold">
                                {{ str_replace('_', ' ', $incident->statut) }}
                            </span>
                        </div>

                        <div class="mb-0">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-1">Date de Signalement</label>
                            <div class="fw-semibold">
                                <i class="bi bi-calendar3 me-2 text-green"></i>{{ $incident->created_at->format('d F Y à H:i') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .text-green { color: #009A44 !important; }
    .bg-green-soft { background-color: rgba(0, 154, 68, 0.1); }
    .bg-blue-soft { background-color: rgba(0, 123, 255, 0.1); }
    .bg-danger-soft { background-color: rgba(220, 53, 69, 0.1); }
    .bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); }
    .bg-success-soft { background-color: rgba(40, 167, 69, 0.1); }
    .btn-green { background: #009A44; color: white; border: none; transition: all 0.3s ease; }
    .btn-green:hover { background: #007a35; color: white; }
    .btn-with-border { border: 2px solid #009A44 !important; }
    .incident-description { line-height: 1.8; color: #444; min-height: 200px; white-space: pre-line; }
    .cp-breadcrumb-separator { margin: 0 0.5rem; color: #6c757d; }
    .cp-breadcrumb-item { color: #009A44; font-weight: 600; }
</style>
@endpush
