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
        }
    });
});
