import { createHiring, sendStatusMessage, startConversation } from './chat_hiring_utils.js';

document.addEventListener('DOMContentLoaded', () => {
    updateRequiredAttributes();

    const billingForm = document.getElementById('billing-form');
    const billingStep = document.getElementById('step-billing');
    const paymentStep = document.getElementById('step-payment');
    const goBackStep = document.getElementById('step-go-back');
    
    const toPaymentBtn = document.getElementById('to-payment');
    toPaymentBtn.addEventListener('click', () => {
        if (billingForm.checkValidity()) {
            billingStep.classList.add('hidden');
            paymentStep.classList.remove('hidden');
            updateRequiredAttributes();
        } else {
            billingForm.reportValidity();
        }
    });

    const paymentForm = document.getElementById('payment-form');
    paymentForm.addEventListener('submit', (e) => {
        e.preventDefault();

        updateRequiredAttributes();

        if (!paymentForm.checkValidity()) {
            paymentForm.reportValidity();
            return;
        }

        const { serviceId, clientId, freelancerId, serviceTitle } = paymentForm.dataset;

        const billingData = new FormData(billingForm);
        const paymentData = new FormData(paymentForm);

        for (let [key, value] of billingData.entries()) {
            paymentData.append(key, value);
        }

        fetch('/actions/action_simulate_payment.php', {
            method: 'POST',
            body: paymentData
        })
        .then(res => res.json())
        .then(async data => {
            const resultDiv = document.getElementById('result');
            if (data.success) {
                resultDiv.innerHTML = '<p class="success">Payment successful! Redirecting...</p>';

                await startConversation(serviceId, clientId, freelancerId, 'false');
                await createHiring(serviceId, clientId, freelancerId);
                await sendStatusMessage('Pending', clientId, freelancerId, serviceId, serviceTitle);

                paymentStep.classList.add('hidden');
                goBackStep.classList.remove('hidden');

                window.location.href = '/pages/home_page.php';
            } else {
                resultDiv.innerHTML = `<p class="error">${data.message}</p>`;
            }
        });
    });

    const confirmButton = document.getElementById('confirm-payment');
    confirmButton.disabled = true;

    function updateConfirmButtonState() {
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
        if (!selectedMethod) {
            confirmButton.disabled = true;
            return;
        }

        const activeForm = document.querySelector(`.method-form[data-method="${selectedMethod.value}"]`);
        if (!activeForm || activeForm.classList.contains('hidden')) {
            confirmButton.disabled = true;
            return;
        }

        const requiredFields = activeForm.querySelectorAll('input');
        let allFilled = true;

        requiredFields.forEach(input => {
            if (input.offsetParent !== null && input.name && input.value.trim() === '') {
                allFilled = false;
            }
        });

        confirmButton.disabled = !allFilled;
    }
    
    const methodForms = document.querySelectorAll('.method-form');
    methodForms.forEach(form => {
        const inputs = form.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('input', updateConfirmButtonState);
        });
    });

    function updateRequiredAttributes() {
        const methodForms = document.querySelectorAll('.method-form');
        methodForms.forEach(form => {
            const inputs = form.querySelectorAll('input');
            inputs.forEach(input => {
                if (form.classList.contains('hidden')) {
                    input.removeAttribute('required');
                } else {
                    input.setAttribute('required', 'required');
                }
            });
        });
    }

    const methodRadios = document.querySelectorAll('input[name="payment_method"]');
    methodRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            methodForms.forEach(form => {
                if (form.dataset.method === radio.value) {
                    form.classList.remove('hidden');
                } else {
                    form.classList.add('hidden');
                }
            });
            updateRequiredAttributes();
            updateConfirmButtonState();
        });
    });
    
    function setupNumericInputFormatter(selector, digitGroups = [4, 4, 4, 4]) {
        const input = document.querySelector(selector);
        if (!input) return;
        
        input.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            
            const totalDigits = digitGroups.reduce((a, b) => a + b, 0);
            if (value.length > totalDigits) value = value.slice(0, totalDigits);
            
            let grouped = '';
            let i = 0;
            for (const groupSize of digitGroups) {
                if (value.length > i) {
                    grouped += value.substr(i, groupSize) + ' ';
                    i += groupSize;
                }
            }
            
            e.target.value = grouped.trim();
        });
        
        input.addEventListener('blur', function (e) {
            const expectedPattern = digitGroups.map(g => `\\d{${g}}`).join(' ');
            const regex = new RegExp(`^${expectedPattern}$`);
            const isValid = regex.test(e.target.value.trim());
            e.target.setCustomValidity(isValid ? '' : `Enter a valid code (${digitGroups.join('-')} digits)`);
        });
    }
    
    /* DEBIT CARD METHOD */
    setupNumericInputFormatter('input[name="cc_number"]');
    
    const expiryInput = document.querySelector('input[name="cc_expiry"]');
    if (expiryInput) {
        expiryInput.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 4) value = value.slice(0, 4);

            if (value.length >= 3) {
                value = `${value.slice(0, 2)}/${value.slice(2)}`;
            }

            e.target.value = value;
        });

        expiryInput.addEventListener('blur', function (e) {
            const isValid = /^((0[1-9])|(1[0-2]))\/\d{2}$/.test(e.target.value);
            e.target.setCustomValidity(isValid ? '' : 'Use MM/YY format, e.g., 12/26');
        });
    }

    /* PAYPAL METHOD */
    const paypalEmailInput = document.querySelector('input[name="paypal_email"]');
    if (paypalEmailInput) {
        paypalEmailInput.addEventListener('input', function () {
            const isValid = checkEmailRequirements(paypalEmailInput.value.trim());
            paypalEmailInput.setCustomValidity(isValid ? '' : 'Enter a valid email address');
            updateConfirmButtonState();
        });

        paypalEmailInput.addEventListener('blur', function () {
            const isValid = checkEmailRequirements(paypalEmailInput.value.trim());
            paypalEmailInput.setCustomValidity(isValid ? '' : 'Enter a valid email address');
        });
    }

    function checkEmailRequirements(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    /* PAYSAFECARD METHOD */
    setupNumericInputFormatter('input[name="paysafe_code"]');
});
