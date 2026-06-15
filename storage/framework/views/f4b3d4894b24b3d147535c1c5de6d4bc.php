<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport - <?php echo e($rapport->titre); ?></title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.5; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #009A44; padding-bottom: 10px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #777; border-top: 1px solid #eee; padding-top: 5px; }
        .content { margin-bottom: 20px; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table td { padding: 8px; border: 1px solid #eee; }
        .info-table .label { font-weight: bold; background-color: #fcfcfc; width: 30%; }
        .section-title { color: #009A44; border-bottom: 1px solid #a5d6a7; margin-top: 20px; padding-bottom: 5px; font-size: 16px; }
        .text-box { background-color: #fafafa; padding: 15px; border-radius: 5px; border: 1px solid #f0f0f0; margin-top: 5px; min-height: 100px; white-space: pre-wrap; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; color: #fff; }
        .bg-success { background-color: #009A44; }
        .bg-danger { background-color: #ef4444; }
        .bg-blue { background-color: #3b82f6; }
        .bg-green { background-color: #009A44; }
        .logo { font-size: 24px; font-weight: bold; color: #009A44; margin-bottom: 5px; }
        .progress-bar { background-color: #009A44; height: 20px; border-radius: 10px; text-align: center; color: white; font-size: 12px; line-height: 20px; }
        .progress-bg { width: 100%; background-color: #eee; height: 20px; border-radius: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">CNRST SUIVI TRAVAUX</div>
        <div style="font-size: 18px; text-transform: uppercase; color: #009A44;">Rapport d'activité</div>
        <div style="font-size: 14px; color: #666;">Généré le <?php echo e(now()->format('d/m/Y à H:i')); ?></div>
    </div>

    <div class="content">
        <h4 class="section-title">Informations Générales</h4>
        <table class="info-table">
            <tr>
                <td class="label">Titre du Rapport</td>
                <td><?php echo e($rapport->titre); ?></td>
            </tr>
            <tr>
                <td class="label">Projet</td>
                <td><?php echo e($rapport->projet->nom ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <td class="label">Date du Rapport</td>
                <td><?php echo e(\Carbon\Carbon::parse($rapport->created_at)->format('d/m/Y')); ?></td>
            </tr>
            <tr>
                <td class="label">Type</td>
                <td><span class="badge bg-green"><?php echo e($rapport->getTypeLabel()); ?></span></td>
            </tr>
            <tr>
                <td class="label">Auteur</td>
                <td>
                    <strong><?php echo e($rapport->auteur->prenom ?? ''); ?> <?php echo e($rapport->auteur->name ?? 'N/A'); ?></strong><br>
                    <span class="badge bg-blue" style="font-size:10px;"><?php echo e($rapport->auteur->role->nom ?? 'Auteur'); ?></span>
                </td>
            </tr>
            <tr>
                <td class="label">Statut</td>
                <td>
                    <?php if($rapport->statut === 'valide'): ?>
                        <span class="badge bg-success">Validé</span>
                    <?php elseif($rapport->statut === 'rejete'): ?>
                        <span class="badge bg-danger">Rejeté</span>
                    <?php else: ?>
                        <span class="badge bg-blue">Soumis</span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>

        <?php if($rapport->contenu): ?>
            <h4 class="section-title">Contenu du Rapport</h4>
            <div class="text-box"><?php echo e($rapport->contenu); ?></div>
        <?php endif; ?>

        <?php if($rapport->observations): ?>
            <h4 class="section-title">Observations</h4>
            <div class="text-box"><?php echo e($rapport->observations); ?></div>
        <?php endif; ?>

        <?php if($rapport->avancement_constate): ?>
            <h4 class="section-title">Avancement</h4>
            <div style="margin-top: 10px;">
                <div class="progress-bg">
                    <div class="progress-bar" style="width: <?php echo e($rapport->avancement_constate); ?>%;">
                        <?php echo e($rapport->avancement_constate); ?>%
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="footer">
        Document confidentiel - Projet <?php echo e($rapport->projet->nom ?? 'N/A'); ?> - &copy; <?php echo e(date('Y')); ?> CNRST
    </div>
</body>
</html>
<?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/partials/pdf-rapport.blade.php ENDPATH**/ ?>