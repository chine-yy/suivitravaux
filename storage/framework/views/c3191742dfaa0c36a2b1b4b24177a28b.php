<?php
    // Usage:
    // @include('partials.export-buttons', ['tableId' => 'partenairesTable', 'title' => 'Liste des partenaires', 'filename' => 'partenaires_export'])
?>
<?php if(!isset($tableId)): ?>
    <?php $tableId = $tableId ?? '' ; $title = $title ?? 'Liste'; $filename = $filename ?? 'export'; ?>
<?php endif; ?>
<button class="btn btn-outline-danger" onclick="exportToPdf('<?php echo e($tableId); ?>', '<?php echo e($title); ?>', '<?php echo e($filename); ?>')">
    <i class="bi bi-file-earmark-pdf me-2"></i>Exporter tout
</button>
<?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux/resources/views/partials/export-buttons.blade.php ENDPATH**/ ?>