(() => {
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

    // Password strength indicator
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
        const colors = ['', '#ef4444', '#f59e0b', '#22c55e', '#16a34a'];
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
                pwMatch.innerHTML = '<small style="color:#22c55e"><i class="bi bi-check-circle me-1"></i>Les mots de passe correspondent</small>';
            } else {
                pwMatch.innerHTML = '<small style="color:#ef4444"><i class="bi bi-x-circle me-1"></i>Les mots de passe ne correspondent pas</small>';
            }
        } else {
            pwMatch.innerHTML = '';
        }
    }
})();
