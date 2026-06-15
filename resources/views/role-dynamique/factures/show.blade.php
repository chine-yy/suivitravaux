@extends('layouts.role-dynamique')

@section('title', 'Détail de la Facture - ' . $facture->numero_facture)

@section('breadcrumb')
    <span class="text-muted"><a href="{{ route('role-dynamique.factures.index') }}">Factures</a></span>
    <span class="mx-2 text-muted">/</span>
    <span class="text-muted">Détails</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <!-- Header -->
        <div class="cp-page-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-receipt me-2"></i>Facture {{ $facture->numero_facture }}</h1>
                <p class="cp-page-subtitle">Informations, montants et statut de paiement</p>
            </div>
            <div class="d-flex gap-2">
                <!-- On pourrait ajouter un bouton de telechargement PDF ici plus tard -->
                <button class="btn btn-outline-primary px-3" onclick="window.print()">
                    <i class="bi bi-printer me-2"></i>Imprimer
                </button>
                <a href="{{ route('role-dynamique.factures.edit', $facture->id) }}" class="btn btn-primary px-4">
                    <i class="bi bi-pencil me-2"></i>Modifier
                </a>
                <a href="{{ route('role-dynamique.factures.index') }}" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>

        <div class="row g-4 print-container">
            <!-- Informations Globales -->
            <div class="col-lg-8">
                <div class="cp-chart-card mb-4 border-0 shadow-sm print-card">
                    <div class="cp-chart-header border-bottom px-4 py-3 d-flex justify-content-between align-items-center bg-light">
                        <h6 class="cp-chart-title mb-0 text-uppercase fw-bold"><i class="bi bi-info-circle me-2"></i>Détails de la Facture</h6>
                        <span class="badge bg-{{ $facture->type === 'avoir' ? 'warning text-dark' : ($facture->type === 'proforma' ? 'info' : 'primary') }} px-3 py-2 rounded-pill">
                            {{ ucfirst($facture->type) }}
                        </span>
                    </div>
                    <div class="p-4">
                        <div class="row mb-5">
                            <div class="col-sm-6 mb-4 mb-sm-0">
                                <label class="text-muted text-uppercase small fw-bold mb-2">Informations Générales</label>
                                <div class="mb-2"><strong>Délivré le :</strong> {{ $facture->date_emission ? date('d/m/Y', strtotime($facture->date_emission)) : 'Non définie' }}</div>
                                <div class="mb-2"><strong>Date d'échéance :</strong> <span class="text-danger">{{ $facture->date_echeance ? date('d/m/Y', strtotime($facture->date_echeance)) : 'Non définie' }}</span></div>
                                <div><strong>Référence Contrat :</strong> {{ $facture->contrat->numero_contrat ?? 'N/A' }}</div>
                            </div>
                            <div class="col-sm-6 text-sm-end">
                                <label class="text-muted text-uppercase small fw-bold mb-2 text-start text-sm-end d-block">Destinataire (Partenaire)</label>
                                @if($facture->partenaire)
                                    <div class="fs-5 fw-bold text-dark">{{ $facture->partenaire->prenom ?? '' }} {{ $facture->partenaire->nom ?? '' }}</div>
                                    <div class="text-muted">{{ $facture->partenaire->email }}</div>
                                    <div class="text-muted">{{ $facture->partenaire->telephone ?? 'Téléphone non spécifié' }}</div>
                                @else
                                    <div class="text-muted fst-italic">Aucun partenaire spécifié</div>
                                @endif
                            </div>
                        </div>

                        <!-- Montants -->
                        <div class="table-responsive mb-4 mt-2">
                            <table class="table table-bordered mb-0">
                                <thead style="background-color: #f8fafc;">
                                    <tr>
                                        <th class="text-muted text-uppercase small py-3 px-4 w-50">Désignation</th>
                                        <th class="text-muted text-uppercase small py-3 px-4 text-end">Montant (FCFA)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="py-3 px-4">Montant HT (Hors Taxes)</td>
                                        <td class="py-3 px-4 text-end fs-5">{{ number_format($facture->montant_ht, 0, ',', ' ') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 px-4 text-muted">TVA</td>
                                        <td class="py-3 px-4 text-end text-muted">{{ number_format($facture->montant_tva, 0, ',', ' ') }}</td>
                                    </tr>
                                    <tr style="background-color: rgba(99,102,241,.05);">
                                        <td class="py-4 px-4"><strong class="fs-5 text-dark">Total TTC à payer</strong></td>
                                        <td class="py-4 px-4 text-end">
                                            <strong class="fs-3 text-primary">{{ number_format($facture->montant_ttc, 0, ',', ' ') }}</strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        @if($facture->notes)
                        <div class="bg-light p-4 rounded-3 mt-4 border">
                            <h6 class="text-uppercase small fw-bold text-muted mb-2"><i class="bi bi-chat-left-text me-2"></i>Notes / Conditions</h6>
                            <div>{!! nl2br(e($facture->notes)) !!}</div>
                        </div>
                        @else
                        <div class="text-muted fst-italic mt-4 small"><i class="bi bi-info-circle me-1"></i>Aucune note spécifique n'a été ajoutée à cette facture.</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Details Latéraux -->
            <div class="col-lg-4">
                <div class="cp-chart-card mb-4 border-0 shadow-sm h-100">
                    <div class="cp-chart-header border-bottom px-4 py-3 bg-light">
                        <h6 class="cp-chart-title mb-0 text-uppercase fw-bold"><i class="bi bi-bookmark-star me-2"></i>Paiement & Statut</h6>
                    </div>
                    <div class="p-4">
                        <div class="text-center mb-5 mt-2">
                            @php
                                $paiementClass = [
                                    'en_attente' => 'bg-primary text-white', 
                                    'paye' => 'bg-success text-white', 
                                    'en_retard' => 'bg-danger text-white', 
                                    'annule' => 'bg-secondary text-white'
                                ];
                                $paiementIcon = [
                                    'en_attente' => 'bi-hourglass-split', 
                                    'paye' => 'bi-check-circle-fill', 
                                    'en_retard' => 'bi-exclamation-triangle-fill', 
                                    'annule' => 'bi-x-circle-fill'
                                ];
                                $paiementText = [
                                    'en_attente' => 'En attente de paiement', 
                                    'paye' => 'Facture Payée', 
                                    'en_retard' => 'Paiement en retard', 
                                    'annule' => 'Annulée'
                                ];
                            @endphp
                            
                            <div class="rounded-circle d-flex mx-auto justify-content-center align-items-center mb-3 {{ $paiementClass[$facture->statut_paiement] ?? 'bg-secondary' }}" style="width: 80px; height: 80px; font-size: 2rem;">
                                <i class="bi {{ $paiementIcon[$facture->statut_paiement] ?? 'bi-file-earmark' }}"></i>
                            </div>
                            
                            <h5 class="fw-bold mb-1">{{ $paiementText[$facture->statut_paiement] ?? ucfirst($facture->statut_paiement) }}</h5>
                            
                            @if($facture->statut_paiement === 'en_retard' && $facture->date_echeance)
                                @php
                                    $daysOverdue = \Carbon\Carbon::parse($facture->date_echeance)->diffInDays(now(), false);
                                @endphp
                                @if($daysOverdue > 0)
                                    <div class="small text-danger fw-bold mt-2">
                                        Retard de {{ floor($daysOverdue) }} jour(s)
                                    </div>
                                @endif
                            @endif
                        </div>
                        
                        <hr class="mb-4">

                        <div class="mb-4">
                            <label class="text-muted text-uppercase small fw-bold mb-2">Mode de paiement préféré</label>
                            <div class="d-flex align-items-center px-3 py-2 border rounded bg-light">
                                <i class="bi {{ 
                                    $facture->mode_paiement === 'virement' ? 'bi-bank' : 
                                    ($facture->mode_paiement === 'cheque' ? 'bi-card-heading' : 
                                    ($facture->mode_paiement === 'carte' ? 'bi-credit-card' : 'bi-cash-coin')) 
                                }} me-3 text-primary fs-4"></i>
                                <span class="fw-semibold">{{ $facture->mode_paiement ? ucfirst($facture->mode_paiement) : 'Non défini' }}</span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="text-muted text-uppercase small fw-bold mb-2">Projet Associé</label>
                            @if($facture->projet)
                                <div class="px-3 py-3 border rounded">
                                    <div class="fw-semibold text-dark"><i class="bi bi-briefcase me-2 text-primary"></i>{{ $facture->projet->nom }}</div>
                                    <a href="{{ route('role-dynamique.projets.show', $facture->projet->id) }}" class="btn btn-sm btn-outline-primary mt-3 w-100">
                                        Voir Projet
                                    </a>
                                </div>
                            @else
                                <div class="text-muted border rounded p-3 bg-light text-center fst-italic">
                                    Aucun projet lié
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body { background-color: #fff !important; }
    .cp-sidebar, .cp-header, .btn, form, header, footer { display: none !important; }
    .cp-dashboard { margin: 0 !important; padding: 0 !important; }
    .cp-content { padding: 0 !important; }
    .print-card { box-shadow: none !important; border: 1px solid #ccc !important; }
    .badge { border: 1px solid #000; color: #000 !important; }
}
</style>
@endsection
