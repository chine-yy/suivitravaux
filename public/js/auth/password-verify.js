(() => {
    const digits = document.querySelectorAll('.otp-digit');
    const hidden = document.getElementById('otpHidden');
    const btn = document.getElementById('verifyBtn');

    function updateOtp() {
        let otp = '';
        digits.forEach(d => otp += d.value);
        hidden.value = otp;
        btn.disabled = otp.length !== 6;
    }

    digits.forEach((input, idx) => {
        input.addEventListener('input', (e) => {
            const val = e.target.value.replace(/\D/g, '');
            e.target.value = val.charAt(0) || '';
            if (val && idx < 5) digits[idx + 1].focus();
            e.target.classList.toggle('filled', !!e.target.value);
            updateOtp();
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !e.target.value && idx > 0) {
                digits[idx - 1].focus();
                digits[idx - 1].value = '';
                digits[idx - 1].classList.remove('filled');
                updateOtp();
            }
        });

        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
            for (let i = 0; i < 6 && i < paste.length; i++) {
                digits[i].value = paste[i];
                digits[i].classList.add('filled');
            }
            if (paste.length >= 6) digits[5].focus();
            else if (paste.length > 0) digits[Math.min(paste.length, 5)].focus();
            updateOtp();
        });
    });
})();
