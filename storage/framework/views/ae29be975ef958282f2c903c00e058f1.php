<?php
    // Usage:
    // @include('partials.row-export', ['id' => $contrat->id, 'prefix' => 'contrat', 'title' => 'Détail - Contrat'])
    $routePrefix = '';
    $currentRoute = request()->route() ? request()->route()->getName() : '';
    if (str_starts_with($currentRoute, 'super-admin.')) $routePrefix = 'super-admin.';
    elseif (str_starts_with($currentRoute, 'role-dynamique.')) $routePrefix = 'role-dynamique.';
    elseif (str_starts_with($currentRoute, 'partenaire.')) $routePrefix = 'partenaire.';
?>
<a href="<?php echo e(route($routePrefix . 'export.pdf.direct', ['type' => $prefix ?? 'item', 'id' => $id])); ?>" 
   class="btn btn-sm btn-outline-secondary" title="<?php echo e($title ?? 'Télécharger'); ?>">
    <i class="bi bi-download"></i>
</a>
<?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux/resources/views/partials/row-export.blade.php ENDPATH**/ ?>