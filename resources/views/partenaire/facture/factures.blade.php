@extends('layouts.partenaire')

@section('title', 'Mes Factures')

@section('breadcrumb')
<span class="text-muted">Factures</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-receipt me-2"></i>Mes Factures</h1>
                <p class="cp-page-subtitle">Consultation des factures relatives à votre projet</p>
            </div>
        </div>

        @php
        $totalFacture = $factures->sum('montant_ttc');
        $totalPaye = $factures->where('statut_paiement', 'paye')->sum('montant_ttc');
        $totalAttente = $factures->where('statut_paiement', 'en_attente')->sum('montant_ttc');
        @endphp

        <div class="cp-stats-grid mb-4">
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-primary"><i class="bi bi-cash-stack"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ number_format($totalFacture, 0, ',', ' ') }}</div>
                    <div class="cp-stat-label">Total Facturé (FCFA)</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-success"><i class="bi bi-check-circle"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ number_format($totalPaye, 0, ',', ' ') }}</div>
                    <div class="cp-stat-label">Payé (FCFA)</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-green"><i class="bi bi-hourglass-split"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ number_format($totalAttente, 0, ',', ' ') }}</div>
                    <div class="cp-stat-label">En Attente (FCFA)</div>
                </div>
            </div>
        </div>

        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-list-ul me-2"></i>Liste des Factures</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="facturesTable">
                    <thead>
                        <tr style="background: rgba(99,102,241,.08);">
                            <th>N° Facture</th>
                            <th>Projet</th>
                            <th>Montant TTC</th>
                            <th>Date d'émission</th>
                            <th>Date d'échéance</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($factures as $facture)
                        <tr>
                            <td><strong>{{ $facture->numero_facture }}</strong></td>
                            <td>{{ $facture->projet->nom ?? 'N/A' }}</td>
                            <td><strong>{{ number_format($facture->montant_ttc, 0, ',', ' ') }} FCFA</strong></td>
                            <td>{{ $facture->date_emission ? date('d/m/Y', strtotime($facture->date_emission)) : 'N/A' }}</td>
                            <td>{{ $facture->date_echeance ? date('d/m/Y', strtotime($facture->date_echeance)) : 'N/A' }}</td>
                            <td>
                                @php
                                $paiementClass = ['en_attente' => 'bg-primary', 'paye' => 'bg-success', 'en_retard' => 'bg-danger', 'annule' => 'bg-secondary'];
                                $paiementText = ['en_attente' => 'En attente', 'paye' => 'Payé', 'en_retard' => 'En retard', 'annule' => 'Annulé'];
                                @endphp
                                <span class="badge {{ $paiementClass[$facture->statut_paiement] ?? 'bg-secondary' }}">
                                    {{ $paiementText[$facture->statut_paiement] ?? $facture->statut_paiement }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <button type="button" class="btn btn-sm btn-outline-warning" title="Voir la facture" data-bs-toggle="modal" data-bs-target="#factureModal{{ $facture->id }}">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <a href="{{ route('super-admin.export.pdf.direct', ['type' => 'facture', 'id' => $facture->id]) }}"
                                        class="btn btn-sm btn-outline-primary" title="Télécharger PDF">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <div class="modal fade" id="factureModal{{ $facture->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content border-0 rounded-4">
                                    <div class="modal-header border-bottom-0 py-4 px-4">
                                        <h5 class="modal-title fw-bold">Facture {{ $facture->numero_facture }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <h6 class="text-muted small text-uppercase mb-2">N° Facture</h6>
                                                <p class="fw-bold text-dark fs-5">{{ $facture->numero_facture }}</p>
                                            </div>
                                            <div class="col-md-6 text-md-end">
                                                <h6 class="text-muted small text-uppercase mb-2">Projet</h6>
                                                <p class="fw-bold text-dark fs-5">{{ $facture->projet->nom ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col-md-4">
                                                <h6 class="text-muted small text-uppercase mb-2">Montant HT</h6>
                                                <p class="fw-bold">{{ number_format($facture->montant_ht, 0, ',', ' ') }} FCFA</p>
                                            </div>
                                            <div class="col-md-4">
                                                <h6 class="text-muted small text-uppercase mb-2">TVA</h6>
                                                <p class="fw-bold">{{ number_format($facture->montant_tva, 0, ',', ' ') }} FCFA</p>
                                            </div>
                                            <div class="col-md-4">
                                                <h6 class="text-muted small text-uppercase mb-2">Montant TTC</h6>
                                                <p class="fw-bold text-success">{{ number_format($facture->montant_ttc, 0, ',', ' ') }} FCFA</p>
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <h6 class="text-muted small text-uppercase mb-2">Date d'émission</h6>
                                                <p class="fw-bold">{{ $facture->date_emission ? date('d/m/Y', strtotime($facture->date_emission)) : 'N/A' }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="text-muted small text-uppercase mb-2">Date d'échéance</h6>
                                                <p class="fw-bold">{{ $facture->date_echeance ? date('d/m/Y', strtotime($facture->date_echeance)) : 'N/A' }}</p>
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <h6 class="text-muted small text-uppercase mb-2">Statut de paiement</h6>
                                                <span class="badge {{ $paiementClass[$facture->statut_paiement] ?? 'bg-secondary' }} fs-6">{{ $paiementText[$facture->statut_paiement] ?? $facture->statut_paiement }}</span>
                                            </div>
                                            @if($facture->mode_paiement)
                                            <div class="col-md-6">
                                                <h6 class="text-muted small text-uppercase mb-2">Mode de paiement</h6>
                                                <p class="fw-bold">{{ $facture->mode_paiement }}</p>
                                            </div>
                                            @endif
                                        </div>
                                        @if($facture->notes)
                                        <div class="bg-light rounded-4 p-4">
                                            <h6 class="fw-bold text-dark mb-2">Notes</h6>
                                            <p class="text-muted mb-0">{{ $facture->notes }}</p>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer border-top-0 p-4">
                                        <button type="button" class="btn px-4 py-2" style="background-color: #6c757d; color: white; border-radius: 8px;" data-bs-dismiss="modal">Fermer</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-receipt display-4"></i>
                                <p class="mt-3">Aucune facture trouvée</p>
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