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
