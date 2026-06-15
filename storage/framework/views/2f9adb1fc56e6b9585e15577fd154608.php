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
                        <a href="#tarifs" class="btn btn-glass btn-lg rounded-pill px-4">
                            <i class="bi bi-tags-fill me-2"></i>Voir les tarifs
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

        <a href="#metriques" class="scroll-down-arrow" aria-label="Défiler vers le bas">
            <i class="bi bi-chevron-down"></i>
        </a>
    </section>

    <!-- MÉTRIQUES -->
    <section class="metrics-section">
        <div class="container">
            <div class="row text-center">
                <div class="col-6 col-md-3 mb-3">
                    <div class="metric-card">
                        <i class="bi bi-building"></i>
                        <div class="metric-number">500+</div>
                        <p>Entreprises</p>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <div class="metric-card">
                        <i class="bi bi-people-fill"></i>
                        <div class="metric-number">2 500+</div>
                        <p>Utilisateurs actifs</p>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <div class="metric-card">
                        <i class="bi bi-cash-stack"></i>
                        <div class="metric-number">50M+</div>
                        <p>Budgété (FCFA)</p>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-3">
                    <div class="metric-card">
                        <i class="bi bi-emoji-smile-fill"></i>
                        <div class="metric-number">98%</div>
                        <p>Partenaires satisfaits</p>
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

    <!-- TARIFS (100% Gratuit) -->
    <section id="tarifs" class="pricing-section">
        <div class="container">
            <div class="section-header">
                <span class="section-badge"><i class="bi bi-gift-fill me-1"></i> Tarif unique</span>
                <h2>100% Gratuit</h2>
                <p>Accédez à toutes les fonctionnalités sans aucun frais</p>
            </div>

            <div class="row g-4 justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="pricing-card">
                        <div class="pricing-icon"><i class="bi bi-rocket-takeoff"></i></div>
                        <h4><?php echo e(config('app.name')); ?></h4>
                        <div class="pricing-amount">Gratuit<small> pour toujours</small></div>
                        <ul class="pricing-list">
                            <li><i class="bi bi-check-circle-fill"></i> Projets illimités</li>
                            <li><i class="bi bi-check-circle-fill"></i> Utilisateurs illimités</li>
                            <li><i class="bi bi-check-circle-fill"></i> Gestion de projets complète</li>
                            <li><i class="bi bi-check-circle-fill"></i> Suivi d'équipes</li>
                            <li><i class="bi bi-check-circle-fill"></i> Gestion des incidents</li>
                            <li><i class="bi bi-check-circle-fill"></i> Module financier</li>
                            <li><i class="bi bi-check-circle-fill"></i> Gestion de stock</li>
                            <li><i class="bi bi-check-circle-fill"></i> Rapports automatiques</li>
                            <li><i class="bi bi-check-circle-fill"></i> Support par email</li>
                        </ul>
                        <a href="<?php echo e(route('entreprise.register')); ?>" class="btn btn-cnrst-green rounded-pill px-5 py-3">
                            <i class="bi bi-arrow-right-circle-fill me-2"></i>Commencer gratuitement
                        </a>
                    </div>
                </div>
            </div>
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

    <!-- TÉMOIGNAGES -->
    <section class="testimonials-section" id="temoignages">
        <div class="container">
            <div class="section-header">
                <span class="section-badge"><i class="bi bi-chat-quote-fill me-1"></i> Témoignages</span>
                <h2>Ils nous font confiance</h2>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="stars">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <p>"<?php echo e(config('app.name')); ?> nous a fait gagner un temps précieux dans la gestion de nos chantiers. Plus de papiers, tout est centralisé !"</p>
                        <div class="testimonial-author">
                            <i class="bi bi-person-circle"></i>
                            <div>
                                <strong>Jean Dupont</strong>
                                <span>Bâtisseur SARL</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="stars">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <p>"Le suivi des incidents en temps réel nous permet de réagir immédiatement. Un outil indispensable pour nos chantiers."</p>
                        <div class="testimonial-author">
                            <i class="bi bi-person-circle"></i>
                            <div>
                                <strong>Marie Koné</strong>
                                <span>Construct SARL</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="stars">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <p>"La gestion financière intégrée nous a simplifié la vie. Devis, factures, tout est synchronisé avec nos projets."</p>
                        <div class="testimonial-author">
                            <i class="bi bi-person-circle"></i>
                            <div>
                                <strong>Amadou Traoré</strong>
                                <span>Génie Civil SA</span>
                            </div>
                        </div>
                    </div>
                </div>
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
                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                    <input type="text" name="name" class="form-control" placeholder="Votre nom" value="<?php echo e(old('name')); ?>" required>
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
                            <span>+225 01 23 45 67</span>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon"><i class="bi bi-envelope-fill"></i></div>
                            <span>contact@CNRST.com</span>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon"><i class="bi bi-globe2"></i></div>
                            <span>www.CNRST.com</span>
                        </div>

                        <h5 class="mt-4">Suivez-nous</h5>
                        <div class="social-links">
                            <a href="#"><i class="bi bi-facebook"></i></a>
                            <a href="#"><i class="bi bi-linkedin"></i></a>
                            <a href="#"><i class="bi bi-twitter-x"></i></a>
                            <a href="#"><i class="bi bi-youtube"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux/resources/views/accueil.blade.php ENDPATH**/ ?>