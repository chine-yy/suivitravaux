<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Détails du Projet - <?php echo e($projet->nom); ?></title>
    <style>
        @page { margin: 0; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1e293b; background-color: #f8fafc; margin: 0; padding: 40px; font-size: 11px; }
        .header { margin-bottom: 25px; }
        .logo { font-size: 24px; font-weight: bold; color: #009A44; margin-bottom: 5px; }
        .page-title { font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 2px; }
        .page-subtitle { color: #64748b; font-size: 13px; margin-bottom: 25px; }

        .stats-grid { width: 100%; margin-bottom: 25px; border-collapse: separate; border-spacing: 12px 0; margin-left: -12px; }
        .stat-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; text-align: center; width: 25%; }
        .stat-value { font-size: 18px; font-weight: bold; color: #0f172a; margin-bottom: 4px; }
        .stat-label { font-size: 9px; color: #64748b; text-transform: uppercase; font-weight: 800; }

        .main-container { width: 100%; border-collapse: separate; border-spacing: 20px 0; margin-left: -20px; }
        .left-col { width: 66%; vertical-align: top; }
        .right-col { width: 34%; vertical-align: top; }

        .card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px; overflow: hidden; }
        .card-header { background: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 12px 20px; font-weight: 700; color: #334155; font-size: 12px; }
        .card-header-icon { color: #009A44; margin-right: 8px; }
        .card-body { padding: 20px; }

        .label { color: #64748b; font-weight: 700; font-size: 9px; text-transform: uppercase; margin-bottom: 5px; display: block; }
        .value { color: #0f172a; font-weight: 600; font-size: 11px; margin-bottom: 15px; display: block; }
        .progress-box { margin-bottom: 15px; }
        .progress-container { background: #e2e8f0; height: 10px; border-radius: 5px; overflow: hidden; margin-top: 8px; }
        .progress-bar { height: 100%; border-radius: 5px; background-color: #3b82f6; }

        .budget-value { font-size: 16px; font-weight: 800; margin-bottom: 2px; }
        .text-success { color: #009A44; }
        .text-primary { color: #3b82f6; }

        .phase-item { border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 15px; margin-bottom: 8px; }
        .phase-name { font-weight: 700; color: #0f172a; }
        .phase-meta { font-size: 9px; color: #64748b; margin-top: 2px; }

        .footer { position: fixed; bottom: 20px; left: 40px; right: 40px; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        table { width: 100%; border-collapse: collapse; }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td><div class="logo">KAFYKA</div></td>
                <td style="text-align: right; color: #94a3b8; font-size: 9px;">Généré le <?php echo e(now()->format('d/m/Y H:i')); ?></td>
            </tr>
        </table>
        <div class="page-title"><?php echo e($projet->nom); ?></div>
        <div class="page-subtitle">Rapport complet de suivi de projet</div>
    </div>

    <table class="stats-grid">
        <tr>
            <td class="stat-card">
                <div class="stat-value"><?php echo e($projet->taches->count()); ?></div>
                <div class="stat-label">Tâches</div>
            </td>
            <td class="stat-card">
                <div class="stat-value"><?php echo e($projet->phases->count()); ?></div>
                <div class="stat-label">Phases</div>
            </td>
            <td class="stat-card">
                <div class="stat-value"><?php echo e(number_format($budgetTotal, 0, ',', ' ')); ?></div>
                <div class="stat-label">Budget (FCFA)</div>
            </td>
            <td class="stat-card" style="border-top: 3px solid #009A44;">
                <div class="stat-value"><?php echo e($projet->avancement ?? 0); ?>%</div>
                <div class="stat-label">Avancement</div>
            </td>
        </tr>
    </table>

    <table class="main-container">
        <tr>
            <td class="left-col">
                <div class="card">
                    <div class="card-header"> Informations générales</div>
                    <div class="card-body">
                        <table style="width: 100%;">
                            <tr>
                                <td style="width: 50%;">
                                    <span class="label">Partenaire</span>
                                    <span class="value">
                                        <?php $__empty_1 = true; $__currentLoopData = $partenaires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <?php echo e($c->name); ?> <?php echo e($c->prenom); ?><br>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            Aucun partenaire associé
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td style="width: 50%;">
                                    <span class="label">Statut</span>
                                    <span class="value"><?php echo e(ucfirst(str_replace('_', ' ', $projet->statut))); ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="label">Date de début</span>
                                    <span class="value"><?php echo e($projet->date_debut ? $projet->date_debut->format('d/m/Y') : 'Non définie'); ?></span>
                                </td>
                                <td>
                                    <span class="label">Date d'échéance</span>
                                    <span class="value"><?php echo e($projet->date_fin_prevue ? $projet->date_fin_prevue->format('d/m/Y') : 'Non définie'); ?></span>
                                </td>
                            </tr>
                        </table>
                        <span class="label">Description</span>
                        <div style="background: #f8fafc; padding: 15px; border-radius: 8px; color: #475569; line-height: 1.6; min-height: 80px;">
                            <?php echo nl2br(e($projet->description ?: 'Aucune description.')); ?>

                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"> Phases du projet</div>
                    <div class="card-body">
                        <?php $__empty_1 = true; $__currentLoopData = $projet->phases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="phase-item">
                                <div class="phase-name"><?php echo e($phase->nom); ?></div>
                                <div class="phase-meta">
                                    Période: <?php echo e($phase->date_debut ? $phase->date_debut->format('d/m/Y') : 'N/A'); ?> - <?php echo e($phase->date_fin_prevue ? $phase->date_fin_prevue->format('d/m/Y') : 'N/A'); ?>

                                    | Avancement: <?php echo e($phase->avancement ?? 0); ?>%
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div style="color: #94a3b8; font-style: italic;">Aucune phase enregistrée.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </td>

            <td class="right-col">
                <div class="card">
                    <div class="card-header"> Budget</div>
                    <div class="card-body">
                        <span class="label">BUDGET ALLOUÉ</span>
                        <div class="budget-value text-success"><?php echo e(number_format($budgetTotal, 0, ',', ' ')); ?> FCFA</div>
                        <br>
                        <span class="label">MONTANT CONSOMMÉ</span>
                        <div class="budget-value text-primary"><?php echo e(number_format($budgetConsomme, 0, ',', ' ')); ?> FCFA</div>
                        <br>
                        <span class="label">BUDGET RESTANT</span>
                        <div class="budget-value" style="color: #0f172a;"><?php echo e(number_format($budgetRestant, 0, ',', ' ')); ?> FCFA</div>
                        
                        <div class="progress-box" style="margin-top: 20px;">
                            <span class="label">TAUX D'UTILISATION : <?php echo e($budgetPourcentage); ?>%</span>
                            <div class="progress-container">
                                <div class="progress-bar" style="width: <?php echo e($budgetPourcentage); ?>%; background-color: #009A44;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"> Avancement global</div>
                    <div class="card-body">
                        <div class="progress-box">
                            <span class="label">Progression : <?php echo e($projet->avancement ?? 0); ?>%</span>
                            <div class="progress-container">
                                <div class="progress-bar" style="width: <?php echo e($projet->avancement ?? 0); ?>%;"></div>
                            </div>
                        </div>
                        <div style="margin-top: 15px; font-size: 10px; color: #64748b;">
                            <strong>Sous-tâches:</strong><br>
                            En cours: <?php echo e($sousTachesStats['en_cours']); ?><br>
                            Terminées: <?php echo e($sousTachesStats['terminee']); ?>

                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"> Historique</div>
                    <div class="card-body">
                        <span class="label">Créé le</span>
                        <span class="value"><?php echo e($projet->created_at->format('d/m/Y à H:i')); ?></span>
                        <span class="label">Dernière modification</span>
                        <span class="value"><?php echo e($projet->updated_at->format('d/m/Y à H:i')); ?></span>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Document d'audit projet généré par KAFYKA Suivi Travaux - &copy; <?php echo e(date('Y')); ?>

    </div>
</body>
</html>
<?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/partials/pdf-project.blade.php ENDPATH**/ ?>