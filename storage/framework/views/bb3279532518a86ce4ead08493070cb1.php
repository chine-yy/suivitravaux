<?php $__env->startSection('title', config('app.name') . ' - Solution de gestion de chantier BTP'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/accueil.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <!-- HERO SECTION -->
    <section id="accueil" class="hero-section">
        <div class="hero-bg-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
        <div class="container position-relative" style="z-index:2;">
            <div class="row align-items-center min-vh-75">
                <div class="col-lg-7">
                    <p class="hero-badge">
                        <i class="bi bi-lightning-charge-fill me-1"></i> Plateforme tout-en-un pour les professionnels
                    </p>
                    <h1 class="hero-title">
                        Suivez vos <span class="text-gradient">travaux</span> avec puissance et simplicité
                    </h1>
                    <p class="hero-sub">
                        Projets, équipes, incidents, finances et stocks — tout centralisé
                        dans une seule application pensée pour les professionnels du BTP.
                    </p>
                    <div class="d-flex gap-3 mb-4 flex-wrap">
                        <a href="<?php echo e(route('entreprise.register')); ?>" class="btn btn-cnrst-green btn-lg rounded-pill px-4 shadow-green">
                            <i class="bi bi-rocket-takeoff-fill me-2"></i>Inscription gratuite
                        </a>
                        <a href="#contact" class="btn btn-glass btn-lg rounded-pill px-4">
                            <i class="bi bi-envelope-fill me-2"></i>Contactez-nous
                        </a>
                    </div>
                    <div class="hero-trust">
                        <div class="stars">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <span>4.8/5 — 500+ entreprises nous font confiance</span>
                    </div>
                </div>
                <div class="col-lg-5 text-center hero-visual">
                    <div class="hero-icon-wrapper">
                        <i class="bi bi-building"></i>
                    </div>
                </div>
            </div>
        </div>

        <a href="#fonctionnalites" class="scroll-down-arrow" aria-label="Défiler vers le bas">
            <i class="bi bi-chevron-down"></i>
        </a>
    </section>

    <!-- MÉTRIQUES -->
    <section class="metrics-section">
        <div class="container">
            <div class="row text-center">
                <div class="col-6 col-md-3 mb-3">
                    <div class="metric-card">
                        <i class="bi bi-diagram-3-fill"></i>
                        <div class="metric-number"><?php echo e($projetCount); ?>+</div>
                        <p>Projets</p>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <div class="metric-card">
                        <i class="bi bi-layers-fill"></i>
                        <div class="metric-number"><?php echo e($phaseCount); ?>+</div>
                        <p>Phases</p>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <div class="metric-card">
                        <i class="bi bi-check2-square"></i>
                        <div class="metric-number"><?php echo e($tacheCount); ?>+</div>
                        <p>Tâches</p>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <div class="metric-card">
                        <i class="bi bi-people-fill"></i>
                        <div class="metric-number"><?php echo e($userCount); ?>+</div>
                        <p>Utilisateurs</p>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <div class="metric-card">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <div class="metric-number"><?php echo e($incidentCount); ?>+</div>
                        <p>Incidents</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FONCTIONNALITÉS -->
    <section id="fonctionnalites" class="features-section">
        <div class="container">
            <div class="section-header">
                <span class="section-badge"><i class="bi bi-grid-3x3-gap-fill me-1"></i> Fonctionnalités</span>
                <h2>Tout ce dont vous avez besoin</h2>
                <p>Des outils puissants pour gérer vos chantiers de A à Z</p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-kanban"></i></div>
                        <h5>Gestion de projets</h5>
                        <p>Créez et suivez vos projets avec phases, tâches et diagramme de Gantt.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-people-fill"></i></div>
                        <h5>Suivi d'équipe</h5>
                        <p>Planifiez les interventions et suivez le pointage de vos techniciens.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
                        <h5>Gestion des incidents</h5>
                        <p>Signalez et suivez les incidents avec priorité et résolution.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-wallet2"></i></div>
                        <h5>Module financier</h5>
                        <p>Gérez devis, factures et suivez vos budgets en temps réel.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-box-seam"></i></div>
                        <h5>Gestion de stock</h5>
                        <p>Suivez vos matériaux et automatisez les réapprovisionnements.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-file-earmark-bar-graph"></i></div>
                        <h5>Rapports automatiques</h5>
                        <p>Générez des rapports PDF pour vos partenaires et partenaires.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- À PROPOS DU CNRST -->
    <section id="propos" class="about-section">
        <div class="container">
            <div class="section-header">
                <span class="section-badge"><i class="bi bi-info-circle-fill me-1"></i> À propos</span>
                <h2>Qui sommes-nous ?</h2>
                <p>Centre National de la Recherche Scientifique et Technologique</p>
            </div>

            <div class="row g-4 justify-content-center">
                <div class="col-lg-10">
                    <div class="text-center">
                        <i class="bi bi-building" style="font-size:3rem;color:var(--green);"></i>
                        <p class="mt-3" style="font-size:1.1rem;line-height:1.8;">
                            Le <strong>CNRST</strong> (Centre National de la Recherche Scientifique et Technologique) est un Établissement Public à caractère Scientifique, Culturel et Technique (EPSCT) en charge de la recherche scientifique et technologique au Burkina Faso. Il assure la coordination et le contrôle de l'ensemble des activités de ses quatre instituts spécialisés.
                        </p>
                        <div class="useful-links mt-4">
                            <h5 class="mb-3">Liens utiles</h5>
                            <a href="https://council.science/member/burkina-faso-centre-national-de-la-recherche-scientifique-et-technologique/" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-green rounded-pill me-2 mb-2">
                                <i class="bi bi-box-arrow-up-right me-1"></i>International Science Council
                            </a>
                            <a href="https://www.auf.org/membre/centre-national-de-la-recherche-scientifique-et-technologique-3/" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-green rounded-pill me-2 mb-2">
                                <i class="bi bi-box-arrow-up-right me-1"></i>AUF
                            </a>
                            <a href="https://www.nature.com/nature-index/institution-outputs/burkina-faso/centre-national-de-la-recherche-scientifique-et-technologique-cnrst/" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-green rounded-pill me-2 mb-2">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Nature Index
                            </a>
                            <a href="https://www.revuesciences-techniquesburkina.org/" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-green rounded-pill me-2 mb-2">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Revue Science et Technique
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- LOCALISATION -->
    <section id="localisation" class="about-section" style="padding-top:4rem;">
        <div class="container">
            <div class="section-header">
                <span class="section-badge"><i class="bi bi-geo-alt-fill me-1"></i> Localisation</span>
                <h2 style="font-size:2.5rem;">Où nous trouver</h2>
                <p>Avenue du Président Thomas Sankara, 03 B.P. 7047, Ouagadougou, Burkina Faso</p>
            </div>
            <div id="cnrst-map" style="width:100%; height:350px; border-radius:12px;"></div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-box">
                <i class="bi bi-buildings"></i>
                <h3>Prêt à optimiser vos chantiers ?</h3>
                <p>Rejoignez +500 entreprises qui nous font confiance</p>
                <a href="<?php echo e(route('entreprise.register')); ?>" class="btn btn-cnrst-green btn-lg rounded-pill px-5">
                    <i class="bi bi-arrow-right-circle-fill me-2"></i>Commencer maintenant
                </a>
            </div>
        </div>
    </section>



    <!-- CONTACT -->
    <section id="contact" class="contact-section">
        <div class="container">
            <div class="section-header">
                <span class="section-badge"><i class="bi bi-envelope-fill me-1"></i> Contact</span>
                <h2>Contactez-nous</h2>
            </div>

            <div class="row g-4">
                <div class="col-md-7">
                    <div class="contact-form-card">
                        <?php if(session('status')): ?>
                            <div class="alert alert-green"><i class="bi bi-check-circle-fill me-2"></i><?php echo e(session('status')); ?></div>
                        <?php elseif(session('error')): ?>
                            <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo e(session('error')); ?></div>
                        <?php endif; ?>

                        <form action="<?php echo e(route('contact.send')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <h5 class="mb-3 fw-bold">Formulaire de contact</h5>
                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                    <input type="text" name="name" class="form-control" placeholder="Votre nom et prenom au complet" value="<?php echo e(old('name')); ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                    <input type="email" name="email" class="form-control" placeholder="Votre email" value="<?php echo e(old('email')); ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <textarea name="message" class="form-control" rows="4" placeholder="Votre message" required><?php echo e(old('message')); ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-green rounded-pill px-5">
                                <i class="bi bi-send-fill me-2"></i>Envoyer
                            </button>
                        </form>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="contact-info-card">
                        <h5>Contact direct</h5>
                        <div class="contact-item">
                            <div class="contact-icon"><i class="bi bi-telephone-fill"></i></div>
                            <span>+226 25 31 58 69</span>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon"><i class="bi bi-envelope-fill"></i></div>
                            <span>dgcnrst@fasonet.bf</span>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon"><i class="bi bi-geo-alt-fill"></i></div>
                            <span>Avenue du Président Thomas Sankara, 03 B.P. 7047, Ouagadougou, Burkina Faso</span>
                        </div>

                        <h5 class="mt-4">Liens utiles</h5>
                        <div class="useful-links">
                            <a href="https://council.science/member/burkina-faso-centre-national-de-la-recherche-scientifique-et-technologique/" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-green rounded-pill me-2 mb-2">
                                <i class="bi bi-box-arrow-up-right me-1"></i>International Science Council
                            </a>
                            <a href="https://www.auf.org/membre/centre-national-de-la-recherche-scientifique-et-technologique-3/" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-green rounded-pill me-2 mb-2">
                                <i class="bi bi-box-arrow-up-right me-1"></i>AUF
                            </a>
                            <a href="https://www.nature.com/nature-index/institution-outputs/burkina-faso/centre-national-de-la-recherche-scientifique-et-technologique-cnrst/" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-green rounded-pill me-2 mb-2">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Nature Index
                            </a>
                            <a href="https://www.revuesciences-techniquesburkina.org/" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-green rounded-pill me-2 mb-2">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Revue Science et Technique
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const map = L.map('cnrst-map').setView([12.3648, -1.5073], 16);
            L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: '© Esri'
            }).addTo(map);
            L.marker([12.3648, -1.5073])
                .addTo(map)
                .bindPopup('<b>CNRST</b><br>Avenue du Président Thomas Sankara<br>Ouagadougou, Burkina Faso')
                .openPopup();
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/accueil.blade.php ENDPATH**/ ?>