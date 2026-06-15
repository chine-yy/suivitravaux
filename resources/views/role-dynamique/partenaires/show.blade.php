@extends('layouts.role-dynamique')

@section('title', 'Détails du Partenaire')

@section('breadcrumb')
    <a href="{{ route('role-dynamique.partenaires.index') }}" class="text-decoration-none">Partenaires</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Détails #{{ $partenaire->id }}</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-person-workspace me-2 text-green"></i>Détails du Partenaire</h1>
                <p class="cp-page-subtitle">Consultez les informations complètes sur ce partenaire</p>
            </div>
            <div class="d-flex gap-2">
                @if($has('exporter-pdf-partenaires'))
                @include('partials.row-export', ['id' => $partenaire->id, 'prefix' => 'partenaire', 'title' => 'Détail - Partenaire'])
                @endif
                @if($has('edit-partenaires'))
                <a href="{{ route('role-dynamique.partenaires.edit', $partenaire->id) }}" class="btn btn-green btn-with-border">
                    <i class="bi bi-pencil me-2"></i>Modifier
                </a>
                @endif
                <a href="{{ route('role-dynamique.partenaires.index') }}" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>


        <div class="row g-4">
            <!-- Main Content: Partenaire Details -->
            <div class="col-lg-8">
                <div class="cp-chart-card mb-4">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title">Informations du Partenaire</h6>
                    </div>
                    <div class="p-4">
                        <h4 class="fw-bold mb-3">{{ $partenaire->name }} {{ $partenaire->prenom }}</h4>
                        <div class="partenaire-details bg-light p-4 rounded border">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="fw-bold text-muted">Email:</label>
                                    <p class="mb-0">{{ $partenaire->email }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="fw-bold text-muted">Téléphone:</label>
                                    <p class="mb-0">{{ $partenaire->telephone ?? 'Non renseigné' }}</p>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-12">
                                    <label class="fw-bold text-muted">Projet associé:</label>
                                    <div class="d-flex align-items-center mt-2">
                                        <div class="bg-green-soft p-2 rounded me-3">
                                            <i class="bi bi-briefcase text-green"></i>
                                        </div>
                                        <span class="fw-semibold">{{ $partenaire->projet->nom ?? 'Aucun projet' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar: Metadata -->
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
                                    <i class="bi bi-briefcase text-green"></i>
                                </div>
                                <span class="fw-semibold">{{ $partenaire->projet->nom ?? 'N/A' }}</span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-1">Date de création</label>
                            <div class="fw-semibold">
                                <i class="bi bi-calendar3 me-2 text-green"></i>{{ $partenaire->created_at->format('d F Y à H:i') }}
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-1">Dernière mise à jour</label>
                            <div class="fw-semibold">
                                <i class="bi bi-clock-history me-2 text-green"></i>{{ $partenaire->updated_at->format('d F Y à H:i') }}
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-1">ID Partenaire</label>
                            <div class="fw-semibold text-green">#{{ $partenaire->id }}</div>
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
    .text-green { color: #ff8a00 !important; }
    .bg-green-soft { background-color: rgba(255, 138, 0, 0.1); }
    .bg-blue-soft { background-color: rgba(0, 123, 255, 0.1); }
    .btn-green { background: linear-gradient(135deg, #ff8a00, #ffb300); color: white; border: none; transition: all 0.3s ease; }
    .btn-green:hover { background: linear-gradient(135deg, #e67c00, #e6a100); color: white; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(255, 138, 0, 0.3); }
    .btn-with-border { border: 2px solid #ff8a00 !important; }
    .btn-with-border:hover { border-color: #e67c00 !important; }
    .partenaire-details { line-height: 1.8; color: #444; min-height: 200px; }
    .cp-breadcrumb-separator { margin: 0 0.5rem; color: #6c757d; }
    .cp-breadcrumb-item { color: #ff8a00; font-weight: 600; }
</style>
@endpush
