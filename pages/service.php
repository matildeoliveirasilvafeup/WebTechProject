<?php
    require_once '../database/service.class.php';
    require_once '../database/category.class.php';
    require_once '../database/review.class.php';
    require '../templates/common/header.tpl.php';
    require '../templates/category.tpl.php';
    require '../templates/review.tpl.php';
    require '../templates/service.tpl.php';
    require '../templates/common/footer.tpl.php';
    require '../templates/common/copytoast.tpl.php';

    $serviceId = $_GET['id'] ?? null;
    if (!$serviceId || !is_numeric($serviceId)) {
        header("Location: index.php");
        exit;
    }


    $categories = Category::getAllWithSubcategories();
    $service = Service::getById((int)$serviceId);
    if (!$service) {
        echo "Service not found.";
        exit;
    }

    $ratingInfo = Review::getServiceRatingInfo((int)$service->id);
    $reviews = Review::getServiceReviews((int)$service->id);
    $moreFromFreelancer = Service::getMoreFromFreelancer((int)$service->freelancerId, (int)$service->id, 100);
    $relatedServices = Service::getRelated($service->categoryId, $service->id, 100);
    $averageRating = Review::getAverageRating($reviews);

    drawHeader();
    drawCategoryMenu($categories);
    drawServicePage($service, $ratingInfo);
    drawReviewBlock($reviews, $averageRating);
    drawMoreFromFreelancer($service, $moreFromFreelancer); 
    drawRelatedServices($relatedServices); 
    drawCopyToast();
    drawFooter();
?>