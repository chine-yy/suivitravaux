@extends('layouts.partenaire')

@section('title', 'Mon Équipe')

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">

        <!-- Page Header -->
        <div class="cp-page-header">
            <h1 class="cp-page-title">Mon Équipe</h1>
            <p class="cp-page-subtitle">Détails de l'équipe travaillant sur votre projet.</p>
        </div>

        <!-- Team Section -->
        <div class="cp-card-elevated mb-4">
            <div class="cp-card-header">
                <h5 class="mb-0">Membres de l'équipe</h5>
            </div>
            <div class="cp-card-body">
                @if($projet->admin)
                <div class="d-flex align-items-center gap-3 mb-3">
<div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div>
                            <div class="fw-bold">{{ $projet->admin->prenom }} {{ $projet->admin->nom }}</div>
                            <div class="text-muted small">Chef de Projet</div>
                        </div>
                </div>
                @endif

                @php
                $equipes = $projet->equipes()->with('chef')->get();
                $chefEquipes = $equipes->whereNotNull('chef_equipe_id')->pluck('chef')->filter();
                $allMembers = $projet->membres()->get();
                $regularMembers = $allMembers->whereNotIn('id', $chefEquipes->pluck('id'));
                @endphp

                @if($chefEquipes->count() > 0)
                    @foreach($chefEquipes as $chef)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div>
                            <div class="fw-bold">{{ $chef->prenom }} {{ $chef->nom }}</div>
                            <div class="text-muted small">Chef d'équipe</div>
                        </div>
                    </div>
                    @endforeach
                @endif

                @if($regularMembers->count() > 0)
                    @foreach($regularMembers as $member)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div>
                            <div class="fw-bold">{{ $member->prenom }} {{ $member->nom }}</div>
                            <div class="text-muted small">Membre de l'équipe</div>
                        </div>
                    </div>
                    @endforeach
                @elseif($chefEquipes->count() == 0)
                    <div class="text-muted small">Aucun membre dans l'équipe pour le moment.</div>
                @endif

                <div class="mt-3 pt-3 border-top">
                    <span class="text-muted small">
                        Total: {{ $chefEquipes->count() + $regularMembers->count() }} personne(s) dans l'équipe
                    </span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection