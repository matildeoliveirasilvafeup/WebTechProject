<?php
declare(strict_types=1);
require_once(__DIR__ .  '/../database/review.class.php');

function renderStars(float $rating): string {
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

    return str_repeat('⭐', (int)$fullStars) .
           ($halfStar ? '✩' : '') .
           str_repeat('☆', (int)$emptyStars);
}
?>


<?php function drawReviewsSummary($averageRating) { ?>
    <div class="reviews-summary">
        <h2>Reviews</h2>
        <p><strong><?= $averageRating['average'] ?>★</strong> out of 5 — <?= $averageRating['total'] ?> reviews</p>
        
        <div class="rating-bars">
            <?php for ($i = 5; $i >= 1; $i--): 
                $count = $averageRating['counts'][$i];
                $percent = $averageRating['total'] > 0 ? ($count / $averageRating['total']) * 100 : 0;
            ?>
                <div class="rating-bar">
                    <span><?= $i ?>★</span>
                    <div class="bar">
                        <div class="fill" style="width: <?= $percent ?>%;"></div>
                    </div>
                    <span><?= $count ?></span>
                </div>
            <?php endfor; ?>
        </div>

        <div class="reviews-controls">
            <button class="btn-add-cart" onclick="openReviewModal()">Write a review</button>
            <select id="review-sort">
                <option value="latest">Newest</option>
                <option value="oldest">Oldest</option>
                <option value="highest">Best rating</option>
                <option value="lowest">Worst rating</option>
            </select>
        </div>
    </div>
<?php } ?>

<?php function drawReviewSection($reviews) { ?>
    <div class="reviews-section">
        <?php foreach ($reviews as $index => $review): ?>
            <div class="review-card" data-index="<?= $index ?>" 
                data-rating="<?= $review->rating ?>"
                data-date="<?= htmlspecialchars($review->createdAt) ?>"
                style="<?= $index >= 3 ? 'display: none;' : '' ?>">
                <div class="review-header">
                    <img src="<?= htmlspecialchars($review->profilePicture ?? 'https://via.placeholder.com/40') ?>" alt="Foto do cliente">
                    <div>
                        <strong><?= renderUserLink($review->clientUsername,$review->clientName) ?></strong><br>
                        <?= renderStars($review->rating) ?>
                    </div>
                </div>
                <p class="review-comment">"<?= htmlspecialchars($review->comment) ?>"</p>
                <small class="review-date"><?= date('d M Y', strtotime($review->createdAt)) ?></small>
            </div>
        <?php endforeach; ?>

        <?php if (count($reviews) > 3): ?>
            <button class="load-more-btn" onclick="loadMoreReviews()">Load more</button>
        <?php endif; ?>
    </div>
    <script src="../js/reviews.js"></script>    
<?php } ?>

<?php function drawEmptyReviewSection() { ?>
    <div class="reviews-summary">
        <h2>Reviews</h2>
        <p>This service doesn't have reviews yet.</p>
    </div>
<?php } ?>

<?php function drawTestimonials($testimonials) { ?>
    <div class="testimonials">
        <h2>What Clients Are Saying</h2>
        <div class="testimonial-cards">
            <?php foreach ($testimonials as $review): ?>
                <div class="testimonial">
                    <p class="comment">"<?= htmlspecialchars($review->comment) ?>"</p>
                    <p class="client">– <?= htmlspecialchars($review->clientName) ?> on <strong><?= htmlspecialchars($review->serviceTitle) ?></strong></p>
                    <div class="rating">
                        <?php for ($i = 0; $i < $review->rating; $i++): ?>
                            <i class="fas fa-star"></i>
                        <?php endfor; ?>
                        <?php for ($i = $review->rating; $i < 5; $i++): ?>
                            <i class="far fa-star"></i>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php } ?>

<?php function drawReviewBlock($reviews, $averageRating) {
    if (count($reviews) > 0) { 
        drawReviewsSummary($averageRating);
        drawReviewSection($reviews);    
    } else {
        drawEmptyReviewSection();
    }
} ?>    