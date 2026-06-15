{{--
Partial: permission-matrix-body
Variables attendues:
$groupedPermissions : collection groupée depuis Permission::getGroupedPermissions()
$checkedIds : array d'IDs de permissions déjà cochées
$groupSlugAttr : (optionnel) nom de l'attribut data-group, défaut "data-group"
--}}

@foreach($groupedPermissions as $groupName => $modules)
    @php
        $groupIcon = match (true) {
            str_contains($groupName, 'Gestion Globale') => 'shield-lock',
            str_contains($groupName, 'Projets') => 'kanban',
            str_contains($groupName, 'Ressources Humaines') => 'people',
            str_contains($groupName, 'Partenaires') => 'person-badge',
            str_contains($groupName, 'Interventions') => 'wrench',
            str_contains($groupName, 'Fournisseurs') => 'truck',
            str_contains($groupName, 'Rendez-vous') => 'calendar-event',
            str_contains($groupName, 'Documents') => 'folder2-open',
            str_contains($groupName, 'Communication') => 'chat-dots',
            default => 'grid',
        };
    @endphp
    <div class="permission-group" data-group="{{ \Illuminate\Support\Str::slug($groupName) }}">
        {{-- Group Header --}}
        <div class="d-flex justify-content-between align-items-center p-3 border-bottom group-header bg-light cursor-pointer"
            data-toggle="{{ \Illuminate\Support\Str::slug($groupName) }}">
            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center">
                <i class="bi bi-{{ $groupIcon }} text-green me-2 fs-5"></i>
                {{ $groupName }}
            </h6>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-secondary-soft text-secondary rounded-pill px-2 py-1">{{ count($modules) }}
                    modules</span>
                <button type="button" class="btn btn-sm btn-outline-green group-select-all px-3"
                    data-group="{{ \Illuminate\Support\Str::slug($groupName) }}" data-full-text="true">
                    <i class="bi bi-check-all me-1"></i>Tout cocher
                </button>
                <i class="bi bi-chevron-down text-muted chevron-icon"></i>
            </div>
        </div>

        {{-- Modules List --}}
        <div class="group-content">
            @foreach($modules as $moduleSlug => $moduleData)
                @php
                    $moduleHasView = collect($moduleData['permissions'])->contains('action', 'view');
                @endphp
                <div class="d-flex flex-wrap flex-md-nowrap align-items-center p-3 border-bottom module-row"
                    data-has-view="{{ $moduleHasView ? 'true' : 'false' }}">
                    <div class="module-name fw-medium text-secondary d-flex align-items-center mb-2 mb-md-0">
                        <i class="bi bi-{{ $moduleData['icon'] }} me-2 fs-5 text-muted opacity-75"></i>
                        {{ $moduleData['nom'] }}
                    </div>
                    <div class="d-flex flex-wrap gap-2 flex-grow-1 border-start ps-md-3 border-opacity-25">
                        @foreach($moduleData['permissions'] as $permission)
                            @php
                                $isChecked = in_array($permission->id, $checkedIds);
                                $isView = $permission->action === 'view';
                                $actionIcon = match ($permission->action) {
                                    'view' => 'eye',
                                    'create' => 'plus',
                                    'edit' => 'pencil',
                                    'delete' => 'trash',
                                    'export' => 'download',
                                    'upload' => 'upload',
                                    'download' => 'cloud-download',
                                    'reordonner' => 'arrows-move',
                                    'archiver' => 'archive',
                                    'restaurer' => 'arrow-counterclockwise',
                                    'valider' => 'check-circle',
                                    'plan' => 'calendar-week',
                                    'payer' => 'credit-card',
                                    'envoyer-partenaire' => 'envelope',
                                    'exporter-pdf' => 'file-pdf',
                                    'allouer-projet' => 'wallet2',
                                    'reset-password' => 'key',
                                    'activer' => 'toggle-on',
                                    'clear' => 'x-circle',
                                    'sauvegarde' => 'cloud-upload',
                                    'manage' => 'sliders',
                                    default => 'circle',
                                };
                                $label = \App\Models\Permission::$actionLabels[$permission->action] ?? ucfirst($permission->action);
                            @endphp
                            <label class="perm-switch {{ $isChecked ? 'active' : '' }} {{ $isView ? 'perm-view-switch' : '' }}"
                                data-action="{{ $permission->action }}">
                                <input class="d-none" type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                    data-action="{{ $permission->action }}"
                                    data-group="{{ \Illuminate\Support\Str::slug($groupName) }}" 
                                    data-messaging-parent="{{ $permission->slug === 'chat-messagerie-activer' ? 'true' : 'false' }}"
                                    {{ $isChecked ? 'checked' : '' }}>
                                <span class="perm-btn{{ $isView ? ' perm-btn-view' : '' }}">
                                    <i class="bi bi-{{ $actionIcon }} me-1"></i>
                                    {{ $label }}
                                    @if($isView && $moduleHasView && count($moduleData['permissions']) > 1)
                                        <span class="perm-view-required" title="Obligatoire pour toutes les autres actions">*</span>
                                    @endif
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endforeach