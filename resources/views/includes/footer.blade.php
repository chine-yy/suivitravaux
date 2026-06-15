{{-- FOOTER --}}
<footer class="footer-dark text-white pt-5 pb-3">
    <div class="container">
        <div class="row g-4">
            {{-- Brand Section --}}
            <div class="col-lg-4 col-md-6 mb-4">
                <a class="footer-brand d-flex align-items-center" href="/">
                    <img src="{{ asset('image/cnrst.png') }}" alt="CNRST" style="height:36px; width:auto; margin-right:10px; object-fit:contain;">
                    <span class="footer-brand-text">{{ config('app.name') }}</span>
                </a>
                <p class="footer-description">
                    Solution de gestion de chantier BTP développée par le <strong>Centre National de la Recherche Scientifique et Technologique (CNRST)</strong> — Burkina Faso.
                </p>
                <div class="footer-quick-links">
                    <a href="https://council.science/member/burkina-faso-centre-national-de-la-recherche-scientifique-et-technologique/" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-light rounded-pill me-2 mb-2">International Science Council</a>
                    <a href="https://www.auf.org/membre/centre-national-de-la-recherche-scientifique-et-technologique-3/" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-light rounded-pill me-2 mb-2">AUF</a>
                    <a href="https://www.nature.com/nature-index/institution-outputs/burkina-faso/centre-national-de-la-recherche-scientifique-et-technologique-cnrst/" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-light rounded-pill me-2 mb-2">Nature Index</a>
                    <a href="https://www.revuesciences-techniquesburkina.org/" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-light rounded-pill me-2 mb-2">Revue Science et Technique</a>
                </div>
            </div>

            {{-- Liens --}}
            <div class="col-lg-2 col-md-6 mb-4">
                <h6>Liens</h6>
                <ul class="list-unstyled">
                    <li><a href="/#fonctionnalites" class="text-decoration-none">Fonctionnalités</a></li>
                    <li><a href="/#propos" class="text-decoration-none">À propos</a></li>
                    <li><a href="/#localisation" class="text-decoration-none">Localisation</a></li>
                </ul>
            </div>

            {{-- Instituts --}}
            <div class="col-lg-2 col-md-6 mb-4">
                <h6>Instituts</h6>
                <ul class="list-unstyled">
                    <li><a href="https://irsat.sist-bf.org/" target="_blank" rel="noopener noreferrer" class="text-decoration-none">IRSAT</a></li>
                    <li><a href="https://irss-cnrst.bf/" target="_blank" rel="noopener noreferrer" class="text-decoration-none">IRSS</a></li>
                </ul>
            </div>

            {{-- Contact Direct --}}
            <div class="col-lg-4 col-md-6 mb-4">
                <h6>Contact</h6>
                <div class="contact-info">
                    <div class="contact-info-item">
                        <i class="bi bi-telephone-fill"></i>
                        <span>+226 25 31 58 69</span>
                    </div>
                    <div class="contact-info-item">
                        <i class="bi bi-envelope-fill"></i>
                        <span>dgcnrst@fasonet.bf</span>
                    </div>
                    <a href="https://maps.app.goo.gl/z3wDjshVyvw1w8kW6" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                        <div class="contact-info-item">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span>Avenue du Président Thomas Sankara, 03 B.P. 7047, Ouagadougou, Burkina Faso</span>
                    </div>
                    </a>
                </div>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-12 text-center">
                <p class="mb-0">&copy; 2026 {{ config('app.name') }}. Tous droits réservés.</p>
            </div>
        </div>
    </div>
</footer>

