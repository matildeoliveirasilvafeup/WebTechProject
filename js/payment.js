import { createHiring, sendStatusMessage, startConversation } from './chat_hiring_utils.js';

document.addEventListener('DOMContentLoaded', () => {
    const billingForm = document.getElementById('billing-form');
    // const goBackStep = document.getElementById('step-go-back');
    const paymentStep = document.getElementById('step-payment');
    const billingStep = document.getElementById('step-billing');

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

                // Aguarda cada chamada assíncrona
                await startConversation(serviceId, clientId, freelancerId, 'false');
                await createHiring(serviceId, clientId, freelancerId);
                await sendStatusMessage('Pending', clientId, freelancerId, serviceId, serviceTitle);

                console.log('here here here');
                window.location.href = '/pages/home_page.php';
            } else {
                resultDiv.innerHTML = `<p class="error">${data.message}</p>`;
            }
        });
    });

});


function openPaymentModal(serviceId) {
    const modal = document.getElementById('payment-modal');
    modal.classList.add('visible');
    modal.dataset.serviceId = serviceId;
}

function closePaymentModal() {
    document.getElementById('payment-modal').classList.remove('visible');
}

function confirmPayment() {
    const modal = document.getElementById('payment-modal');
    const serviceId = modal.dataset.serviceId;
    const paymentMethod = document.querySelector('input[name="payment-method"]:checked')?.value;

    if (!paymentMethod) {
        alert('Choose a payment method');
        return;
    }

    fetch('/actions/simulate_payment.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            service_id: serviceId,
            method: paymentMethod
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Payment simulated successfully!');
            closePaymentModal();
        } else {
            alert('Payment failed: ' + data.error);
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Unexpected error occurred.');
    });
}
