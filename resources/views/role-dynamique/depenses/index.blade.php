@extends('layouts.role-dynamique')

@section('title', 'Module Dépenses')

@section('breadcrumb')
<span class="text-muted">Dépenses</span>
@endsection

@section('content')
<div class="cp-budget">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title">Module Dépenses</h1>
                <p class="cp-page-subtitle">Gestion des dépenses pour les rôles dynamiques </p>
            </div>
            <div class="d-flex gap-2">
                @if($canViewHistorique ?? false)
                    <a href="{{ route('role-dynamique.historique.index') }}" class="btn btn-outline-info">
                        <i class="bi bi-clock-history me-2"></i>Historique
                    </a>
                @endif
                @if($canAllocProject ?? false)
                    <a href="{{ route('role-dynamique.allocation-projet.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-diagram-3 me-2"></i>Allocation Projet
                    </a>
                @endif
                @if($canAllocST ?? false)
                    <a href="{{ route('role-dynamique.allocation-sous-traitance.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-people me-2"></i>Allocation Sous-Traitance
                    </a>
                @endif
            </div>
        </div>

        @include('role-dynamique.depenses.module', [
            'routePrefix' => 'role-dynamique',
            'canManageDepenses' => $canManageDepenses ?? false,
            'canDeleteDepenses' => $canDeleteDepenses ?? false,
        ])
    </div>
</div>
@endsection
