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
    require '../templates/service.tpl.php';

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

    drawServicePage($service, $ratingInfo);
?>

    <?php if (count($reviews) > 0) { 
        drawReviewsSummary($averageRating);
        drawReviewSection($reviews);    
    } else {
        drawEmptyReviewSection();
    } ?>

    <?php 
        drawMoreFromFreelancer($service, $moreFromFreelancer); 
        drawRelatedServices($relatedServices); 
    ?>

        <script src="../js/slider.js"></script>                 
        <script src="../js/reviews.js"></script>    
        <script src="../js/share.js"></script>
        <div id="toast" class="toast">Link copied to clipboard!</div>

<?php 
    require '../templates/common/footer.tpl.php'; 
    drawFooter();
?>