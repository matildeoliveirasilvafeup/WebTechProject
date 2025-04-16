<?php
    require_once 'database/connection.php';
    require_once 'database/services.php';
    require_once 'database/categories.php';
    require_once 'database/reviews.php';

    $serviceId = $_GET['id'] ?? null;

    if (!$serviceId || !is_numeric($serviceId)) {
        header("Location: index.php");
        exit;
    }

    require 'templates/common/header.php';
    require 'templates/category-menu.php';
    require 'templates/reviews_stars.php';

    $service = getServiceById($db, (int)$serviceId);
    $ratingInfo = getServiceRatingInfo($db, (int)$serviceId);
    $reviews = getServiceReviews($db, (int)$serviceId);

    if (!$service) {
        echo "Service not found.";
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?= htmlspecialchars($service['title']) ?> | Serviços</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div class="service-page">  
            <img src="<?= htmlspecialchars($service['media_url'] ?? 'https://via.placeholder.com/480') ?>" alt="Imagem do serviço">

            <div class="service-details">
                <h1><?= htmlspecialchars($service['title']) ?></h1>
                <div class="freelancer-box">
                    <img src="<?= htmlspecialchars($service['profile_picture'] ?? 'https://via.placeholder.com/50') ?>" alt="Foto do freelancer">
                    <p class="freelancer">
                        By <strong><?= htmlspecialchars($service['freelancer_name']) ?></strong><br>
                        <?php if ($ratingInfo['avg']): ?>
                            <?= renderStars($ratingInfo['avg']) ?>
                            <?= $ratingInfo['avg'] ?> (<?= $ratingInfo['count'] ?> reviews)
                        <?php else: ?>
                            No reviews yet
                        <?php endif; ?>
                    </p>
                </div>
                <p class="price">€<?= number_format($service['price'], 2) ?></p>
                <div class="description">
                    <?= nl2br(htmlspecialchars($service['description'])) ?>
                </div>
                <div class="button-group">
                    <a href="contact_freelancer.php?id=<?= $service['freelancer_id'] ?>" class="btn-hire">Contact</a>
                    <a href="#" class="btn-add-cart">Add to Cart</a>
                </div>
            </div>

            <div class="info-actions">
                <button class="icon-btn favorite">
                    <i class="fas fa-heart"></i>
                    <span><?= $service['favorites_count'] ?? 0 ?></span>
                </button>
                <button class="icon-btn share" onclick="shareService()">
                    <i class="fas fa-share-alt"></i>
                </button>
                <a href="report_service.php?id=<?= $service['id'] ?>" class="icon-btn report" title="Report this service">
                    <i class="fas fa-flag"></i>
                </a>
            </div>

            <div class="service-info">
                <h3>Service Information</h3>
                <ul>
                    <li><i class="fas fa-clock"></i><strong> Delivery: </strong> <?= $service['delivery_time'] ?> days</li>
                    <li><i class="fas fa-tags"></i><strong> Category: </strong> <?= htmlspecialchars($service['category_name']) ?></li>
                    <li><i class="fas fa-sync-alt"></i><strong> Included Revisions: </strong> Until <?= $service['number_of_revisions'] ?> revisions</li>
                    <li><i class="fas fa-language"></i><strong> Language: </strong> <?= htmlspecialchars($service['language']) ?></li>
                </ul>
            </div>
        </div>

        <?php if (count($reviews) > 0): ?>
    <?php
        $totalReviews = count($reviews);
        $starCounts = array_fill(1, 5, 0);
        $totalRating = 0;

        foreach ($reviews as $r) {
            $rating = (int)$r['rating'];
            $totalRating += $rating;
            if ($rating >= 1 && $rating <= 5) {
                $starCounts[$rating]++;
            }
        }

        $averageRating = round($totalRating / $totalReviews, 1);
    ?>

    <div class="reviews-summary">
        <h2>Reviews</h2>
        <p><strong><?= $averageRating ?>★</strong> out of 5 — <?= $totalReviews ?> reviews</p>
        
        <div class="rating-bars">
            <?php for ($i = 5; $i >= 1; $i--): 
                $count = $starCounts[$i];
                $percent = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
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
            <input type="text" placeholder="Search in reviews...">
            <select>
                <option value="latest">Newest</option>
                <option value="oldest">Oldest</option>
                <option value="highest">Best rating</option>
                <option value="lowest">Worst rating</option>
            </select>
        </div>
    </div>

    <div class="reviews-section">
        <?php foreach ($reviews as $index => $review): ?>
            <div class="review-card" data-index="<?= $index ?>" style="<?= $index >= 3 ? 'display: none;' : '' ?>">
                <div class="review-header">
                    <img src="<?= htmlspecialchars($review['profile_picture'] ?? 'https://via.placeholder.com/40') ?>" alt="Foto do cliente">
                    <div>
                        <strong><?= htmlspecialchars($review['client_name']) ?></strong><br>
                        <?= renderStars($review['rating']) ?>
                    </div>
                </div>
                <p class="review-comment">"<?= htmlspecialchars($review['comment']) ?>"</p>
                <small class="review-date"><?= date('d M Y', strtotime($review['created_at'])) ?></small>
            </div>
        <?php endforeach; ?>

        <?php if (count($reviews) > 3): ?>
            <button class="load-more-btn" onclick="loadMoreReviews()">Load more</button>
        <?php endif; ?>
    </div>
        <?php else: ?>
            <div class="reviews-section">
                <h2>Reviews</h2>
                <p>This service doesn't have reviews yet.</p>
            </div>
        <?php endif; ?>


        <script src="js/reviews.js"></script>    
        <script src="js/share.js"></script>
        <div id="toast" class="toast">Link copied to clipboard!</div>
    </body>
</html>

<?php require 'templates/common/footer.php'; ?>