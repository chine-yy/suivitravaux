/**
 * Tableau de bord réutilisable (ex-chef de projet) - JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initCharts();
    initCalendar();
    initTooltips();
    initAlertes();
    initQuickActions();
    initSectionSearch();
});

/**
 * Initialize Section Search - Search and scroll to section
 */
function initSectionSearch() {
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');
    const searchDropdown = document.getElementById('searchDropdown');
    const sections = document.querySelectorAll('[data-section-name]');
    
    if (!searchInput || !searchDropdown) return;
    
    function updateSearch() {
        const query = searchInput.value.toLowerCase().trim();
        searchDropdown.innerHTML = '';
        
        if (query.length < 1) {
            searchDropdown.style.display = 'none';
            return;
        }

        let hasResults = false;
        sections.forEach(section => {
            const name = section.getAttribute('data-section-name').toLowerCase();
            if (name.includes(query)) {
                hasResults = true;
                const item = document.createElement('div');
                item.className = 'search-result-item';
                
                // Icon selection based on section name
                let icon = 'bi-hash';
                if (name.includes('stat')) icon = 'bi-bar-chart-fill';
                else if (name.includes('graph')) icon = 'bi-pie-chart-fill';
                else if (name.includes('alert')) icon = 'bi-bell-fill';
                else if (name.includes('rapide')) icon = 'bi-lightning-fill';
                else if (name.includes('liste')) icon = 'bi-list-check';
                else if (name.includes('calen')) icon = 'bi-calendar-date';

                item.innerHTML = `<i class="bi ${icon} me-2 text-primary"></i>${section.getAttribute('data-section-name').charAt(0).toUpperCase() + section.getAttribute('data-section-name').slice(1)}`;
                
                item.addEventListener('click', function() {
                    const headerOffset = 90;
                    const elementPosition = section.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: "smooth"
                    });

                    searchInput.value = '';
                    searchDropdown.style.display = 'none';
                    
                    // Highlight effect
                    section.classList.add('section-highlight');
                    setTimeout(() => {
                        section.classList.remove('section-highlight');
                    }, 2000);
                });
                searchDropdown.appendChild(item);
            }
        });

        searchDropdown.style.display = hasResults ? 'block' : 'none';
    }

    searchInput.addEventListener('input', updateSearch);
    
    searchInput.addEventListener('focus', () => {
        if (searchInput.value.trim().length > 0) {
            updateSearch();
        }
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
            searchDropdown.style.display = 'none';
        }
    });

    // Handle Enter keypress
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const firstResult = searchDropdown.querySelector('.search-result-item');
            if (firstResult) {
                firstResult.click();
            }
        }
    });

    if (searchBtn) {
        searchBtn.addEventListener('click', () => {
            const firstResult = searchDropdown.querySelector('.search-result-item');
            if (firstResult) {
                firstResult.click();
            } else {
                updateSearch();
            }
        });
    }
}

/**
 * Initialize Charts using Chart.js
 */
function initCharts() {
    // Only initialize if Chart.js is loaded and canvas elements exist

    // Graphique 1 - Avancement des projets (Barres horizontales)
    const avancementCtx = document.getElementById('cp-avancement-chart');
    if (avancementCtx && typeof Chart !== 'undefined') {
        const avancementData = JSON.parse(avancementCtx.dataset.projects || '[]');
        new Chart(avancementCtx, {
            type: 'bar',
            data: {
                labels: avancementData.map(p => p.nom),
                datasets: [{
                    label: 'Avancement (%)',
                    data: avancementData.map(p => p.avancement),
                    backgroundColor: avancementData.map(p => {
                        if (p.en_retard) return '#ef4444';
                        if (p.avancement >= 75) return '#10b981';
                        if (p.avancement >= 40) return '#f59e0b';
                        return '#ef4444';
                    }),
                    borderRadius: 6,
                    barThickness: 20
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 100,
                        grid: { display: false }
                    },
                    y: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // Graphique 2 - Répartition des projets par statut (Donut)
    const repartitionCtx = document.getElementById('cp-repartition-chart');
    if (repartitionCtx && typeof Chart !== 'undefined') {
        const repartitionData = JSON.parse(repartitionCtx.dataset.statuts || '{}');
        new Chart(repartitionCtx, {
            type: 'doughnut',
            data: {
                labels: ['En cours', 'Terminés', 'En retard', 'En attente', 'En pause'],
                datasets: [{
                    data: [
                        repartitionData.en_cours || 0,
                        repartitionData.termines || 0,
                        repartitionData.en_retard || 0,
                        repartitionData.en_attente || 0,
                        repartitionData.en_pause || 0
                    ],
                    backgroundColor: [
                        '#f7941d', // KAFYKA Orange
                        '#10b981',
                        '#ef4444',
                        '#64748b',
                        '#f59e0b'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    }
                }
            }
        });
    }

    // Graphique 3 - Évolution de l'avancement (Courbe)
    const evolutionCtx = document.getElementById('cp-evolution-chart');
    if (evolutionCtx && typeof Chart !== 'undefined') {
        const evolutionData = JSON.parse(evolutionCtx.dataset.evolution || '[]');
        new Chart(evolutionCtx, {
            type: 'line',
            data: {
                labels: evolutionData.map(e => e.date),
                datasets: [{
                    label: 'Avancement global (%)',
                    data: evolutionData.map(e => e.avancement),
                    borderColor: '#f7941d',
                    backgroundColor: 'rgba(247, 148, 29, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#f7941d',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: { color: '#f3f4f6' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // Graphique 4 - Tâches terminées vs en retard (Barres groupées)
    const tachesCtx = document.getElementById('cp-taches-chart');
    if (tachesCtx && typeof Chart !== 'undefined') {
        const tachesData = JSON.parse(tachesCtx.dataset.taches || '[]');
        new Chart(tachesCtx, {
            type: 'bar',
            data: {
                labels: tachesData.map(t => t.nom),
                datasets: [
                    {
                        label: 'Terminées',
                        data: tachesData.map(t => t.terminees),
                        backgroundColor: '#10b981',
                        borderRadius: 4
                    },
                    {
                        label: 'En retard',
                        data: tachesData.map(t => t.en_retard),
                        backgroundColor: '#ef4444',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'rect'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f3f4f6' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // Graphique 5 - Charge de travail par technician (Barres)
    const chargeCtx = document.getElementById('cp-charge-chart');
    if (chargeCtx && typeof Chart !== 'undefined') {
        const chargeData = JSON.parse(chargeCtx.dataset.charge || '[]');
        new Chart(chargeCtx, {
            type: 'bar',
            data: {
                labels: chargeData.map(c => c.nom),
                datasets: [{
                    label: 'Tâches assignées',
                    data: chargeData.map(c => c.count),
                    backgroundColor: '#06b6d4',
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
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f3f4f6' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // Graphique 6 - Budget consommé vs prévu (Jauge/Donut)
    const budgetCtx = document.getElementById('cp-budget-chart');
    if (budgetCtx && typeof Chart !== 'undefined') {
        const budgetData = JSON.parse(budgetCtx.dataset.budget || '{}');
        const percentage = budgetData.pourcentage || 0;
        const color = percentage > 90 ? '#ef4444' : percentage > 75 ? '#f59e0b' : '#10b981';

        new Chart(budgetCtx, {
            type: 'doughnut',
            data: {
                labels: ['Consommé', 'Restant'],
                datasets: [{
                    data: [percentage, 100 - percentage],
                    backgroundColor: [color, '#e5e7eb'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false }
                }
            }
        });

        // Update percentage text
        const budgetPercentEl = document.getElementById('cp-budget-percent');
        if (budgetPercentEl) {
            budgetPercentEl.textContent = percentage + '%';
            budgetPercentEl.style.color = color;
        }
    }
}

/**
 * Initialize Calendar
 */
function initCalendar() {
    const calendarGrid = document.getElementById('cp-calendar-grid');
    if (!calendarGrid) return;

    const today = new Date();
    const currentMonth = today.getMonth();
    const currentYear = today.getFullYear();

    const firstDay = new Date(currentYear, currentMonth, 1);
    const lastDay = new Date(currentYear, currentMonth + 1, 0);
    const startingDay = firstDay.getDay() || 7; // Monday = 1
    const totalDays = lastDay.getDate();

    const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                        'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
    const dayNames = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];

    // Update header
    const calendarTitle = document.getElementById('cp-calendar-title');
    if (calendarTitle) {
        calendarTitle.textContent = `${monthNames[currentMonth]} ${currentYear}`;
    }

    // Clear and rebuild grid
    calendarGrid.innerHTML = '';

    // Add day headers
    dayNames.forEach(day => {
        const header = document.createElement('div');
        header.className = 'cp-calendar-day-header';
        header.textContent = day;
        calendarGrid.appendChild(header);
    });

    // Get events from data attribute
    const events = JSON.parse(calendarGrid.dataset.events || '[]');
    const eventsByDate = {};
    events.forEach(event => {
        if (!eventsByDate[event.date]) eventsByDate[event.date] = [];
        eventsByDate[event.date].push(event);
    });

    // Add empty cells for days before first of month
    for (let i = 1; i < startingDay; i++) {
        const emptyCell = document.createElement('div');
        emptyCell.className = 'cp-calendar-day cp-day-other-month';
        calendarGrid.appendChild(emptyCell);
    }

    // Add days of month
    for (let day = 1; day <= totalDays; day++) {
        const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const dayEvents = eventsByDate[dateStr] || [];

        const cell = document.createElement('div');
        cell.className = 'cp-calendar-day';
        if (day === today.getDate()) {
            cell.classList.add('cp-day-today');
        }

        cell.innerHTML = `
            <span>${day}</span>
            ${dayEvents.length > 0 ? `
                <div class="cp-day-events">
                    ${dayEvents.slice(0, 3).map(e => `
                        <span class="cp-day-event-dot cp-event-${e.type}"></span>
                    `).join('')}
                </div>
            ` : ''}
        `;

        // Add tooltip on hover
        if (dayEvents.length > 0) {
            cell.title = dayEvents.map(e => `${e.titre} - ${e.projet}`).join('\n');
        }

        calendarGrid.appendChild(cell);
    }
}

/**
 * Initialize Tooltips
 */
function initTooltips() {
    // Simple tooltip implementation
    const tooltipElements = document.querySelectorAll('[data-cp-tooltip]');

    tooltipElements.forEach(el => {
        const tooltip = document.createElement('div');
        tooltip.className = 'cp-tooltip';
        tooltip.textContent = el.dataset.cpTooltip;
        tooltip.style.cssText = `
            position: absolute;
            background: #1f2937;
            color: white;
            padding: 0.5rem 0.75rem;
            border-radius: 4px;
            font-size: 0.75rem;
            white-space: nowrap;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.2s;
            pointer-events: none;
        `;

        el.style.position = 'relative';
        el.appendChild(tooltip);

        el.addEventListener('mouseenter', () => {
            tooltip.style.opacity = '1';
        });

        el.addEventListener('mouseleave', () => {
            tooltip.style.opacity = '0';
        });
    });
}

/**
 * Initialize Alert interactions
 */
function initAlertes() {
    const alertes = document.querySelectorAll('.cp-alerte-item');

    alertes.forEach(alerte => {
        // Add click handler for navigation
        const lien = alerte.dataset.lien;
        if (lien) {
            alerte.addEventListener('click', (e) => {
                if (e.target.tagName !== 'BUTTON') {
                    window.location.href = lien;
                }
            });
        }

        // Add animation
        alerte.style.opacity = '0';
        alerte.style.transform = 'translateX(-10px)';

        setTimeout(() => {
            alerte.style.transition = 'opacity 0.3s, transform 0.3s';
            alerte.style.opacity = '1';
            alerte.style.transform = 'translateX(0)';
        }, Math.random() * 500);
    });
}

/**
 * Initialize Quick Actions
 */
function initQuickActions() {
    // Handle confirmation for delete actions
    document.querySelectorAll('.cp-action-confirm').forEach(btn => {
        btn.addEventListener('click', (e) => {
            if (!confirm('Êtes-vous sûr de vouloir effectuer cette action ?')) {
                e.preventDefault();
                return false;
            }
        });
    });
}

/**
 * Refresh dashboard data (for AJAX updates)
 */
function refreshDashboard() {
    // Show loading state
    document.querySelectorAll('.cp-stat-value, .cp-chart-body').forEach(el => {
        el.style.opacity = '0.5';
    });

    // Fetch new data
    fetch(window.location.href)
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Update stats
            document.querySelectorAll('.cp-stat-value').forEach((el, index) => {
                const newValue = doc.querySelectorAll('.cp-stat-value')[index];
                if (newValue) el.textContent = newValue.textContent;
            });

            // Reset opacity
            document.querySelectorAll('.cp-stat-value, .cp-chart-body').forEach(el => {
                el.style.opacity = '1';
            });
        })
        .catch(err => {
            console.error('Error refreshing dashboard:', err);
            document.querySelectorAll('.cp-stat-value, .cp-chart-body').forEach(el => {
                el.style.opacity = '1';
            });
        });
}

/**
 * Export dashboard to PDF
 */
function exportToPDF() {
    // Simple window.print() for PDF generation
    // In production, use libraries like jsPDF or html2pdf
    window.print();
}

/**
 * Format number with thousand separator
 */
function formatNumber(num) {
    return new Intl.NumberFormat('fr-FR').format(num);
}

/**
 * Format currency
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
    }).format(amount);
}

// Make functions globally available
window.cpExportToPDF = exportToPDF;
window.cpRefreshDashboard = refreshDashboard;
window.cpFormatNumber = formatNumber;
window.cpFormatCurrency = formatCurrency;
