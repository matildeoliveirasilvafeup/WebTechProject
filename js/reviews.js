let currentVisible = 3;
function loadMoreReviews() {
    const cards = document.querySelectorAll('.review-card');
    let shown = 0;

    for (let i = currentVisible; i < cards.length && shown < 3; i++) {
        cards[i].style.display = 'block';
        shown++;
    }

    currentVisible += shown;

    if (currentVisible >= cards.length) {
        document.querySelector('.load-more-btn').style.display = 'none';
    }
}


document.addEventListener('DOMContentLoaded', () => {
    const sortSelect = document.getElementById('review-sort');
    const reviewContainer = document.querySelector('.reviews-section');
    const loadMoreBtn = reviewContainer.querySelector('.load-more-btn');

    const getSortedReviews = (reviews, criterion) => {
        return reviews.sort((a, b) => {
            const ratingA = parseInt(a.dataset.rating);
            const ratingB = parseInt(b.dataset.rating);
            const dateA = new Date(a.dataset.date);
            const dateB = new Date(b.dataset.date);

            switch (criterion) {
                case 'latest': return dateB - dateA;
                case 'oldest': return dateA - dateB;
                case 'highest': return ratingB - ratingA;
                case 'lowest': return ratingA - ratingB;
                default: return 0;
            }
        });
    };

    sortSelect.addEventListener('change', () => {
        const criterion = sortSelect.value;
        const reviewCards = Array.from(reviewContainer.querySelectorAll('.review-card'));
        const sorted = getSortedReviews(reviewCards, criterion);

        currentVisible = 3;

        reviewCards.forEach(card => card.remove());

        sorted.forEach((card, i) => {
            card.style.display = i < 3 ? 'block' : 'none';
            reviewContainer.insertBefore(card, loadMoreBtn);
        });

        if (sorted.length > 3) {
            loadMoreBtn.style.display = 'block';
        } else {
            loadMoreBtn.style.display = 'none';
        }
    });
});
