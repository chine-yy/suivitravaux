@extends('layouts.super-admin')

@section('title', 'Créer un Rôle')
@section('breadcrumb')
<a href="{{ route('super-admin.roles.index') }}" class="text-decoration-none">Rôles</a>
<span class="cp-breadcrumb-separator">/</span>
<span class="cp-breadcrumb-item">Nouveau</span>
@endsection

@section('content')
<div class=\"cp-dashboard\">
    <div class=\"cp-content\">
        <div class=\"cp-page-header\">
            <div>
                <h1 class=\"cp-page-title\"><i class=\"bi bi-plus-circle me-2\"></i>Créer un Rôle</h1>
                <p class=\"cp-page-subtitle\">Définissez un nouveau rôle avec ses permissions</p>
            </div>
        </div>


        <div class=\"row g-4\">
            <div class=\"col-lg-8\">
                <div class=\"cp-chart-card\">
                    <div class=\"cp-chart-header\">
                        <h6 class=\"cp-chart-title\"><i class=\"bi bi-shield-plus me-2\"></i>Informations du Rôle</h6>
                    </div>
                    <div class=\"p-4\">
                        <form action=\"{{ route('super-admin.roles.store') }}\" method=\"POST\">
                            @csrf
                            <div class=\"mb-3\">
                                <label for=\"nom\" class=\"form-label fw-semibold\">Nom du Rôle <span class=\"text-danger\">*</span></label>
                                <input type=\"text\" name=\"nom\" id=\"nom\" class=\"form-control form-control-lg @error('nom') is-invalid @enderror\" placeholder=\"Ex: Chef de Projet, Membre, Observateur...\" value=\"{{ old('nom') }}\" autofocus required>
                                <div class=\"form-text\">Ce nom apparaîtra dans le formulaire de connexion.</div>
                                @error('nom')<div class=\"invalid-feedback\">{{ $message }}</div>@enderror
                            </div>

                            <div class=\"mb-3\">
                                <label class=\"form-label fw-semibold\"><i class=\"bi bi-shield-check me-2\"></i>Permissions</label>
                                <p class=\"text-muted small mb-2\">Sélectionnez les accès par module.</p>

                                @php
                                $groupedPermissions = \App\Models\Permission::getGroupedPermissions();
                                @endphp

                                @forelse($groupedPermissions as $groupName => $modules)
                                <div class=\"mb-3 permission-group collapsed\" data-group=\"{{ \Illuminate\Support\Str::slug($groupName) }}\">
                                    <div class=\"d-flex justify-content-between align-items-center p-2 bg-green-light border border-green rounded-top cursor-pointer group-header\" data-toggle=\"{{ \Illuminate\Support\Str::slug($groupName) }}\">
                                        <h6 class=\"fw-bold text-green mb-0 flex-grow-1\"><i class=\"bi bi-folder me-2\"></i>{{ $groupName }}</h6>
                                        <div>
                                            <small class=\"me-2\">{{ count($modules) }} modules</small>
                                            <button type=\"button\" class=\"btn btn-sm btn-outline-green group-select-all me-1\" data-group=\"{{ \Illuminate\Support\Str::slug($groupName) }}\">
                                                <i class=\"bi bi-check-all me-1\"></i>Tout
                                            </button>
                                            <i class=\"bi bi-chevron-down group-chevron\"></i>
                                        </div>
                                    </div>
                                    <div class=\"group-content border border-top-0 border-green rounded-bottom p-2 bg-white\">
                                        <div class=\"row g-2\">
                                            @foreach($modules as $moduleSlug => $moduleData)
                                            <div class=\"col-12\">
                                                <div class=\"d-flex align-items-center mb-1 p-1 bg-light rounded module-row\">
                                                    <div class=\"flex-shrink-0\">
                                                        <i class=\"bi bi-{{ $moduleData['icon'] }} text-muted me-2 fs-5\"></i>
                                                        <span class=\"fw-semibold text-muted fs-6\">{{ $moduleData['nom'] }}</span>
                                                    </div>
                                                    <div class=\"flex-grow-1 ms-2 permission-badges\" style=\"overflow-x: auto; max-height: 60px;\">
                                                        @foreach($moduleData['permissions'] as $permission)
                                                        @php $isChecked = in_array($permission->id, old('permissions', [])); @endphp
                                                        <label class=\"perm-label {{ $isChecked ? 'active' : '' }} me-1 mb-1 d-inline-flex\" style=\"font-size: 0.8rem;\">
                                                            <input class=\"form-check-input me-1 align-middle mt-0 ms-0\" style=\"cursor:pointer; transform: scale(0.85);\" type=\"checkbox\" name=\"permissions[]\" id=\"perm_{{ $permission->id }}\" value=\"{{ $permission->id }}\" data-perm-id=\"{{ $permission->id }}\" data-module=\"{{ $moduleSlug }}\" data-group=\"{{ \Illuminate\Support\Str::slug($groupName) }}\" {{ $isChecked ? 'checked' : '' }}>
                                                            <span class=\"badge bg-{{ $permission->color ?? 'secondary' }} p-1\">
                                                                <i class=\"bi bi-{{ $permission->action == 'view' ? 'eye' : ($permission->action == 'create' ? 'plus' : ($permission->action == 'delete' ? 'trash' : ($permission->action == 'edit' ? 'pencil' : 'circle'))) }} me-1\" style=\"font-size: 0.7rem;\"></i>
                                                                {{ substr($permission->action, 0, 1) }}
                                                            </span>
                                                        </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class=\"alert alert-danger mt-2\">
                                    <i class=\"bi bi-exclamation-triangle me-2\"></i>
                                    Aucune permission disponible. Exécutez le seeder.
                                </div>
                                @endforelse
                            </div>

                            <div class=\"d-flex gap-2 pt-3\">
                                <button type=\"submit\" class=\"btn btn-primary px-4\">
                                    <i class=\"bi bi-check2 me-2\"></i>Créer le Rôle
                                </button>
                                <a href=\"{{ route('super-admin.roles.index') }}\" class=\"btn btn-outline-secondary px-4\">
                                    Annuler
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class=\"col-lg-4\">
                <div class=\"cp-chart-card\">
                    <div class=\"cp-chart-header\">
                        <h6 class=\"cp-chart-title\"><i class=\"bi bi-info-circle me-2\"></i>Légende</h6>
                    </div>
                    <div class=\"p-3\">
                        <div class=\"d-flex flex-wrap gap-2 mb-2\">
                            <span class=\"badge bg-primary fs-6\"><i class=\"bi bi-eye me-1\"></i>V</span>
                            <span class=\"badge bg-success fs-6\"><i class=\"bi bi-plus me-1\"></i>C</span>
                            <span class=\"badge bg-warning fs-6\"><i class=\"bi bi-pencil me-1\"></i>M</span>
                            <span class=\"badge bg-danger fs-6\"><i class=\"bi bi-trash me-1\"></i>S</span>
                        </div>
                        <div class=\"text-muted small\">V=Voire, C=Créer, M=Modifier, S=Supprimer</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.text-green { color: #009A44 !important; }
.bg-green-light { background-color: rgba(0, 154, 68, 0.05); }
.border-green { border-color: #009A44 !important; }
.btn-outline-green { color: #009A44; border-color: #009A44; }
.btn-outline-green:hover { color: #fff; background-color: #009A44; }
.cursor-pointer { cursor: pointer; }
.permission-group { transition: all 0.3s ease; }
.permission-group.collapsed .group-content { display: none; }
.permission-group:not(.collapsed) .group-chevron { transform: rotate(180deg); }
.perm-label { cursor: pointer; transition: all 0.2s; padding: 2px 6px; border: 1px solid transparent; border-radius: 4px; }
.perm-label:hover { border-color: #009A44; }
.perm-label.active { border-color: #009A44 !important; background-color: rgba(0, 154, 68, 0.1); }
.perm-label.active .badge { background-color: #009A44 !important; }
.form-check-input:checked { background-color: #009A44 !important; border-color: #009A44 !important; }
.module-row { border-radius: 6px !important; transition: all 0.2s; }
.module-row:hover { background-color: rgba(0, 154, 68, 0.08) !important; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Group collapse/expand
    document.querySelectorAll('.group-header').forEach(header => {
        header.addEventListener('click', function(e) {
            if (e.target.closest('.group-select-all')) return;
            const groupSlug = this.dataset.toggle;
            const group = document.querySelector(`[data-group=\"${groupSlug}\"]`);
            group.classList.toggle('collapsed');
        });
    });

    // Group select all
    document.querySelectorAll('.group-select-all').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const groupSlug = this.dataset.group;
            const checkboxes = document.querySelectorAll(`[data-group=\"${groupSlug}\"] input[type=\"checkbox\"]` );
            const allChecked = Array.from(checkboxes).every(c => c.checked);
            checkboxes.forEach(cb => {
                cb.checked = !allChecked;
                cb.closest('.perm-label').classList.toggle('active', !allChecked);
            });
            updateGroupSwitches();
        });
    });

    // Individual checkbox
    document.querySelectorAll('input[type=\"checkbox\"][name=\"permissions[]\"]').forEach(cb => {
        cb.addEventListener('change', function() {
            this.closest('.perm-label').classList.toggle('active', this.checked);
            updateGroupSwitches();
        });
    });

    function updateGroupSwitches() {
        document.querySelectorAll('.group-select-all').forEach(btn => {
            const groupSlug = btn.dataset.group;
            const checkboxes = document.querySelectorAll(`[data-group=\"${groupSlug}\"] input[type=\"checkbox\"]`);
            const allChecked = Array.from(checkboxes).every(c => c.checked);
            btn.innerHTML = allChecked ? '<i class=\"bi bi-x-circle me-1\"></i>Off' : '<i class=\"bi bi-check-all me-1\"></i>Tout';
            btn.classList.toggle('btn-green', allChecked);
        });
    }
    updateGroupSwitches();
});
</script>
@endpush

