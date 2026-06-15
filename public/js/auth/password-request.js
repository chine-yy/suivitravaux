(() => {
    const form = document.getElementById('forgotForm');
    form.addEventListener('submit', function(e) {
        const email = document.getElementById('email');
        let valid = true;

        if (!email.value.trim()) {
            email.classList.add('is-invalid');
            valid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
            email.classList.add('is-invalid');
            valid = false;
        } else {
            email.classList.remove('is-invalid');
        }

        if (!valid) {
            e.preventDefault();
            return;
        }

        // Show loading state
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Envoi en cours...';
    });
})();
