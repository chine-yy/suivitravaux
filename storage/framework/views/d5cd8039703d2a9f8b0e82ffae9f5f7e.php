

<?php $__currentLoopData = $groupedPermissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupName => $modules): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
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
    ?>
    <div class="permission-group" data-group="<?php echo e(\Illuminate\Support\Str::slug($groupName)); ?>">
        
        <div class="d-flex justify-content-between align-items-center p-3 border-bottom group-header bg-light cursor-pointer"
            data-toggle="<?php echo e(\Illuminate\Support\Str::slug($groupName)); ?>">
            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center">
                <i class="bi bi-<?php echo e($groupIcon); ?> text-green me-2 fs-5"></i>
                <?php echo e($groupName); ?>

            </h6>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-secondary-soft text-secondary rounded-pill px-2 py-1"><?php echo e(count($modules)); ?>

                    modules</span>
                <button type="button" class="btn btn-sm btn-outline-green group-select-all px-3"
                    data-group="<?php echo e(\Illuminate\Support\Str::slug($groupName)); ?>" data-full-text="true">
                    <i class="bi bi-check-all me-1"></i>Tout cocher
                </button>
                <i class="bi bi-chevron-down text-muted chevron-icon"></i>
            </div>
        </div>

        
        <div class="group-content">
            <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $moduleSlug => $moduleData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $moduleHasView = collect($moduleData['permissions'])->contains('action', 'view');
                ?>
                <div class="d-flex flex-wrap flex-md-nowrap align-items-center p-3 border-bottom module-row"
                    data-has-view="<?php echo e($moduleHasView ? 'true' : 'false'); ?>">
                    <div class="module-name fw-medium text-secondary d-flex align-items-center mb-2 mb-md-0">
                        <i class="bi bi-<?php echo e($moduleData['icon']); ?> me-2 fs-5 text-muted opacity-75"></i>
                        <?php echo e($moduleData['nom']); ?>

                    </div>
                    <div class="d-flex flex-wrap gap-2 flex-grow-1 border-start ps-md-3 border-opacity-25">
                        <?php $__currentLoopData = $moduleData['permissions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
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
                            ?>
                            <label class="perm-switch <?php echo e($isChecked ? 'active' : ''); ?> <?php echo e($isView ? 'perm-view-switch' : ''); ?>"
                                data-action="<?php echo e($permission->action); ?>">
                                <input class="d-none" type="checkbox" name="permissions[]" value="<?php echo e($permission->id); ?>"
                                    data-action="<?php echo e($permission->action); ?>"
                                    data-group="<?php echo e(\Illuminate\Support\Str::slug($groupName)); ?>" 
                                    data-messaging-parent="<?php echo e($permission->slug === 'chat-messagerie-activer' ? 'true' : 'false'); ?>"
                                    <?php echo e($isChecked ? 'checked' : ''); ?>>
                                <span class="perm-btn<?php echo e($isView ? ' perm-btn-view' : ''); ?>">
                                    <i class="bi bi-<?php echo e($actionIcon); ?> me-1"></i>
                                    <?php echo e($label); ?>

                                    <?php if($isView && $moduleHasView && count($moduleData['permissions']) > 1): ?>
                                        <span class="perm-view-required" title="Obligatoire pour toutes les autres actions">*</span>
                                    <?php endif; ?>
                                </span>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/partials/permission-matrix-body.blade.php ENDPATH**/ ?>