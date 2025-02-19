document.addEventListener('DOMContentLoaded', function() {
    const elements = {
        form: document.getElementById('otpForm'),
        emailInput: document.querySelector('input[name="email"]'),
        otpSection: document.getElementById('otpSection'),
        sendOTPButton: document.getElementById('sendOTP'),
        loadingSpinner: document.getElementById('loadingSpinner'),
        invalidEmailModal: document.getElementById('invalidEmailModal'),
        otpSentModal: document.getElementById('otpSentModal'),
        otpErrorModal: document.getElementById('otpErrorModal'),
        serverErrorModal: document.getElementById('serverErrorModal'),
        verifyOTPButton: document.getElementById('verifyOTP'),
        otpInput: document.querySelector('input[name="otp"]')
    };

    function validateEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function showModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function hideModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    function showLoading() {
        elements.loadingSpinner.classList.remove('hidden');
        elements.loadingSpinner.classList.add('flex');
    }

    function hideLoading() {
        elements.loadingSpinner.classList.add('hidden');
        elements.loadingSpinner.classList.remove('flex');
    }

    if (elements.form) {
        elements.form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const email = elements.emailInput.value.trim();
            if (!validateEmail(email)) {
                showModal('invalidEmailModal');
                return;
            }

            showLoading();
            elements.sendOTPButton.disabled = true;

            try {
                const response = await fetch('Logic_validate.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `sendOTP=1&email=${encodeURIComponent(email)}`
                });

                const data = await response.json();
                hideLoading();
                elements.sendOTPButton.disabled = false;

                if (data.status === 'success') {
                    showModal('otpSentModal');
                    elements.otpSection.classList.remove('hidden');
                } else if (data.status === 'error') {
                    if (data.message === 'Invalid email') {
                        showModal('invalidEmailModal');
                    } else {
                        showModal('otpErrorModal');
                    }
                } else {
                    showModal('serverErrorModal');
                }
            } catch (error) {
                hideLoading();
                elements.sendOTPButton.disabled = false;
                showModal('serverErrorModal');
                console.error('Error:', error);
            }
        });

        elements.verifyOTPButton.addEventListener('click', async function(e) {
            e.preventDefault();

            const otp = elements.otpInput.value.trim();
            if (!otp) {
                showModal('otpErrorModal');
                return;
            }

            showLoading();
            elements.verifyOTPButton.disabled = true;

            try {
                const response = await fetch('Logic_validate.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `verifyOTP=1&otp=${encodeURIComponent(otp)}`
                });

                const data = await response.text();
                hideLoading();
                elements.verifyOTPButton.disabled = false;

                if (data === 'success') {
                    window.location.href = 'enroll.php';
                } else if (data === 'expired') {
                    showModal('otpErrorModal');
                } else if (data === 'invalid') {
                    showModal('otpErrorModal');
                } else {
                    showModal('serverErrorModal');
                }
            } catch (error) {
                hideLoading();
                elements.verifyOTPButton.disabled = false;
                showModal('serverErrorModal');
                console.error('Error:', error);
            }
        });
    }
});

// Global modal close function
window.closeModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
};