document.addEventListener('DOMContentLoaded', function() {
        // Implement search logic as requested (same style as project manager)
        const searchInput = document.getElementById('searchInput');
        const searchDropdown = document.getElementById('searchDropdown');
        const sections = document.querySelectorAll('[data-section-name]');

        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            if (query.length < 2) {
                searchDropdown.style.display = 'none';
                return;
            }

            let resultsHtml = '';
            sections.forEach(section => {
                const name = section.getAttribute('data-section-name');
                if (name.toLowerCase().includes(query)) {
                    resultsHtml += `<div class="search-result-item" onclick="document.getElementById('${section.id}').scrollIntoView({behavior: 'smooth'});">${name}</div>`;
                }
            });

            if (resultsHtml) {
                searchDropdown.innerHTML = resultsHtml;
                searchDropdown.style.display = 'block';
            } else {
                searchDropdown.style.display = 'none';
            }
        });

        // Charts
        const ctx = document.getElementById('cp-repartition-chart');
        if (ctx) {
        const data = JSON.parse(ctx.getAttribute('data-statuts'));
        const statusPalette = {
            enCours: '#2563eb',
            termines: '#16a34a',
            enRetard: '#dc2626',
            enAttente: '#f59e0b'
        };

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                    labels: ['En cours', 'Terminés', 'En retard', 'En attente'],
                datasets: [{
                        data: [data.en_cours, data.termines, data.en_retard, data.en_attente],
                        backgroundColor: [
                            statusPalette.enCours,
                            statusPalette.termines,
                            statusPalette.enRetard,
                            statusPalette.enAttente
                        ],
                        borderColor: '#ffffff',
                        borderWidth: 2
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        }
                    }
                }
            });
        }
    });
