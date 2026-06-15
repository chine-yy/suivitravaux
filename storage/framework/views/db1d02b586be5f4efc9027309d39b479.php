<?php $__env->startSection('title', 'Créer un compte entreprise'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/auth/register.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="register-page">
    <div class="register-wrapper container">
        <div class="register-card">
            <div class="register-header">
                <div class="register-icon">
                    <i class="bi bi-building"></i>
                </div>
                <h1>Créer un compte entreprise</h1>
                <p>Rejoignez <?php echo e(config('app.name')); ?> et gérez vos projets de construction</p>
            </div>

            <div class="register-body">
                <?php echo $__env->make('partials.alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                
                <?php if($errors->any()): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Veuillez corriger les erreurs suivantes :</strong>
                        <ul class="mb-0 mt-2">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('register')); ?>" class="needs-validation" novalidate id="registerForm" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="name" id="full_name" value="<?php echo e(old('name')); ?>">

                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label">Nom de l'entreprise <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-building"></i></span>
                                <input type="text" name="company_name" class="form-control <?php $__errorArgs = ['company_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Ex: BTP Construction SARL" value="<?php echo e(old('company_name')); ?>" required>
                                <?php $__errorArgs = ['company_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><i class="bi bi-exclamation-circle me-1"></i><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Secteur d'activité</label>
                            <select name="industry" class="form-select <?php $__errorArgs = ['industry'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="" selected disabled>Sélectionner...</option>
                                <option value="construction" <?php echo e(old('industry') === 'construction' ? 'selected' : ''); ?>>Construction</option>
                                <option value="genie_civil" <?php echo e(old('industry') === 'genie_civil' ? 'selected' : ''); ?>>Génie civil</option>
                                <option value="architecture" <?php echo e(old('industry') === 'architecture' ? 'selected' : ''); ?>>Architecture</option>
                                <option value="autre" <?php echo e(old('industry') === 'autre' ? 'selected' : ''); ?>>Autre</option>
                            </select>
                            <input type="text" name="custom_industry" id="custom_industry" class="form-control mt-2 <?php $__errorArgs = ['custom_industry'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Précisez votre secteur d'activité" value="<?php echo e(old('custom_industry')); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Numéro IFU</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                <input type="text" name="siret" class="form-control <?php $__errorArgs = ['siret'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Ex: 123 456 789 00012" value="<?php echo e(old('siret')); ?>">
                                <?php $__errorArgs = ['siret'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><i class="bi bi-exclamation-circle me-1"></i><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Téléphone entreprise</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                                <input type="tel" name="company_phone" class="form-control <?php $__errorArgs = ['company_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="+33 1 23 45 67 89" value="<?php echo e(old('company_phone')); ?>">
                                <?php $__errorArgs = ['company_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><i class="bi bi-exclamation-circle me-1"></i><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ville</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-geo-alt-fill"></i></span>
                                <input type="text" name="ville" class="form-control" placeholder="Ex: Paris" value="<?php echo e(old('ville')); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pays</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-globe2"></i></span>
                                <input type="text" name="pays" class="form-control" placeholder="Ex: France" value="<?php echo e(old('pays')); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Site web</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                                <input type="url" name="site_web" class="form-control" placeholder="https://www.monentreprise.com" value="<?php echo e(old('site_web')); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Adresse</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-pin-map-fill"></i></span>
                                <input type="text" name="company_address" class="form-control" placeholder="Rue, ville, code postal" value="<?php echo e(old('company_address')); ?>">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description (facultatif)</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Décrivez votre entreprise..."><?php echo e(old('description')); ?></textarea>
                        </div>
                    </div>

                    <div class="section-title">
                        <span class="section-icon"><i class="bi bi-person-fill"></i></span>
                        Compte administrateur
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label">Prénom <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" id="first_name" name="first_name" class="form-control <?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Votre prénom" value="<?php echo e(old('first_name')); ?>" required>
                                <?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><i class="bi bi-exclamation-circle me-1"></i><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nom <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" id="last_name" name="last_name" class="form-control <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Votre nom" value="<?php echo e(old('last_name')); ?>" required>
                                <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><i class="bi bi-exclamation-circle me-1"></i><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                <input type="email" name="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="admin@entreprise.com" value="<?php echo e(old('email')); ?>" required autocomplete="email">
                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><i class="bi bi-exclamation-circle me-1"></i><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Téléphone</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-phone-fill"></i></span>
                                <input type="tel" name="phone" class="form-control" placeholder="+33 6 12 34 56 78" value="<?php echo e(old('phone')); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mot de passe <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" name="password" id="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Min. 8 caractères" required autocomplete="new-password">
                                <button type="button" class="input-group-text btn-toggle-pw" data-target="password" style="cursor:pointer;">
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback d-block"><i class="bi bi-exclamation-circle me-1"></i><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="password-strength mt-1" id="pwStrength"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Répéter le mot de passe" required autocomplete="new-password">
                                <button type="button" class="input-group-text btn-toggle-pw" data-target="password_confirmation" style="cursor:pointer;">
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                            </div>
                            <div id="pwMatch" class="mt-1"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Photo de profil (facultatif)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-image"></i></span>
                                <input type="file" name="photo" class="form-control" accept="image/*">
                            </div>
                            <small class="text-muted d-block mt-1">Formats acceptés : JPEG, PNG, JPG, GIF. Taille maximale : 10 Mo.</small>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input <?php $__errorArgs = ['accept_terms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" type="checkbox" value="1" id="terms" name="accept_terms" required>
                            <label class="form-check-label" for="terms">
                                J'accepte les <a href="#" class="text-primary">conditions d'utilisation</a> et la <a href="#" class="text-primary">politique de confidentialité</a>
                            </label>
                            <?php $__errorArgs = ['accept_terms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><i class="bi bi-exclamation-circle me-1"></i><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-lg" id="registerBtn">
                        <i class="bi bi-building me-2"></i>
                        Créer mon compte entreprise
                    </button>

                    <div class="auth-footer">
                        Déjà un compte ?
                        <a href="<?php echo e(route('login')); ?>" class="text-primary fw-600">Se connecter</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
(() => {
    // Sync full name
    const first = document.getElementById('first_name');
    const last = document.getElementById('last_name');
    const hidden = document.getElementById('full_name');
    const syncName = () => {
        const parts = [first?.value?.trim(), last?.value?.trim()].filter(Boolean);
        hidden.value = parts.join(' ');
    };
    [first, last].forEach(el => el && el.addEventListener('input', syncName));
    syncName();

    // Toggle password visibility
    document.querySelectorAll('.btn-toggle-pw').forEach(btn => {
        btn.addEventListener('click', () => {
            const target = document.getElementById(btn.dataset.target);
            if (!target) return;
            target.type = target.type === 'password' ? 'text' : 'password';
            const icon = btn.querySelector('i');
            icon.classList.toggle('bi-eye-fill');
            icon.classList.toggle('bi-eye-slash-fill');
        });
    });

    // Password strength
    const pw = document.getElementById('password');
    const pwConfirm = document.getElementById('password_confirmation');
    const pwStrength = document.getElementById('pwStrength');
    const pwMatch = document.getElementById('pwMatch');

    pw.addEventListener('input', () => {
        const val = pw.value;
        let strength = 0;
        if (val.length >= 8) strength++;
        if (/[A-Z]/.test(val)) strength++;
        if (/[0-9]/.test(val)) strength++;
        if (/[^A-Za-z0-9]/.test(val)) strength++;

        const labels = ['', 'Faible', 'Moyen', 'Bon', 'Excellent'];
        const colors = ['', '#ef4444', '#009A44', '#009A44', '#007a35'];
        if (val.length > 0) {
            pwStrength.innerHTML = `<small style="color:${colors[strength]}"><i class="bi bi-shield-fill me-1"></i>${labels[strength]}</small>`;
        } else {
            pwStrength.innerHTML = '';
        }
        checkMatch();
    });

    pwConfirm.addEventListener('input', checkMatch);

    function checkMatch() {
        if (pwConfirm.value && pw.value) {
            if (pw.value === pwConfirm.value) {
                pwMatch.innerHTML = '<small style="color:#009A44"><i class="bi bi-check-circle me-1"></i>Identiques</small>';
            } else {
                pwMatch.innerHTML = '<small style="color:#ef4444"><i class="bi bi-x-circle me-1"></i>Ne correspondent pas</small>';
            }
        } else {
            pwMatch.innerHTML = '';
        }
    }

    // Partenaire-side validation
    const form = document.getElementById('registerForm');
    form.addEventListener('submit', function(e) {
        let valid = true;
        const fields = [
            { el: form.querySelector('[name="company_name"]'), msg: 'Le nom de l\'entreprise est obligatoire.' },
            { el: first, msg: 'Le prénom est obligatoire.' },
            { el: last, msg: 'Le nom est obligatoire.' },
            { el: form.querySelector('[name="email"]'), msg: 'L\'email est obligatoire.' },
            { el: pw, msg: 'Le mot de passe est obligatoire.' },
            { el: pwConfirm, msg: 'La confirmation du mot de passe est obligatoire.' },
        ];

        // Clear old partenaire errors
        form.querySelectorAll('.partenaire-error').forEach(el => el.remove());
        form.querySelectorAll('.is-invalid-partenaire').forEach(el => el.classList.remove('is-invalid-partenaire'));

        fields.forEach(f => {
            if (f.el && !f.el.value.trim()) {
                f.el.classList.add('is-invalid');
                const parent = f.el.closest('.input-group') || f.el.parentElement;
                if (!parent.nextElementSibling?.classList?.contains('partenaire-error')) {
                    const div = document.createElement('div');
                    div.className = 'invalid-feedback d-block partenaire-error';
                    div.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i>' + f.msg;
                    parent.after(div);
                }
                valid = false;
            }
        });

        // Email format
        const emailInput = form.querySelector('[name="email"]');
        if (emailInput.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value)) {
            emailInput.classList.add('is-invalid');
            valid = false;
        }

        // Password length
        if (pw.value && pw.value.length < 8) {
            const parent = pw.closest('.input-group');
            if (!parent.nextElementSibling?.classList?.contains('partenaire-error')) {
                const div = document.createElement('div');
                div.className = 'invalid-feedback d-block partenaire-error';
                div.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i>Le mot de passe doit contenir au moins 8 caractères.';
                parent.after(div);
            }
            valid = false;
        }

        // Password match
        if (pw.value && pwConfirm.value && pw.value !== pwConfirm.value) {
            const parent = pwConfirm.closest('.input-group');
            if (!parent.nextElementSibling?.classList?.contains('partenaire-error')) {
                const div = document.createElement('div');
                div.className = 'invalid-feedback d-block partenaire-error';
                div.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i>Les mots de passe ne correspondent pas.';
                parent.after(div);
            }
            valid = false;
        }

        // Terms
        const terms = document.getElementById('terms');
        if (!terms.checked) {
            terms.classList.add('is-invalid');
            valid = false;
        }

        if (!valid) {
            e.preventDefault();
            // Scroll to first error
            const firstError = form.querySelector('.is-invalid, .partenaire-error');
            if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        // Loading state
        const btn = document.getElementById('registerBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Création en cours...';
    });

    // Clear errors on input
    form.querySelectorAll('input, select, textarea').forEach(input => {
        input.addEventListener('input', () => {
            input.classList.remove('is-invalid');
            const parent = input.closest('.input-group');
            if (parent) {
                const next = parent.nextElementSibling;
                if (next && next.classList.contains('partenaire-error')) next.remove();
            }
        });
    });

    // Toggle custom industry input visibility
    const industrySelect = document.querySelector('select[name="industry"]');
    const customIndustryInput = document.getElementById('custom_industry');

    if (industrySelect && customIndustryInput) {
        function toggleCustomIndustry() {
            customIndustryInput.style.display = industrySelect.value === 'autre' ? 'block' : 'none';
        }

        industrySelect.addEventListener('change', toggleCustomIndustry);
        toggleCustomIndustry();
    }
})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/auth/register.blade.php ENDPATH**/ ?>