let CURRENT_STATUS = 'Pending';

document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('.custom-offer-form');
    const modal = document.getElementById('custom-offer-modal');
    const editButtons = document.querySelectorAll('.edit-offer-btn');
    const createButton = document.querySelector('.create-offer-btn');
    const closeModalBtn = document.querySelector('.close-modal');

    if (!form || !modal) return;

    editButtons.forEach(button => {
        button.addEventListener('click', () => {
            const offer = JSON.parse(button.dataset.offer);
            form.offer_id.value = offer.id;
            form.price.value = offer.price;
            form.delivery.value = offer.delivery_time;
            form.revisions.value = offer.number_of_revisions;

            modal.classList.remove('hidden');
        });
    });

    createButton?.addEventListener('click', () => {
        form.reset();
        form.offer_id.value = '';
        modal.classList.remove('hidden');
    });

    closeModalBtn?.addEventListener('click', () => {
        modal.classList.add('hidden');
    });

    form.addEventListener('submit', (e) => {
        const price = parseFloat(form.price.value);
        const delivery = parseInt(form.delivery.value);
        const revisions = parseInt(form.revisions.value);

        let errors = [];

        if (isNaN(price) || price < 0) {
            errors.push("Price must be 0 or more.");
        }

        if (isNaN(delivery) || delivery < 1) {
            errors.push("Delivery time must be at least 1 day.");
        }

        if (isNaN(revisions) || revisions < 0) {
            errors.push("Revisions must be 0 or more.");
        }

        if (errors.length > 0) {
            e.preventDefault();
            alert(errors.join('\n'));
        } else {
            const status = CURRENT_STATUS;
            const hiring_id = form.querySelector('input[name="hiring_id"]').value;
            const service_id = form.querySelector('input[name="service_id"]').value;
            const sender_id = form.querySelector('input[name="sender_id"]').value;
            const receiver_id = form.querySelector('input[name="receiver_id"]').value;

            sendOfferMessage(status, hiring_id, sender_id, receiver_id, service_id);
            updateOfferStatus(status, hiring_id, sender_id, receiver_id, service_id);
        }
    });
});

function sendOfferMessage(status, hiring_id, sender_id, receiver_id, service_id, fileInput = null) {

    let message;
    if (status == 'Pending') {
        message = `A new offer has been created. ${status}!`;
    } else if (status == 'Accepted') {
        message = `A offer has been accepted. ${status}!`;
    } else if (status == 'Rejected') {
        message = `A offer has been rejected. ${status}!`;
    } else if (status == 'Cancelled') {
        message = `A offer has been cancelled. ${status}!`;
    }

    const subMessage = 'Click to see details';

    const ids = [sender_id, receiver_id].sort((a, b) => a - b);
    const conversation_id = `${ids[0]}_${ids[1]}`;

    const formData = new FormData();
    formData.append('conversation_id', conversation_id);
    formData.append('hiring_id', hiring_id);
    formData.append('service_id', service_id);
    formData.append('sender_id', sender_id);
    formData.append('receiver_id', receiver_id);
    formData.append('sub_message', subMessage);
    formData.append('message', message);
    
    if (fileInput) {
        formData.append('file', fileInput);
    }

    fetch('/actions/action_send_message.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            console.error('Error sending message:', data.error || 'Unknown error');
        } else {
            console.log('Message sent successfully:', data);
        }
    })
    .catch(err => {
        console.error('Error sending:', err);
    });
}

function updateOfferStatus(status, hiringId, senderId, receiverId, serviceId) {
    CURRENT_STATUS = status;
    sendOfferMessage(status, hiringId, senderId, receiverId, serviceId);

    const formData1 = new FormData();
    formData1.append('hiring_id', hiringId);

    fetch('/actions/action_check_hiring_status.php', {
        method: 'POST',
        body: formData1
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error checking status');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const newStatus = data.status;

            console.log('Status checked:', newStatus);
            alert(`Status changed to ${newStatus}`);

            const formData2 = new FormData();
            formData2.append('id', hiringId);
            formData2.append('status', newStatus);

            return fetch('/actions/action_update_hiring_status.php', {
                method: 'POST',
                body: formData2
            });
        } else {
            throw new Error('Failed to check status');
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error updating hiring status');
        }
        return response.json();
    })
    .then(data => {
        console.log('Response from update:', data);
        if (data.success) {
            console.log('Hiring status updated successfully');
        } else {
            console.error('Update failed with message:', data.message);
            throw new Error(data.message || 'Unknown error from hiring update');
        }
    })
    .catch(error => {
    });
}
