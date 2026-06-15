@extends('layouts.role-dynamique')

@section('title', 'Modifier Contrat')

@push('styles')
    <style>
        #projet_id option {
            color: #000 !important;
        }
    </style>
@endpush

@section('breadcrumb')
    <a href="{{ route('role-dynamique.contrats.index') }}" class="text-decoration-none">Contrats</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Modifier</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-pencil-square me-2"></i>Modifier Contrat</h1>
                <p class="cp-page-subtitle">Mettez à jour les informations du contrat</p>
            </div>
            <a href="{{ route('role-dynamique.contrats.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-list"></i> Liste des Contrats
            </a>
        </div>


        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-file-earmark-text me-2"></i>Détails du contrat</h6>
            </div>
            <div class="p-4">
                <form action="{{ route('role-dynamique.contrats.update', $contrat->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">N° Contrat <span class="text-danger">*</span></label>
                            <input type="text" name="numero_contrat" class="form-control @error('numero_contrat') is-invalid @enderror" value="{{ old('numero_contrat', $contrat->numero_contrat) }}" required>
                            @error('numero_contrat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="prestation" {{ $contrat->type == 'prestation' ? 'selected' : '' }}>Prestation</option>
                                <option value="marche" {{ $contrat->type == 'marche' ? 'selected' : '' }}>Marché</option>
                                <option value="sous_traitance" {{ $contrat->type == 'sous_traitance' ? 'selected' : '' }}>Sous-traitance</option>
                                <option value="autre" {{ $contrat->type == 'autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                            @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Projet</label>
                            <select name="projet_id" id="projet_id" class="form-select" onchange="updateBudgetRestantContrat(this)">
                                <option value="">-- Aucun --</option>
                                @foreach($projets ?? [] as $projet)
                                    @php
                                        $partenaireIds = collect();
                                        if ($projet->partenaire_id) {
                                            $partenaireIds->push($projet->partenaire_id);
                                        }
                                        foreach ($projet->partenaires as $partenaire) {
                                            $partenaireIds->push($partenaire->id);
                                        }
                                        $partenaireIdsJson = json_encode($partenaireIds->unique()->values()->toArray());
                                    @endphp
                                <option value="{{ $projet->id }}"
                                    data-partenaire-ids="{{ $partenaireIdsJson }}"
                                    data-budget="{{ $projet->dynamic_budget ?? 0 }}"
                                    data-consomme="{{ $projet->dynamic_consomme ?? 0 }}"
                                    {{ $contrat->projet_id == $projet->id ? 'selected' : '' }}>{{ $projet->nom }}</option>
                                @endforeach
                            </select>
                            <div class="form-text" id="contrat_budget_info"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Montant (FCFA)</label>
                            <input type="number" step="0.01" name="montant" id="montant" class="form-control" value="{{ old('montant', $contrat->montant) }}">
                            <div id="montant_error" class="text-danger small mt-1" style="display:none;"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Statut</label>
                            <select name="statut" class="form-select">
                                <option value="brouillon" {{ $contrat->statut == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                                <option value="signe" {{ $contrat->statut == 'signe' ? 'selected' : '' }}>Signé</option>
                                <option value="en_cours" {{ $contrat->statut == 'en_cours' ? 'selected' : '' }}>En cours</option>
                                <option value="termine" {{ $contrat->statut == 'termine' ? 'selected' : '' }}>Terminé</option>
                                <option value="annule" {{ $contrat->statut == 'annule' ? 'selected' : '' }}>Annulé</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date Début</label>
                            <input type="date" name="date_debut" class="form-control" value="{{ $contrat->date_debut ? date('Y-m-d', strtotime($contrat->date_debut)) : '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date Fin</label>
                            <input type="date" name="date_fin" class="form-control" value="{{ $contrat->date_fin ? date('Y-m-d', strtotime($contrat->date_fin)) : '' }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Objet</label>
                            <textarea name="objet" class="form-control" rows="3">{{ $contrat->objet }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Conditions</label>
                            <textarea name="conditions" class="form-control" rows="3">{{ $contrat->conditions }}</textarea>
                        </div>
                    </div>
                    <div class="d-flex gap-2 pt-4 border-top">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check2 me-2"></i>Enregistrer
                        </button>
                        <a href="{{ route('role-dynamique.contrats.index') }}" class="btn btn-outline-secondary px-4">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        let montantRestant = 0;

        function checkMontant() {
            const montantInput = document.getElementById('montant');
            const errorDiv = document.getElementById('montant_error');
            if (!montantInput || !errorDiv) return;
            const valeur = parseFloat(montantInput.value);
            if (valeur > montantRestant && montantRestant >= 0) {
                errorDiv.textContent = 'Le montant ne peut pas dépasser le restant disponible de ' + montantRestant.toLocaleString('fr-FR') + ' FCFA.';
                errorDiv.style.display = 'block';
            } else {
                errorDiv.style.display = 'none';
            }
        }

        function updateBudgetRestantContrat(select) {
            const opt = select.options[select.selectedIndex];
            const budget = parseFloat(opt.dataset.budget) || 0;
            const consomme = parseFloat(opt.dataset.consomme) || 0;
            montantRestant = Math.max(0, budget - consomme);
            const info = document.getElementById('contrat_budget_info');
            const montantInput = document.getElementById('montant');
            if (!info) return;

            if (!opt.value) {
                info.innerHTML = '';
                if (montantInput) montantInput.removeAttribute('max');
                return;
            }

            if (budget > 0) {
                const color = montantRestant <= 0 ? 'text-danger' : 'text-success';
                info.innerHTML = 'Budget alloué : <strong>' + budget.toLocaleString('fr-FR') + ' FCF</strong> — Restant : <strong class="' + color + '">' + montantRestant.toLocaleString('fr-FR') + ' FCF</strong>';
                if (montantInput) {
                    montantInput.max = montantRestant;
                }
            } else {
                info.innerHTML = '<span class="text-danger fw-bold"><i class="bi bi-exclamation-triangle"></i> Veuillez allouer d\'abord une somme pour le projet "' + opt.text + '"</span>';
                if (montantInput) montantInput.removeAttribute('max');
            }
            checkMontant();
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('montant')?.addEventListener('input', checkMontant);
        });
    </script>
@endpush
