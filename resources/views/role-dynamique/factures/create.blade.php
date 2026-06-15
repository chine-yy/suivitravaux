@extends('layouts.role-dynamique')

@section('title', 'Nouvelle Facture')

@push('styles')
    <style>
        #projet_id option {
            color: #000 !important;
        }
    </style>
@endpush

@section('breadcrumb')
    <a href="{{ route('role-dynamique.factures.index') }}" class="text-decoration-none">Factures</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Nouveau</span>
@endsection

@section('content')
    <div class="cp-dashboard">
        <div class="cp-content">
            <div class="cp-page-header">
                <div>
                    <h1 class="cp-page-title"><i class="bi bi-plus-circle me-2"></i>Nouvelle Facture</h1>
                    <p class="cp-page-subtitle">Créez une nouvelle facture</p>
                </div>
                <a href="{{ route('role-dynamique.factures.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-list"></i> Liste des Factures
                </a>
            </div>


            <div class="cp-chart-card">
                <div class="cp-chart-header">
                    <h6 class="cp-chart-title"><i class="bi bi-receipt me-2"></i>Détails de la facture</h6>
                </div>
                <div class="p-4">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('role-dynamique.factures.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">N° Facture</label>
                                <input type="text" name="numero_facture"
                                    class="form-control @error('numero_facture') is-invalid @enderror"
                                    value="{{ old('numero_facture') }}"
                                    placeholder="Laisser vide pour une génération automatique (ex: 0001)">
                                @error('numero_facture') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-select" required>
                                    <option value="facture" {{ old('type') == 'facture' ? 'selected' : '' }}>Facture</option>
                                    <option value="avoir" {{ old('type') == 'avoir' ? 'selected' : '' }}>Avoir</option>
                                    <option value="proforma" {{ old('type') == 'proforma' ? 'selected' : '' }}>Proforma
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Projet</label>
                                <select name="projet_id" id="projet_id" class="form-select" onchange="updateBudgetRestantFacture(this)">
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
                                            {{ old('projet_id') == $projet->id ? 'selected' : '' }}>
                                            {{ $projet->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text" id="facture_budget_info"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Montant HT (FCFA)</label>
                                <input type="number" step="0.01" name="montant_ht" class="form-control"
                                    value="{{ old('montant_ht') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">TVA (FCFA)</label>
                                <input type="number" step="0.01" name="montant_tva" class="form-control"
                                    value="{{ old('montant_tva') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Montant TTC (FCFA)</label>
                                <input type="number" step="0.01" name="montant_ttc" id="montant_ttc"
                                    class="form-control @error('montant_ttc') is-invalid @enderror"
                                    value="{{ old('montant_ttc') }}">
                                @error('montant_ttc') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Date Émission</label>
                                <input type="date" name="date_emission" class="form-control"
                                    value="{{ old('date_emission') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Date Échéance</label>
                                <input type="date" name="date_echeance" class="form-control"
                                    value="{{ old('date_echeance') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Statut Paiement</label>
                                <select name="statut_paiement" class="form-select">
                                    <option value="en_attente" {{ old('statut_paiement', 'en_attente') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                    <option value="paye" {{ old('statut_paiement') == 'paye' ? 'selected' : '' }}>Payé
                                    </option>
                                    <option value="en_retard" {{ old('statut_paiement') == 'en_retard' ? 'selected' : '' }}>En
                                        retard</option>
                                    <option value="annule" {{ old('statut_paiement') == 'annule' ? 'selected' : '' }}>Annulé
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Mode Paiement</label>
                                <select name="mode_paiement" class="form-select">
                                    <option value="">-- Sélectionner --</option>
                                    <option value="virement" {{ old('mode_paiement') == 'virement' ? 'selected' : '' }}>
                                        Virement</option>
                                    <option value="cheque" {{ old('mode_paiement') == 'cheque' ? 'selected' : '' }}>Chèque
                                    </option>
                                    <option value="especes" {{ old('mode_paiement') == 'especes' ? 'selected' : '' }}>Espèces
                                    </option>
                                    <option value="carte" {{ old('mode_paiement') == 'carte' ? 'selected' : '' }}>Carte
                                        bancaire</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Notes</label>
                                <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                        <div class="d-flex gap-2 pt-4 border-top">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check2 me-2"></i>Enregistrer
                            </button>
                            <a href="{{ route('role-dynamique.factures.index') }}"
                                class="btn btn-outline-secondary px-4">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function updateBudgetRestantFacture(select) {
            const opt = select.options[select.selectedIndex];
            const budget = parseFloat(opt.dataset.budget) || 0;
            const consomme = parseFloat(opt.dataset.consomme) || 0;
            const restant = Math.max(0, budget - consomme);
            const info = document.getElementById('facture_budget_info');
            const montantInput = document.getElementById('montant_ttc');
            if (!info) return;

            if (!opt.value) {
                info.innerHTML = '';
                if (montantInput) montantInput.removeAttribute('max');
                return;
            }

            if (budget > 0) {
                const color = restant <= 0 ? 'text-danger' : 'text-success';
                info.innerHTML = 'Budget alloué : <strong>' + budget.toLocaleString('fr-FR') + ' FCF</strong> — Restant : <strong class="' + color + '">' + restant.toLocaleString('fr-FR') + ' FCF</strong>';
                if (montantInput) {
                    montantInput.max = restant;
                    const currentValue = parseFloat(montantInput.value || '0');
                    if (!isNaN(currentValue) && currentValue > restant) {
                        montantInput.value = restant;
                    }
                }
            } else {
                info.innerHTML = '<span class="text-danger fw-bold"><i class="bi bi-exclamation-triangle"></i> Veuillez allouer d\'abord une somme pour le projet "' + opt.text + '"</span>';
                if (montantInput) montantInput.removeAttribute('max');
            }
        }
    </script>
@endpush