<?php $__env->startSection('title', 'Rapports et Analytique - Super Admin'); ?>

<?php $__env->startSection('breadcrumb'); ?>
<span class="text-muted">Rapports</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<style>
    html { scroll-behavior: smooth; }
    .modal-backdrop.show { z-index: 2050; }
    .modal.show { z-index: 2060; }
</style>
<div class="cp-rapports">
    <div class="cp-content">
        <!-- Page Header -->
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title">Rapports</h1>
                <p class="cp-page-subtitle">Vue d'ensemble et statistiques des rapports et le bouton envoyer permet l'envoi du rapport au partenaire(s) rattacher au projet </p>
            </div>
            <div class="d-flex gap-2">
                <?php if($canPermission('exporter-pdf-rapports')): ?>
                <button class="btn btn-outline-danger" onclick="exportToPdf('rapportsTable', 'Liste des rapports', 'rapports_export')">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Exporter tout
                </button>
                <?php endif; ?>
                <?php if($canPermission('create-rapports')): ?>
                <a href="<?php echo e(route('role-dynamique.rapports.create')); ?>" class="btn btn-green">
                    <i class="bi bi-plus-lg me-2"></i>Nouveau Rapport
                </a>
                <?php endif; ?>
            </div>
        </div>


        <!-- Statistics Cards -->
        <div class="cp-stats-grid mb-4" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <?php
                $isEntrepriseAdmin = auth()->user() && auth()->user()->isAdminEntreprise() && !auth()->user()->isSuperAdmin();
            ?>

            <?php if($isEntrepriseAdmin): ?>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-info"><i class="bi bi-send"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e($totalMesRapports ?? ($mesRapports?->total() ?? 0)); ?></div>
                    <div class="cp-stat-label">Mes rapports</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-primary"><i class="bi bi-inbox"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e($totalRapportsRecus ?? ($recusRapports?->total() ?? 0)); ?></div>
                    <div class="cp-stat-label">Rapports reçus</div>
                </div>
            </div>
            <?php endif; ?>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-info"><i class="bi bi-file-earmark-text"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e($totalRapports ?? ($rapports?->total() ?? 0)); ?></div>
                    <div class="cp-stat-label"><?php echo e($totalRapportsLabel ?? 'Total Rapports'); ?></div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-warning"><i class="bi bi-eye-slash"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e($rapportsParStatut['non_lu'] ?? 0); ?></div>
                    <div class="cp-stat-label">Non lus</div>
                    <a href="<?php echo e(route('role-dynamique.rapports.index', ['filter' => 'non_lu'])); ?>#rapports-table-container" class="btn btn-sm btn-link p-0 text-green fw-bold" style="text-decoration: none;">Voir</a>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-green"><i class="bi bi-hourglass-split"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e($rapportsParStatut['en_attente']); ?></div>
                    <div class="cp-stat-label">En attente</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-success"><i class="bi bi-check-circle"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e($rapportsParStatut['valide']); ?></div>
                    <div class="cp-stat-label">Validés</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-danger"><i class="bi bi-x-circle"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e($rapportsParStatut['rejete']); ?></div>
                    <div class="cp-stat-label">Rejetés</div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <!-- Status Distribution Chart -->
            <div class="col-lg-6 mb-4">
                <div class="cp-chart-card">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title">Répartition par Statut</h6>
                    </div>
                    <div class="cp-chart-body">
                        <canvas id="rapportStatutChart" data-statuts='<?php echo json_encode($rapportsParStatut, 15, 512) ?>'></canvas>
                    </div>
                </div>
            </div>

            <!-- Reports by Project Chart -->
            <div class="col-lg-6 mb-4">
                <div class="cp-chart-card">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title">Rapports par Projet</h6>
                    </div>
                    <div class="cp-chart-body">
                        <canvas id="rapportProjetChart" data-projets='<?php echo json_encode($rapportsParProjet, 15, 512) ?>'></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reports Table -->
        <div class="cp-card" id="rapports-table-container">
            <?php
                $isAdmin = auth()->user() && auth()->user()->isAdminEntreprise() && !auth()->user()->isSuperAdmin();
                $currentTab = request('tab', 'mes');
            ?>

            <?php if($isAdmin): ?>
            <div class="p-3 border-bottom bg-white">
                <ul class="nav nav-tabs">
                    <li class="nav-item"><a class="nav-link <?php echo e($currentTab==='mes' ? 'active' : ''); ?>" href="<?php echo e(route('role-dynamique.rapports.index', ['tab' => 'mes'])); ?>#rapports-table-container" data-tab="mes">Mes rapports (<?php echo e($mesRapports->total() ?? 0); ?>)</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo e($currentTab==='recus' ? 'active' : ''); ?>" href="<?php echo e(route('role-dynamique.rapports.index', ['tab' => 'recus'])); ?>#rapports-table-container" data-tab="recus">Rapports reçus (<?php echo e($recusRapports->total() ?? 0); ?>)</a></li>
                </ul>
            </div>
            <?php endif; ?>
            <div class="cp-card-header">
                <h5 class="cp-card-title mb-3">Liste des Rapports</h5>

                <form action="<?php echo e(route('role-dynamique.rapports.index')); ?>" method="GET" class="row g-2 p-3 bg-light rounded shadow-sm">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Titre</label>
                        <input id="rapportSearch" type="text" name="titre" class="form-control form-control-sm" placeholder="Rechercher..." value="<?php echo e(request('titre')); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Projet</label>
                        <select name="projet_id" class="form-select form-select-sm">
                            <option value="">Tous les projets</option>
                            <?php $__currentLoopData = $projets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($p->id); ?>" <?php echo e(request('projet_id')==$p->id ? 'selected' : ''); ?>><?php echo e($p->nom); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Statut</label>
                        <select id="rapportFilter" name="statut" class="form-select form-select-sm">
                            <option value="">Tous les statuts</option>
                            <option value="en_attente" <?php echo e(request('statut')=='en_attente' ? 'selected' : ''); ?>>En attente</option>
                            <option value="valide" <?php echo e(request('statut')=='valide' ? 'selected' : ''); ?>>Validé</option>
                            <option value="rejete" <?php echo e(request('statut')=='rejete' ? 'selected' : ''); ?>>Rejeté</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Type</label>
                        <select name="type" class="form-select form-select-sm">
                            <option value="">Tous les types</option>
                            <option value="journalier" <?php echo e(request('type')=='journalier' ? 'selected' : ''); ?>>Journalier</option>
                            <option value="hebdomadaire" <?php echo e(request('type')=='hebdomadaire' ? 'selected' : ''); ?>>Hebdomadaire</option>
                            <option value="mensuel" <?php echo e(request('type')=='mensuel' ? 'selected' : ''); ?>>Mensuel</option>
                            <option value="incident" <?php echo e(request('type')=='incident' ? 'selected' : ''); ?>>Incident</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-1">
                        <button type="submit" class="btn btn-sm btn-green w-100"><i class="bi bi-search me-1"></i> Filtrer</button>
                        <a href="<?php echo e(route('role-dynamique.rapports.index')); ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-counterclockwise"></i></a>
                    </div>
                </form>

                <div id="rapports-table-inner" class="mt-3">
                    <?php
                        if(isset($isAdmin) && $isAdmin) {
                            $initialItems = ($currentTab === 'recus') ? ($recusRapports ?? $mesRapports ?? $rapports ?? collect()) : ($mesRapports ?? $recusRapports ?? $rapports ?? collect());
                        } else {
                            $initialItems = $rapports ?? $mesRapports ?? $recusRapports ?? collect();
                        }
                    ?>
                    <?php echo $__env->make('role-dynamique.rapports._table', ['items' => $initialItems, 'currentTab' => $currentTab], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>
            <!-- Pagination handled in partial -->
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Ensure report modals are attached to <body> so they overlay the full viewport.
        document.addEventListener('show.bs.modal', function (event) {
            const modal = event.target;
            if (!modal.classList || !modal.classList.contains('js-rapport-modal')) {
                return;
            }
            if (modal.parentElement !== document.body) {
                document.body.appendChild(modal);
            }
        });

        // Status Chart
        const statutCanvas = document.getElementById('rapportStatutChart');
        if (statutCanvas) {
            const statutData = JSON.parse(statutCanvas.dataset.statuts || '{}');
            if (statutData) {
                new Chart(statutCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: ['En attente', 'Validé', 'Rejeté'],
                        datasets: [{
                            data: [statutData.en_attente || 0, statutData.valide || 0, statutData.rejete || 0],
                            backgroundColor: ['#ffc107', '#198754', '#dc3545'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });
            }
        }

        // Project Chart
        const projetCanvas = document.getElementById('rapportProjetChart');
        if (projetCanvas) {
            const projetData = JSON.parse(projetCanvas.dataset.projets || '[]');
            if (projetData.length) {
                new Chart(projetCanvas, {
                    type: 'bar',
                    data: {
                        labels: projetData.map(p => p.nom),
                        datasets: [{
                            label: 'Nombre de rapports',
                            data: projetData.map(p => p.count),
                            backgroundColor: '#6366f1',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            } else {
                projetCanvas.parentElement.innerHTML = '<div class="text-center text-muted py-5">Aucune donnée disponible</div>';
            }
        }

        // Search and Filter
        const searchInput = document.getElementById('rapportSearch');
        const filterSelect = document.getElementById('rapportFilter');

        function filterTable() {
            const searchTerm = (searchInput?.value || '').toLowerCase();
            const filterValue = filterSelect?.value || '';
            const rows = document.querySelectorAll('#rapportsTable tbody tr');

            rows.forEach(row => {
                const text = (row.textContent || '').toLowerCase();
                const statut = row.dataset.statut;
                const matchesSearch = text.includes(searchTerm);
                const matchesFilter = !filterValue || statut === filterValue;

                row.style.display = (matchesSearch && matchesFilter) ? '' : 'none';
            });
        }

        if (searchInput) searchInput.addEventListener('input', filterTable);
        if (filterSelect) filterSelect.addEventListener('change', filterTable);

    // Auto-open modal if redirected from show route
    <?php if(session('open_modal')): ?>
    var modalToOpen = document.getElementById('<?php echo e(session('open_modal')); ?>');
    if (modalToOpen) {
        var modal = new bootstrap.Modal(modalToOpen);
        modal.show();
    }
    <?php endif; ?>

    // init bindings for table (select all, re-filter)
    function initTableBindings() {
            const selectAllEl = document.getElementById('selectAll');
            if (selectAllEl) {
                // remove previous listener if any
                selectAllEl.replaceWith(selectAllEl.cloneNode(true));
                const newSelect = document.getElementById('selectAll');
                if (newSelect) {
                    newSelect.addEventListener('change', function () {
                        const rows = document.querySelectorAll('#rapportsTable tbody tr');
                        rows.forEach(row => {
                            const checkbox = row.querySelector('input[type="checkbox"]');
                            if (checkbox) checkbox.checked = this.checked;
                        });
                    });
                }
            }

            // re-apply current filters to new rows
            if (typeof filterTable === 'function') filterTable();
        }

        // Initialize table bindings and scroll to the selected tab section on page load
        initTableBindings();
        const currentTab = <?php echo json_encode($currentTab ?? 'mes', 15, 512) ?>;
        const target = document.getElementById('rapports-table-container');
        if (target) {
            // Only scroll when a tab param is present or not default
            if (currentTab && currentTab !== '') {
                try {
                    target.scrollIntoView({ behavior: 'smooth' });
                } catch (e) {
                    // fallback: set location hash
                    window.location.hash = 'rapports-table-container';
                }
            }
        }
    });

    <!-- SheetJS for Excel export -->
<script>
// Export Excel function
    const table = document.getElementById('rapportsTable');

    // Cloner la table pour la nettoyer avant l'export
    const clone = table.cloneNode(true);

    // Supprimer la colonne 1 (checkbox) et la dernière (actions) dans l'en-tête
    const theadTr = clone.querySelector('thead tr');
    if (theadTr) {
        theadTr.removeChild(theadTr.firstElementChild); // checkbox
    theadTr.removeChild(theadTr.lastElementChild); // actions
    }

    // Supprimer dans le corps
    const tbodyTrs = clone.querySelectorAll('tbody tr');
    tbodyTrs.forEach(tr => {
        // Ignorer la ligne "Aucun rapport trouvé" (colspan = 7)
        const cell = tr.querySelector('td');
        if (cell && cell.colSpan > 1) return;

    tr.removeChild(tr.firstElementChild); // checkbox
    tr.removeChild(tr.lastElementChild); // actions

    // Nettoyer le formatage du Titre (retirer le texte de description pour un rendu propre dans Excel, ou extraire uniquement le 'strong')
    const titleTd = tr.children[1]; // Après retrait de la checkbox, Titre est à l'index 1
    if (titleTd) {
            const strong = titleTd.querySelector('strong');
    if (strong) {
        // Remplacer le contenu HTML entier par juste le titre fort
        titleTd.textContent = strong.textContent.trim();
            }
        }

    // Nettoyer le formatage de l'Auteur
    const authorTd = tr.children[3]; // Auteur est à l'index 3
    if (authorTd) {
            // Le texte de l'auteur est directement dans la div d-flex après l'avatar
            const dFlex = authorTd.querySelector('.d-flex');
    if (dFlex) {
        // Extrait le nœud de texte (le nom)
        let text = "";
                Array.from(dFlex.childNodes).forEach(node => {
                    if (node.nodeType === Node.TEXT_NODE && node.textContent.trim() !== '') {
        text += node.textContent.trim();
                    }
                });
    authorTd.textContent = text || dFlex.textContent.trim();
            }
        }

    // Nettoyer le Statut
    const statusTd = tr.children[4]; // Statut est à l'index 4
    if (statusTd) {
        statusTd.textContent = statusTd.textContent.trim();
        }
    });

    const wb = XLSX.utils.table_to_book(clone, {sheet: "Rapports"});

    const ws = wb.Sheets["Rapports"];
    // Définir la largeur des colonnes
    ws['!cols'] = [
    {wch: 12 }, // Date
    {wch: 45 }, // Titre
    {wch: 20 }, // Projet
    {wch: 25 }, // Auteur
    {wch: 15 }, // Statut
    ];

}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.role-dynamique', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/role-dynamique/rapports/index.blade.php ENDPATH**/ ?>