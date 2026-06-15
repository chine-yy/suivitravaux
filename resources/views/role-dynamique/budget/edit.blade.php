@extends('layouts.role-dynamique')

@section('title', 'Modifier le Budget Annuel')

@section('breadcrumb')
    <a href="{{ route('role-dynamique.budget.index') }}" class="text-muted">Budgets</a>
    <span class="mx-2">/</span>
    <span>Modifier le Budget</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-pencil me-2"></i>Modifier le Budget Annuel</h1>
                <p class="cp-page-subtitle">Année {{ $budget->annee }}</p>
            </div>
            <a href="{{ route('role-dynamique.budget.index') }}" class="btn btn-outline-secondary px-4">
                <i class="bi bi-arrow-left me-2"></i>Retour
            </a>
        </div>

        @include('partials.alerts')

        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-info-circle me-2"></i>Informations du Budget</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('role-dynamique.budget.update', $budget->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Année</label>
                            <input type="text" class="form-control" value="{{ $budget->annee }}" disabled readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Budget Total (FCF) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="budget_total" class="form-control @error('budget_total') is-invalid @enderror" value="{{ old('budget_total', $budget->budget_total) }}" required autofocus>
                            @error('budget_total') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Description / Notes</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description', $budget->description) }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="{{ route('role-dynamique.budget.index') }}" class="btn btn-outline-secondary px-4">Annuler</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-circle me-2"></i>Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
