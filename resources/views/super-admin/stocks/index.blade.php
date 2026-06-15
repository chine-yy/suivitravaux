@extends('layouts.super-admin')

@section('title', 'Gestion des Stocks')

@section('breadcrumb')
<span class="text-muted">Stocks & Matériaux</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-box-seam me-2"></i>Stocks & Matériaux</h1>
                <p class="cp-page-subtitle">Gérez votre inventaire</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('super-admin.stocks.export') }}" class="btn btn-outline-danger">
                    <i class="bi bi-file-earmark-pdf me-2"></i> Exporter
                </a>
                <a href="{{ route('super-admin.stocks.create') }}" class="btn btn-primary px-4">
                    <i class="bi bi-plus-circle me-2"></i>Nouveau Stock
                </a>
            </div>
        </div>


        @php
        $totalValeur = $stocks->sum(function($s) { return $s->quantite * $s->prix_unitaire; });
        @endphp

        <div class="cp-stats-grid mb-4">
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-primary"><i class="bi bi-boxes"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ $stocks->count() }}</div>
                    <div class="cp-stat-label">Articles en stock</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-success"><i class="bi bi-cash-stack"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ number_format($totalValeur, 0, ',', ' ') }}</div>
                    <div class="cp-stat-label">Valeur Totale (FCFA)</div>
                </div>
            </div>

        </div>

        <!-- Filtre -->
        <div class="cp-chart-card mb-4">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-filter me-2"></i>Filtres de recherche</h6>
            </div>
            <div class="p-4">
                <form action="{{ route('super-admin.stocks.index') }}" method="GET" class="row g-3">
                    <div class="col-md-7">
                        <label class="form-label small fw-bold">Nom / Référence / Catégorie</label>
                        <input type="text" name="search" class="form-control form-control-sm"
                            placeholder="Rechercher..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Statut</label>
                        <select name="statut" class="form-select form-select-sm">
                            <option value="">Tous</option>
                            <option value="disponible" {{ request('statut')=='disponible' ? 'selected' : '' }}>
                                Disponible</option>
                            <option value="epuise" {{ request('statut')=='epuise' ? 'selected' : '' }}>Épuisé</option>
                            <option value="en_reapprovisionnement" {{ request('statut')=='en_reapprovisionnement'
                                ? 'selected' : '' }}>En réapprovisionnement</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="bi bi-search me-1"></i> Filtrer
                        </button>
                        <a href="{{ route('super-admin.stocks.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-list-ul me-2"></i>Liste des Stocks</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="stocksTable">
                    <thead>
                        <tr style="background: rgba(99,102,241,.08);">
                            <th>Nom</th>
                            <th>Référence</th>
                            <th>Catégorie</th>
                            <th>Quantité</th>
                            <th>Prix Unit.</th>
                            <th>Fournisseur</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stocks as $stock)
                        <tr>
                            <td><strong>{{ $stock->nom }}</strong></td>
                            <td>{{ $stock->reference ?? 'N/A' }}</td>
                            <td>{{ $stock->categorie ?? 'N/A' }}</td>
                            <td>
                                {{ $stock->quantite }}
                            </td>
                            <td>{{ number_format($stock->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                            <td>{{ $stock->fournisseur->nom ?? 'N/A' }}</td>
                            <td>
                                @php
                                $statutClass = ['disponible' => 'bg-success', 'epuise' => 'bg-danger',
                                'en_reapprovisionnement' => 'bg-primary'];
                                $statutText = ['disponible' => 'Disponible', 'epuise' => 'Épuisé',
                                'en_reapprovisionnement' => 'Réappro.'];
                                @endphp
                                <span class="badge {{ $statutClass[$stock->statut] ?? 'bg-secondary' }}">{{
                                    $statutText[$stock->statut] ?? $stock->statut }}</span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="{{ route('super-admin.stocks.show', $stock->id) }}"
                                        class="btn btn-sm btn-outline-info" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('super-admin.export.pdf.direct', ['type' => 'stock_single', 'id' => $stock->id]) }}"
                                        class="btn btn-sm btn-outline-secondary" title="Télécharger">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <a href="{{ route('super-admin.stocks.edit', $stock->id) }}"
                                        class="btn btn-sm btn-outline-primary" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <form action="{{ route('super-admin.stocks.destroy', $stock->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Supprimer ce stock ?')" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-box-seam display-4"></i>
                                <p class="mt-3">Aucun stock trouvé</p>
                <button class="btn btn-outline-danger" onclick="exportToPdf('id="stocksTable"', 'Liste des stocks', 'stocks_export')">                    <i class="bi bi-file-earmark-pdf me-2"></i> Exporter                </button>
                                <a href="{{ route('super-admin.stocks.create') }}">Créer un stock</a>
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
