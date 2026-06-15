@extends('layouts.super-admin')

@section('title', 'Détails de la Phase')

@section('breadcrumb')
<a href="{{ route('super-admin.phases.index') }}" class="text-decoration-none">Phases</a>
<span class="cp-breadcrumb-separator">/</span>
<span class="cp-breadcrumb-item">Détails</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-kanban me-2"></i>{{ $phase->nom }}</h1>
                <p class="cp-page-subtitle">Phase du projet: <strong>{{ $phase->projet->nom ?? 'N/A' }}</strong></p>
            </div>
            <div class="d-flex gap-2">
                @include('partials.row-export', ['id' => $phase->id, 'prefix' => 'phase', 'title' => 'Détails de la Phase'])
                <a href="{{ route('super-admin.phases.edit', $phase->id) }}" class="btn btn-primary px-4">
                    <i class="bi bi-pencil me-2"></i>Modifier
                </a>
                <form action="{{ route('super-admin.phases.destroy', $phase->id) }}" method="POST" onsubmit="return confirm('Supprimer cette phase ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger px-4">
                        <i class="bi bi-trash me-2"></i>Supprimer
                    </button>
                </form>
            </div>
        </div>

        @include('partials.alerts')

        <div class="cp-chart-card mb-4">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-info-circle me-2"></i>Informations</h6>
            </div>
            <div class="p-4">
                <div class="row">
                    <div class="col-md-4">
                        <p><strong>Date de début:</strong> {{ $phase->date_debut ? \Carbon\Carbon::parse($phase->date_debut)->format('d/m/Y') : '-' }}</p>
                        <p><strong>Date fin prévue:</strong> {{ $phase->date_fin_prevue ? \Carbon\Carbon::parse($phase->date_fin_prevue)->format('d/m/Y') : '-' }}</p>
                    </div>
                    <div class="col-md-8">
                        <p><strong>Description:</strong></p>
                        <p>{{ $phase->description ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-list-ul me-2"></i>Tâches liées</h6>
            </div>
            <div class="p-4">
                @if($phase->taches && $phase->taches->isNotEmpty())
                    <ul class="list-group">
                        @foreach($phase->taches as $tache)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $tache->titre }}</strong>
                                    <div class="small text-muted">Projet: {{ $tache->projet->nom ?? 'N/A' }}</div>
                                </div>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('super-admin.taches.edit', $tache->id) }}" class="btn btn-sm btn-outline-primary" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="{{ route('super-admin.taches.show', $tache->id) }}" class="btn btn-sm btn-outline-secondary" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">Aucune tâche liée à cette phase.</p>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
