@extends('layouts.super-admin')

@section('title', 'Satisfaction Partenaire')

@section('breadcrumb')
<span class="text-muted">Satisfaction</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
<div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-star me-2"></i>Satisfaction Partenaire</h1>
                <p class="cp-page-subtitle">Gérez vos enquêtes de satisfaction</p>
            </div>
            <div class="d-flex gap-2">
                @if($has('exporter-pdf-satisfaction-partenaire'))
                <button class="btn btn-outline-danger" onclick="exportToPdf('satisfactionTable', 'Liste des satisfactions', 'satisfactions_export')">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Exporter tout
                </button>
                @endif
            </div>
        </div>


        @php
        $avgNote = $satisfactions->count() > 0 ? round($satisfactions->avg('note'), 1) : 0;
        @endphp

        <div class="cp-stats-grid mb-4">
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-success"><i class="bi bi-emoji-smile"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ $avgNote }}/5</div>
                    <div class="cp-stat-label">Note Moyenne</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-info"><i class="bi bi-list-check"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ $satisfactions->count() }}</div>
                    <div class="cp-stat-label">Enquêtes Envoyées</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-green"><i class="bi bi-reply"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ $satisfactions->where('statut', 'repondu')->count() }}</div>
                    <div class="cp-stat-label">Réponses Reçues</div>
                </div>
            </div>
        </div>

        <!-- Filtre -->
        <div class="cp-chart-card mb-4">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-filter me-2"></i>Filtres de recherche</h6>
            </div>
            <div class="p-4">
                <form action="{{ route('super-admin.satisfaction.index') }}" method="GET" class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label small fw-bold">Partenaire</label>
                        <select name="partenaire_id" class="form-select form-select-sm">
                            <option value="">Tous les partenaires</option>
                            @foreach($partenaires as $c)
                            <option value="{{ $c->id }}" {{ request('partenaire_id')==$c->id ? 'selected' : '' }}>
                                {{ $c->nom }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-bold">Note</label>
                        <select name="note" class="form-select form-select-sm">
                            <option value="">Toutes les notes</option>
                            @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ request('note')==$i ? 'selected' : '' }}>{{ $i }} Étoile(s)
                            </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="bi bi-search me-1"></i> Filtrer
                        </button>
                        <a href="{{ route('super-admin.satisfaction.index') }}"
                            class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-list-ul me-2"></i>Liste des Enquêtes</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="satisfactionTable">
                    <thead>
                        <tr style="background: rgba(99,102,241,.08);">
                            <th>Partenaire</th>
                            <th>Projet</th>
                            <th>Note</th>
                            <th>Commentaire</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($satisfactions as $satisfaction)
                        <tr>
                            <td>{{ $satisfaction->partenaire->nom ?? 'N/A' }}</td>
                            <td>{{ $satisfaction->projet->nom ?? 'N/A' }}</td>
                            <td>
                                @for($i = 1; $i <= 5; $i++) <i
                                    class="bi {{ $i <= $satisfaction->note ? 'bi-star-fill text-primary' : 'bi-star text-muted' }}">
                                    </i>
                                    @endfor
                            </td>
                            <td>
                                <span class="text-muted small">{{ Str::limit($satisfaction->commentaire, 40) ?? 'N/A'
                                    }}</span>
                            </td>
                            <td>{{ $satisfaction->date_envoi ? date('d/m/Y', strtotime($satisfaction->date_envoi)) :
                                'N/A' }}</td>
                            <td>
                                @php
                                $statutClass = ['envoye' => 'bg-info', 'repondu' => 'bg-success', 'expire' =>
                                'bg-secondary'];
                                $statutText = ['envoye' => 'Envoyé', 'repondu' => 'Répondu', 'expire' => 'Expiré'];
                                @endphp
                                <span class="badge {{ $statutClass[$satisfaction->statut] ?? 'bg-secondary' }}">{{
                                    $statutText[$satisfaction->statut] ?? $satisfaction->statut }}</span>
                            </td>
<td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="{{ route('super-admin.satisfaction.show', $satisfaction->id) }}"
                                        class="btn btn-sm btn-outline-info" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('super-admin.export.pdf.direct', ['type' => 'satisfaction', 'id' => $satisfaction->id]) }}"
                                        class="btn btn-sm btn-outline-secondary" title="Télécharger PDF">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-star display-4"></i>
                                <p class="mt-3">Aucune enquête trouvée</p>
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
