@extends('layouts.super-admin')

@section('title', 'Facturation & Paiements')

@section('breadcrumb')
<span class="text-muted">Factures</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-receipt me-2"></i>Facturation & Paiements</h1>
                <p class="cp-page-subtitle">Gérez vos factures</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-danger" onclick="exportToPdf('facturesTable', 'Liste des factures', 'factures_export')">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Exporter tout
                </button>
                <a href="{{ route('super-admin.factures.create') }}" class="btn btn-primary px-4">
                    <i class="bi bi-plus-circle me-2"></i>Nouvelle Facture
                </a>
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

        <!-- Filtre -->
        <div class="cp-chart-card mb-4">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-filter me-2"></i>Filtres de recherche</h6>
            </div>
            <div class="p-4">
                <form action="{{ route('super-admin.factures.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">N° Facture</label>
                        <input type="text" name="search" class="form-control form-control-sm"
                            placeholder="Rechercher..." value="{{ request('search') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Statut</label>
                        <select name="statut_paiement" class="form-select form-select-sm">
                            <option value="">Tous les statuts</option>
                            <option value="en_attente" {{ request('statut_paiement')=='en_attente' ? 'selected' : '' }}>
                                En attente</option>
                            <option value="paye" {{ request('statut_paiement')=='paye' ? 'selected' : '' }}>Payé
                            </option>
                            <option value="en_retard" {{ request('statut_paiement')=='en_retard' ? 'selected' : '' }}>En
                                retard</option>
                            <option value="annule" {{ request('statut_paiement')=='annule' ? 'selected' : '' }}>Annulé
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Date d'émission</label>
                        <div class="input-group input-group-sm">
                            <input type="date" name="date_emission_start" class="form-control"
                                value="{{ request('date_emission_start') }}" aria-label="Du">
                            <span class="input-group-text">au</span>
                            <input type="date" name="date_emission_end" class="form-control"
                                value="{{ request('date_emission_end') }}" aria-label="Au">
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="bi bi-search me-1"></i> Filtrer
                        </button>
                        <a href="{{ route('super-admin.factures.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
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
                            <th>Montant HT</th>
                            <th>TVA</th>
                            <th>Montant TTC</th>
                            <th>Date</th>
                            <th>Paiement</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($factures as $facture)
                        <tr>
                            <td><strong>{{ $facture->numero_facture }}</strong></td>
                            <td>{{ $facture->projet->nom ?? 'N/A' }}</td>
                            <td>{{ number_format($facture->montant_ht, 0, ',', ' ') }}</td>
                            <td>{{ number_format($facture->montant_tva, 0, ',', ' ') }}</td>
                            <td><strong>{{ number_format($facture->montant_ttc, 0, ',', ' ') }}</strong></td>
                            <td>{{ $facture->date_emission ? date('d/m/Y', strtotime($facture->date_emission)) : 'N/A'
                                }}</td>
                            <td>
                                @php
                                $paiementClass = ['en_attente' => 'bg-primary', 'paye' => 'bg-success', 'en_retard' =>
                                'bg-danger', 'annule' => 'bg-secondary'];
                                $paiementText = ['en_attente' => 'En attente', 'paye' => 'Payé', 'en_retard' => 'En
                                retard', 'annule' => 'Annulé'];
                                @endphp
                                <span class="badge {{ $paiementClass[$facture->statut_paiement] ?? 'bg-secondary' }}">{{
                                    $paiementText[$facture->statut_paiement] ?? $facture->statut_paiement }}</span>
                            </td>
<td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="{{ route('super-admin.factures.show', $facture->id) }}"
                                        class="btn btn-sm btn-outline-info" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('super-admin.export.pdf.direct', ['type' => 'facture', 'id' => $facture->id]) }}"
                                        class="btn btn-sm btn-outline-secondary" title="Télécharger PDF">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <a href="{{ route('super-admin.factures.edit', $facture->id) }}"
                                        class="btn btn-sm btn-outline-primary" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if(!$facture->est_envoye_partenaire)
                                    <form action="{{ route('super-admin.factures.envoyer-partenaire', $facture->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Envoyer au partenaire" onclick="return confirm('Envoyer cette facture au partenaire ?')">
                                            <i class="bi bi-send"></i>
                                        </button>
                                    </form>
                                    @else
                                    <span class="btn btn-sm btn-outline-secondary" title="Déjà envoyé au partenaire">
                                        <i class="bi bi-check-circle text-success"></i>
                                    </span>
                                    @endif
                                    <form action="{{ route('super-admin.factures.destroy', $facture->id) }}"
                                        method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Supprimer cette facture ?')" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-receipt display-4"></i>
                                <p class="mt-3">Aucune facture trouvée</p>
                <button class="btn btn-outline-danger" onclick="exportToPdf('facturesTable', 'Liste des factures', 'factures_export')">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Exporter tout
                </button>
                                <a href="{{ route('super-admin.factures.create') }}">Créer une facture</a>
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
