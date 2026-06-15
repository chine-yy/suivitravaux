/**
 * Permission Matrix JavaScript
 * Gestion interactive des matrices de permissions
 * Utilisé par: admin/roles/*, super-admin/roles/*, admin/permissions/*
 */

(function() {
    'use strict';

    /**
     * Initialize permission matrix functionality
     */
    function initPermissionMatrix() {
        // Group collapse/expand
        initGroupCollapse();

        // Group select all
        initGroupSelectAll();

        // Individual checkbox toggle
        initIndividualCheckboxes();

        // Apply view-dependency disabled states on page load
        initViewDependencyStates();

        // Initialize group switches state
        updateGroupSwitches();
    }

    /**
     * Initialize group collapse/expand functionality
     */
    function initGroupCollapse() {
        const headers = document.querySelectorAll('.group-header');
        headers.forEach(function(header) {
            header.addEventListener('click', function(e) {
                // Don't toggle if clicking on select-all button
                if (e.target.closest('.group-select-all')) return;

                const groupSlug = this.dataset.toggle;
                if (!groupSlug) return;

                const group = document.querySelector('.permission-group[data-group="' + groupSlug + '"]');
                if (group) {
                    group.classList.toggle('collapsed');
                }
            });
        });
    }

    /**
     * Initialize "Select All" buttons for each group
     * Respecte la règle view obligatoire : coche view en premier, décoche les autres en premier
     */
    function initGroupSelectAll() {
        const buttons = document.querySelectorAll('.group-select-all');
        buttons.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();

                const groupSlug = this.dataset.group;
                if (!groupSlug) return;

                const checkboxes = document.querySelectorAll('input[type="checkbox"][data-group="' + groupSlug + '"]');
                if (!checkboxes.length) return;

                const allChecked = Array.from(checkboxes).every(function(c) {
                    return c.checked;
                });

                if (!allChecked) {
                    // Cocher: activer view en premier (par module), puis les autres
                    // Grouper par module-row
                    const moduleRows = new Set();
                    checkboxes.forEach(function(cb) {
                        const row = cb.closest('.module-row');
                        if (row) moduleRows.add(row);
                    });

                    moduleRows.forEach(function(row) {
                        const viewCb = row.querySelector('input[type="checkbox"][data-action="view"]');
                        // Si le module a un view, cocher d'abord view
                        if (viewCb && !viewCb.checked) {
                            viewCb.checked = true;
                            const viewSwitch = viewCb.closest('.perm-switch');
                            if (viewSwitch) {
                                viewSwitch.classList.add('active');
                                viewSwitch.classList.remove('disabled');
                            }
                        }
                        // Puis activer tous les autres checkbox du module
                        const rowCheckboxes = row.querySelectorAll('input[type="checkbox"][data-group="' + groupSlug + '"]');
                        rowCheckboxes.forEach(function(cb) {
                            cb.checked = true;
                            cb.disabled = false;
                            const switchEl = cb.closest('.perm-switch');
                            if (switchEl) {
                                switchEl.classList.add('active');
                                switchEl.classList.remove('disabled');
                            }
                        });
                    });
                } else {
                    // Décocher: désactiver les non-view en premier, puis view
                    checkboxes.forEach(function(cb) {
                        if (cb.dataset.action !== 'view') {
                            cb.checked = false;
                            const switchEl = cb.closest('.perm-switch');
                            if (switchEl) {
                                switchEl.classList.remove('active');
                            }
                        }
                    });
                    checkboxes.forEach(function(cb) {
                        if (cb.dataset.action === 'view') {
                            cb.checked = false;
                            const switchEl = cb.closest('.perm-switch');
                            if (switchEl) {
                                switchEl.classList.remove('active');
                            }
                        }
                        // Appliquer l'état disabled après avoir décoche view
                        applyDisabledStateForRow(cb.closest('.module-row'));
                    });
                }

                updateGroupSwitches();
            });
        });
    }

    /**
     * Applique l'état disabled aux non-view d'une module-row selon que view est coché ou non
     */
    function applyDisabledStateForRow(moduleRow) {
        if (!moduleRow) return;

        const viewCheckbox = moduleRow.querySelector('input[type="checkbox"][data-action="view"]');
        // Si le module n'a pas de "view" (ex: Messagerie), pas de règle de dépendance
        if (!viewCheckbox) return;

        const allCheckboxes = moduleRow.querySelectorAll('input[type="checkbox"][name="permissions[]"]');
        const viewIsChecked = viewCheckbox.checked;

        allCheckboxes.forEach(function(cb) {
            if (cb.dataset.action === 'view') return;
            const switchEl = cb.closest('.perm-switch');
            if (!viewIsChecked) {
                cb.disabled = true;
                if (switchEl) {
                    switchEl.classList.add('disabled');
                    switchEl.classList.remove('active');
                }
                cb.checked = false;
            } else {
                cb.disabled = false;
                if (switchEl) {
                    switchEl.classList.remove('disabled');
                }
            }
        });
    }

    /**
     * Applique les états disabled à toutes les module-row au chargement de la page
     */
    function initViewDependencyStates() {
        const moduleRows = document.querySelectorAll('.module-row');
        moduleRows.forEach(function(row) {
            applyDisabledStateForRow(row);
        });
    }

    /**
     * Vérifie et applique la règle view obligatoire par module
     */
    function viewDependencyCheck(changedCheckbox) {
        const moduleRow = changedCheckbox.closest('.module-row');
        if (!moduleRow) return;

        const allCheckboxes = moduleRow.querySelectorAll('input[type="checkbox"][name="permissions[]"]');
        const viewCheckbox = moduleRow.querySelector('input[type="checkbox"][data-action="view"]');

        // Si le module n'a pas de "view" (ex: Messagerie), pas de règle de dépendance
        if (!viewCheckbox) return;

        const isViewCheckbox = changedCheckbox.dataset.action === 'view';
        const isCheckingNonView = !isViewCheckbox && changedCheckbox.checked;
        const isUncheckingView = isViewCheckbox && !changedCheckbox.checked;

        // Si on coche une action non-view → forcer view
        if (isCheckingNonView) {
            viewCheckbox.checked = true;
            const viewSwitch = viewCheckbox.closest('.perm-switch');
            if (viewSwitch) {
                viewSwitch.classList.add('active');
                viewSwitch.classList.remove('disabled');
            }
            // Réactiver les autres non-view maintenant que view est coché
            allCheckboxes.forEach(function(cb) {
                if (cb.dataset.action !== 'view') {
                    cb.disabled = false;
                    const switchEl = cb.closest('.perm-switch');
                    if (switchEl) switchEl.classList.remove('disabled');
                }
            });
        }

        // Si on décoche view → décocher/désactiver toutes non-view
        if (isUncheckingView) {
            allCheckboxes.forEach(function(cb) {
                if (cb.dataset.action !== 'view') {
                    cb.checked = false;
                    cb.disabled = true;
                    const switchEl = cb.closest('.perm-switch');
                    if (switchEl) {
                        switchEl.classList.remove('active');
                        switchEl.classList.add('disabled');
                    }
                }
            });
            return;
        }

        // Si view est coché → réactiver toutes non-view
        if (viewCheckbox.checked) {
            allCheckboxes.forEach(function(cb) {
                if (cb.dataset.action !== 'view') {
                    cb.disabled = false;
                    const switchEl = cb.closest('.perm-switch');
                    if (switchEl) switchEl.classList.remove('disabled');
                }
            });
        }
    }

    /**
     * Initialize individual checkbox change handlers
     */
    function initIndividualCheckboxes() {
        const checkboxes = document.querySelectorAll('input[type="checkbox"][name="permissions[]"]');
        checkboxes.forEach(function(cb) {
            // Ajouter data-action si manquant (des vues)
            if (!cb.dataset.action && cb.closest('.perm-switch')) {
                cb.dataset.action = cb.closest('.perm-switch').dataset.action;
            }

            cb.addEventListener('change', function() {
                const permSwitch = this.closest('.perm-switch');
                const permLabel = this.closest('.perm-label');

                if (permSwitch) {
                    permSwitch.classList.toggle('active', this.checked);
                }
                if (permLabel) {
                    permLabel.classList.toggle('active', this.checked);
                }

                // Appliquer logique dépendance view
                viewDependencyCheck(this);

                // Appliquer logique dépendance messagerie (Activer → Super-admin)
                messagingDependencyCheck(this);

                updateGroupSwitches();
            });
        });
    }

    /**
     * Update all group switch buttons based on checkbox states
     */
    function updateGroupSwitches() {
        const buttons = document.querySelectorAll('.group-select-all');
        buttons.forEach(function(btn) {
            const groupSlug = btn.dataset.group;
            if (!groupSlug) return;

            const checkboxes = document.querySelectorAll('input[type="checkbox"][data-group="' + groupSlug + '"]');
            if (!checkboxes.length) return;

            const allChecked = Array.from(checkboxes).every(function(c) {
                return c.checked;
            });

            // Update button text
            if (btn.dataset.fullText === 'true') {
                btn.innerHTML = allChecked
                    ? '<i class="bi bi-x-circle me-1"></i>Tout décocher'
                    : '<i class="bi bi-check-all me-1"></i>Tout cocher';
            } else {
                btn.innerHTML = allChecked
                    ? '<i class="bi bi-x-circle me-1"></i>Off'
                    : '<i class="bi bi-check-all me-1"></i>Tout';
            }

            // Update button classes
            btn.classList.toggle('btn-orange', allChecked);
            btn.classList.toggle('btn-outline-orange', !allChecked);
        });
    }

    /**
     * Initialize preset permissions (for create with preset)
     */
    function initPresets(presetPermissionIds) {
        if (!presetPermissionIds || !presetPermissionIds.length) return;

        const switches = document.querySelectorAll('.perm-switch');
        switches.forEach(function(label) {
            const checkbox = label.querySelector('input[type="checkbox"]');
            if (checkbox) {
                const val = parseInt(checkbox.value);
                if (presetPermissionIds.includes(val)) {
                    label.classList.add('active');
                    checkbox.checked = true;
                }
            }
        });

        // Appliquer l'état disabled après les presets
        initViewDependencyStates();
        updateGroupSwitches();
    }

    /**
     * Expand all groups
     */
    function expandAllGroups() {
        const groups = document.querySelectorAll('.permission-group');
        groups.forEach(function(group) {
            group.classList.remove('collapsed');
        });
    }

    /**
     * Collapse all groups
     */
    function collapseAllGroups() {
        const groups = document.querySelectorAll('.permission-group');
        groups.forEach(function(group) {
            group.classList.add('collapsed');
        });
    }

    /**
     * Get all selected permission IDs
     */
    function getSelectedPermissions() {
        const checkboxes = document.querySelectorAll('input[type="checkbox"][name="permissions[]"]:checked');
        return Array.from(checkboxes).map(function(cb) {
            return parseInt(cb.value);
        });
    }

    /**
     * Select permissions by action type (respecte la règle view)
     */
    function selectByAction(action) {
        const checkboxes = document.querySelectorAll('input[type="checkbox"][name="permissions[]"]');

        // Si on sélectionne une action non-view, d'abord activer view dans chaque module concerné
        if (action !== 'view') {
            checkboxes.forEach(function(cb) {
                const permSwitch = cb.closest('.perm-switch');
                if (permSwitch && permSwitch.dataset.action === action) {
                    const moduleRow = cb.closest('.module-row');
                    if (moduleRow) {
                        const viewCb = moduleRow.querySelector('input[type="checkbox"][data-action="view"]');
                        if (viewCb && !viewCb.checked) {
                            viewCb.checked = true;
                            const viewSwitch = viewCb.closest('.perm-switch');
                            if (viewSwitch) {
                                viewSwitch.classList.add('active');
                                viewSwitch.classList.remove('disabled');
                            }
                        }
                    }
                    cb.disabled = false;
                    cb.checked = true;
                    permSwitch.classList.add('active');
                    permSwitch.classList.remove('disabled');
                }
            });
        } else {
            checkboxes.forEach(function(cb) {
                const permSwitch = cb.closest('.perm-switch');
                if (permSwitch && permSwitch.dataset.action === 'view') {
                    cb.checked = true;
                    permSwitch.classList.add('active');
                    // Réactiver les non-view du module
                    const moduleRow = cb.closest('.module-row');
                    if (moduleRow) {
                        moduleRow.querySelectorAll('input[type="checkbox"][name="permissions[]"]').forEach(function(other) {
                            if (other.dataset.action !== 'view') {
                                other.disabled = false;
                                const otherSwitch = other.closest('.perm-switch');
                                if (otherSwitch) otherSwitch.classList.remove('disabled');
                            }
                        });
                    }
                }
            });
        }
        updateGroupSwitches();
    }

    // Export to global scope
    window.PermissionMatrix = {
        init: initPermissionMatrix,
        initPresets: initPresets,
        expandAll: expandAllGroups,
        collapseAll: collapseAllGroups,
        getSelected: getSelectedPermissions,
        selectByAction: selectByAction,
        updateGroupSwitches: updateGroupSwitches,
        initViewDependencyStates: initViewDependencyStates
    };

    // Auto-initialize on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPermissionMatrix);
    } else {
        initPermissionMatrix();
    }
})();
