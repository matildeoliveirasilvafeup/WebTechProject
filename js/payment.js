import { createHiring, sendStatusMessage, startConversation } from './chat_hiring_utils.js';

document.addEventListener('DOMContentLoaded', () => {
    const billingForm = document.getElementById('billing-form');
    const goBackStep = document.getElementById('step-go-back');
    const paymentStep = document.getElementById('step-payment');
    const billingStep = document.getElementById('step-billing');
    const methodRadios = document.querySelectorAll('input[name="payment_method"]');
    const methodForms = document.querySelectorAll('.method-form');

    methodRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            methodForms.forEach(form => {
                if (form.dataset.method === radio.value) {
                    form.classList.remove('hidden');
                } else {
                    form.classList.add('hidden');
                }
            });
        });
    });

    const toPaymentBtn = document.getElementById('to-payment');
    toPaymentBtn.addEventListener('click', () => {
        if (billingForm.checkValidity()) {
            billingStep.classList.add('hidden');
            paymentStep.classList.remove('hidden');
        } else {
            billingForm.reportValidity();
        }
    });

    const paymentForm = document.getElementById('payment-form');
    paymentForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const serviceId = paymentForm.dataset.serviceId;
        const clientId = paymentForm.dataset.clientId;
        const freelancerId = paymentForm.dataset.freelancerId;
        const serviceTitle = paymentForm.dataset.serviceTitle;

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

});
