<?php $__env->startSection('title', 'Facturation & Paiements'); ?>

<?php $__env->startSection('breadcrumb'); ?>
<span class="text-muted">Factures</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
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
                <a href="<?php echo e(route('super-admin.factures.create')); ?>" class="btn btn-primary px-4">
                    <i class="bi bi-plus-circle me-2"></i>Nouvelle Facture
                </a>
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

        <!-- Filtre -->
        <div class="cp-chart-card mb-4">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-filter me-2"></i>Filtres de recherche</h6>
            </div>
            <div class="p-4">
                <form action="<?php echo e(route('super-admin.factures.index')); ?>" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">N° Facture</label>
                        <input type="text" name="search" class="form-control form-control-sm"
                            placeholder="Rechercher..." value="<?php echo e(request('search')); ?>">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Statut</label>
                        <select name="statut_paiement" class="form-select form-select-sm">
                            <option value="">Tous les statuts</option>
                            <option value="en_attente" <?php echo e(request('statut_paiement')=='en_attente' ? 'selected' : ''); ?>>
                                En attente</option>
                            <option value="paye" <?php echo e(request('statut_paiement')=='paye' ? 'selected' : ''); ?>>Payé
                            </option>
                            <option value="en_retard" <?php echo e(request('statut_paiement')=='en_retard' ? 'selected' : ''); ?>>En
                                retard</option>
                            <option value="annule" <?php echo e(request('statut_paiement')=='annule' ? 'selected' : ''); ?>>Annulé
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Date d'émission</label>
                        <div class="input-group input-group-sm">
                            <input type="date" name="date_emission_start" class="form-control"
                                value="<?php echo e(request('date_emission_start')); ?>" aria-label="Du">
                            <span class="input-group-text">au</span>
                            <input type="date" name="date_emission_end" class="form-control"
                                value="<?php echo e(request('date_emission_end')); ?>" aria-label="Au">
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="bi bi-search me-1"></i> Filtrer
                        </button>
                        <a href="<?php echo e(route('super-admin.factures.index')); ?>" class="btn btn-sm btn-outline-secondary">
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
                        <?php $__empty_1 = true; $__currentLoopData = $factures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $facture): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><strong><?php echo e($facture->numero_facture); ?></strong></td>
                            <td><?php echo e($facture->projet->nom ?? 'N/A'); ?></td>
                            <td><?php echo e(number_format($facture->montant_ht, 0, ',', ' ')); ?></td>
                            <td><?php echo e(number_format($facture->montant_tva, 0, ',', ' ')); ?></td>
                            <td><strong><?php echo e(number_format($facture->montant_ttc, 0, ',', ' ')); ?></strong></td>
                            <td><?php echo e($facture->date_emission ? date('d/m/Y', strtotime($facture->date_emission)) : 'N/A'); ?></td>
                            <td>
                                <?php
                                $paiementClass = ['en_attente' => 'bg-primary', 'paye' => 'bg-success', 'en_retard' =>
                                'bg-danger', 'annule' => 'bg-secondary'];
                                $paiementText = ['en_attente' => 'En attente', 'paye' => 'Payé', 'en_retard' => 'En
                                retard', 'annule' => 'Annulé'];
                                ?>
                                <span class="badge <?php echo e($paiementClass[$facture->statut_paiement] ?? 'bg-secondary'); ?>"><?php echo e($paiementText[$facture->statut_paiement] ?? $facture->statut_paiement); ?></span>
                            </td>
<td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="<?php echo e(route('super-admin.factures.show', $facture->id)); ?>"
                                        class="btn btn-sm btn-outline-info" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('super-admin.export.pdf.direct', ['type' => 'facture', 'id' => $facture->id])); ?>"
                                        class="btn btn-sm btn-outline-secondary" title="Télécharger PDF">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <a href="<?php echo e(route('super-admin.factures.edit', $facture->id)); ?>"
                                        class="btn btn-sm btn-outline-primary" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php if(!$facture->est_envoye_partenaire): ?>
                                    <form action="<?php echo e(route('super-admin.factures.envoyer-partenaire', $facture->id)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Envoyer au partenaire" onclick="return confirm('Envoyer cette facture au partenaire ?')">
                                            <i class="bi bi-send"></i>
                                        </button>
                                    </form>
                                    <?php else: ?>
                                    <span class="btn btn-sm btn-outline-secondary" title="Déjà envoyé au partenaire">
                                        <i class="bi bi-check-circle text-success"></i>
                                    </span>
                                    <?php endif; ?>
                                    <form action="<?php echo e(route('super-admin.factures.destroy', $facture->id)); ?>"
                                        method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Supprimer cette facture ?')" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-receipt display-4"></i>
                                <p class="mt-3">Aucune facture trouvée</p>
                <button class="btn btn-outline-danger" onclick="exportToPdf('facturesTable', 'Liste des factures', 'factures_export')">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Exporter tout
                </button>
                                <a href="<?php echo e(route('super-admin.factures.create')); ?>">Créer une facture</a>
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

<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/super-admin/factures/index.blade.php ENDPATH**/ ?>