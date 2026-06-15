<?php $__env->startSection('title', 'Mes Factures'); ?>

<?php $__env->startSection('breadcrumb'); ?>
<span class="text-muted">Factures</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-receipt me-2"></i>Mes Factures</h1>
                <p class="cp-page-subtitle">Consultation des factures relatives à votre projet</p>
            </div>
        </div>

        <?php
        $totalFacture = $factures->sum('montant_ttc');
        $totalPaye = $factures->where('statut_paiement', 'paye')->sum('montant_ttc');
        $totalAttente = $factures->where('statut_paiement', 'en_attente')->sum('montant_ttc');
        ?>

        <div class="cp-stats-grid mb-4">
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-primary"><i class="bi bi-cash-stack"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e(number_format($totalFacture, 0, ',', ' ')); ?></div>
                    <div class="cp-stat-label">Total Facturé (FCFA)</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-success"><i class="bi bi-check-circle"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e(number_format($totalPaye, 0, ',', ' ')); ?></div>
                    <div class="cp-stat-label">Payé (FCFA)</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-green"><i class="bi bi-hourglass-split"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e(number_format($totalAttente, 0, ',', ' ')); ?></div>
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
                        <?php $__empty_1 = true; $__currentLoopData = $factures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $facture): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><strong><?php echo e($facture->numero_facture); ?></strong></td>
                            <td><?php echo e($facture->projet->nom ?? 'N/A'); ?></td>
                            <td><strong><?php echo e(number_format($facture->montant_ttc, 0, ',', ' ')); ?> FCFA</strong></td>
                            <td><?php echo e($facture->date_emission ? date('d/m/Y', strtotime($facture->date_emission)) : 'N/A'); ?></td>
                            <td><?php echo e($facture->date_echeance ? date('d/m/Y', strtotime($facture->date_echeance)) : 'N/A'); ?></td>
                            <td>
                                <?php
                                $paiementClass = ['en_attente' => 'bg-primary', 'paye' => 'bg-success', 'en_retard' => 'bg-danger', 'annule' => 'bg-secondary'];
                                $paiementText = ['en_attente' => 'En attente', 'paye' => 'Payé', 'en_retard' => 'En retard', 'annule' => 'Annulé'];
                                ?>
                                <span class="badge <?php echo e($paiementClass[$facture->statut_paiement] ?? 'bg-secondary'); ?>">
                                    <?php echo e($paiementText[$facture->statut_paiement] ?? $facture->statut_paiement); ?>

                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="<?php echo e(route('super-admin.export.pdf.direct', ['type' => 'facture', 'id' => $facture->id])); ?>"
                                        class="btn btn-sm btn-outline-primary" title="Télécharger PDF">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-receipt display-4"></i>
                                <p class="mt-3">Aucune facture trouvée</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.partenaire', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux/resources/views/partenaire/facture/factures.blade.php ENDPATH**/ ?>