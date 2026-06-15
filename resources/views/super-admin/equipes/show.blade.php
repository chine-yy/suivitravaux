@extends('layouts.super-admin')

@section('title', 'Détails de l\'Équipe - ' . $equipe->nom)

@section('breadcrumb')
    <span class="text-muted"><a href="{{ route('super-admin.equipes.index') }}">Équipes</a></span>
    <span class="mx-2 text-muted">/</span>
    <span class="text-muted">Détails</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <!-- Header -->
        <div class="cp-page-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-people-fill me-2"></i>Équipe: {{ $equipe->nom }}</h1>
                <p class="cp-page-subtitle">Informations, membres et statistiques de l'équipe</p>
            </div>
            <a href="{{ route('super-admin.equipes.index') }}" class="btn btn-outline-secondary px-4">
                <i class="bi bi-arrow-left me-2"></i>Retour
            </a>
        </div>

        <div class="row g-4">
            <!-- Informations de l'équipe -->
            <div class="col-lg-4">
                <div class="cp-chart-card mb-4 h-100">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-info-circle me-2"></i>Informations générales</h6>
                    </div>
                    <div class="p-4">
                        <div class="mb-3">
                            <label class="text-muted small fw-bold">Nom de l'Équipe</label>
                            <div class="fs-5 fw-bold text-dark">{{ $equipe->nom }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small fw-bold">Description</label>
                            <div>{{ $equipe->description ?? 'Aucune description fournie.' }}</div>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="text-muted small fw-bold">Projet Associé</label>
                            <div>
                                @if($equipe->projet)
                                    <span class="badge bg-light text-primary border border-primary px-3 py-2 rounded-pill">
                                        <i class="bi bi-briefcase me-1"></i> {{ $equipe->projet->nom }}
                                    </span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small fw-bold">Rôle assigné</label>
                            <div>
                                @if($equipe->role)
                                    <span class="badge bg-light text-info border border-info px-3 py-2 rounded-pill">{{ $equipe->role->nom }}</span>
                                @else
                                    <span class="text-muted">Standard</span>
                                @endif
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="text-muted small fw-bold">Statut</label>
                            <div>
                                <span class="badge bg-{{ $equipe->statut === 'active' ? 'success' : 'secondary' }} px-3 py-2 rounded-pill">
                                    {{ $equipe->statut === 'active' ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('super-admin.equipes.edit', $equipe->id) }}" class="btn btn-primary">
                                <i class="bi bi-pencil me-2"></i>Modifier cette Équipe
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Membres -->
            <div class="col-lg-8">
                <!-- Chef d'Equipe Highlights -->
                @if($equipe->chef)
                <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #fffbeb, #fef3c7); border-left: 4px solid #84cc16 !important;">
                    <div class="card-body p-4 d-flex align-items-center gap-4">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                            style="width: 60px; height: 60px; background: linear-gradient(135deg, #84cc16, #d97706); font-size: 1.5rem;">
                            {{ strtoupper(substr($equipe->chef->name, 0, 1)) }}{{ strtoupper(substr($equipe->chef->prenom ?? '', 0, 1)) }}
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1 text-dark">{{ $equipe->chef->name }} {{ $equipe->chef->prenom }}</h5>
                            <div class="text-muted small text-uppercase fw-bold d-flex align-items-center mb-1">
                                <i class="bi bi-star-fill text-warning me-1"></i> Chef d'Équipe
                            </div>
                            <div class="small">
                                <span class="me-3"><i class="bi bi-envelope me-1 text-muted"></i>{{ $equipe->chef->email }}</span>
                                <span><i class="bi bi-telephone me-1 text-muted"></i>{{ $equipe->chef->telephone ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="cp-chart-card">
                    <div class="cp-chart-header d-flex justify-content-between align-items-center">
                        <h6 class="cp-chart-title mb-0"><i class="bi bi-person-lines-fill me-2"></i>Membres de l'Équipe ({{ $equipe->users->count() }})</h6>
                    </div>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Nom Prénom</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                    <th>Rôle Global</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($equipe->users->count() > 0)
                                    @foreach($equipe->users as $member)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                                    style="width: 35px; height: 35px; font-size: 0.85rem;">
                                                    {{ strtoupper(substr($member->name, 0, 1)) }}{{ strtoupper(substr($member->prenom ?? '', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $member->name }} {{ $member->prenom }}</div>
                                                    @if($equipe->chef_equipe_id == $member->id)
                                                        <span class="badge bg-warning text-dark" style="font-size: 0.65rem;">Chef d'Équipe</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="text-muted"><i class="bi bi-envelope me-1"></i>{{ $member->email }}</span></td>
                                        <td>{{ $member->telephone ?? '—' }}</td>
                                        <td>
                                            @if($member->role)
                                                <span class="badge bg-light text-dark border">{{ $member->role->nom }}</span>
                                            @else
                                                <span class="text-muted small">Aucun rôle</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="bi bi-person-x display-6 mb-3 d-block"></i>
                                        Il n'y a actuellement aucun membre dans cette équipe.
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
