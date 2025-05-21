function openReviewModal() {
    document.getElementById('review-modal').classList.remove('hidden');
}

function closeReviewModal() {
    document.getElementById('review-modal').classList.add('hidden');
}

document.getElementById('review-form').addEventListener('submit', function (e) {
    e.preventDefault();
    
    const rating = this.rating.value;
    const comment = this.comment.value;

    closeReviewModal();
});

document.addEventListener('DOMContentLoaded', () => {
    const stars = document.querySelectorAll('#star-rating-input i');
    const ratingInput = document.getElementById('rating');
    let selectedRating = 0;

    stars.forEach(star => {
        star.addEventListener('mouseenter', () => {
        const val = parseInt(star.dataset.value);
        updateStars(val);
        });

        star.addEventListener('mouseleave', () => {
        updateStars(selectedRating);
        });

        star.addEventListener('click', () => {
        selectedRating = parseInt(star.dataset.value);
        ratingInput.value = selectedRating;
        updateStars(selectedRating);
        });
    });

    function updateStars(rating) {
        stars.forEach(star => {
        const val = parseInt(star.dataset.value);
        star.classList.remove('fa-solid', 'fa-regular', 'checked');
        if (val <= rating) {
            star.classList.add('fa-solid', 'checked');
        } else {
            star.classList.add('fa-regular');
        }
        });
    }
});

