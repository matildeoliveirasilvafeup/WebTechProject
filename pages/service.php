<?php
    require_once '../database/service.class.php';
    require_once '../database/category.class.php';
    require_once '../database/review.class.php';

    $serviceId = $_GET['id'] ?? null;

    if (!$serviceId || !is_numeric($serviceId)) {
        header("Location: index.php");
        exit;
    }

    require '../templates/common/header.tpl.php';
    require '../templates/category.tpl.php';

    $categories = Category::getAllWithSubcategories();

    drawHeader();
    drawCategoryMenu($categories);
    require '../templates/review.tpl.php';
    require '../templates/service_cards_slider.php';

    $service = Service::getById((int)$serviceId);
    $ratingInfo = Review::getServiceRatingInfo((int)$serviceId);
    $reviews = Review::getServiceReviews((int)$serviceId);
    $moreFromFreelancer = Service::getMoreFromFreelancer((int)$service->freelancerId, (int)$service->id, 100);
    $relatedServices = Service::getRelated($service->categoryId, $service->id, 100);
    $averageRating = Review::getAverageRating($reviews);

    if (!$service) {
        echo "Service not found.";
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?= htmlspecialchars($service->title) ?> | Serviços</title>
        <link rel="stylesheet" href="../css/style.css">
    </head>
    <body>
        <div class="service-page">  
            <img src="<?= htmlspecialchars($service->mediaUrl ?? 'https://via.placeholder.com/480') ?>" alt="Imagem do serviço">

            <div class="service-details">
                <h1><?= htmlspecialchars($service->title) ?></h1>
                <div class="freelancer-box">
                    <img src="<?= htmlspecialchars($service->profilePicture ?? 'https://via.placeholder.com/50') ?>" alt="Foto do freelancer">
                    <p class="freelancer">
                        By <strong><?= htmlspecialchars($service->freelancerName) ?></strong><br>
                        <?php if ($ratingInfo['avg']): ?>
                            <?= renderStars($ratingInfo['avg']) ?>
                            <?= $ratingInfo['avg'] ?> (<?= $ratingInfo['count'] ?> reviews)
                        <?php else: ?>
                            No reviews yet
                        <?php endif; ?>
                    </p>
                </div>
                <p class="price">€<?= number_format($service->price, 2) ?></p>
                <div class="description">
                    <?= nl2br(htmlspecialchars($service->description)) ?>
                </div>
                <div class="button-group">
                    <a href="contact_freelancer.php?id=<?= $service->freelancerId ?>" class="btn-hire">Contact</a>
                    <a href="#" class="btn-add-cart">Add to Cart</a>
                </div>
            </div>

            <div class="info-actions">
                <button class="icon-btn favorite">
                    <i class="fas fa-heart"></i>
                    <span><?= $service->favoritesCount?></span>
                </button>
                <button class="icon-btn share" onclick="shareService()">
                    <i class="fas fa-share-alt"></i>
                </button>
                <a href="report_service.php?id=<?= $service->id ?>" class="icon-btn report" title="Report this service">
                    <i class="fas fa-flag"></i>
                </a>
            </div>

            <div class="service-info">
                <h3>Service Information</h3>
                <ul>
                    <li><i class="fas fa-clock"></i><strong> Delivery: </strong> <?= $service->deliveryTime ?> days</li>
                    <li><i class="fas fa-tags"></i><strong> Category: </strong> <?= htmlspecialchars($service->categoryName) ?></li>
                    <li><i class="fas fa-sync-alt"></i><strong> Included Revisions: </strong> Until <?= $service->numberOfRevisions ?> revisions</li>
                    <li><i class="fas fa-language"></i><strong> Language: </strong> <?= htmlspecialchars($service->language) ?></li>
                </ul>
            </div>
        </div>

    <?php if (count($reviews) > 0) { 
        drawReviewsSummary($averageRating);
        drawReviewSection($reviews);    
    } else {
        drawEmptyReviewSection();
    } ?>

    <?php if (!empty($moreFromFreelancer)): ?>
        <div class="freelancer-services">
            <h2>More Services from <?= htmlspecialchars($service->freelancerName) ?></h2>
            <?= renderServiceSlider($moreFromFreelancer, 4) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($relatedServices)): ?>
        <div class="freelancer-services">
            <h2>You may also like: </h2>
            <?= renderServiceSlider($relatedServices, 4) ?>
        </div>
    <?php endif; ?>

        <script src="../js/slider.js"></script>                 
        <script src="../js/reviews.js"></script>    
        <script src="../js/share.js"></script>
        <div id="toast" class="toast">Link copied to clipboard!</div>
    </body>
</html>

<?php 
    require '../templates/common/footer.tpl.php'; 
    drawFooter();
?>