<?php
    require_once(__DIR__ .  '/../includes/session.php');

    require_once(__DIR__ .  '/../database/service.class.php');
    require_once(__DIR__ .  '/../database/category.class.php');
    require_once(__DIR__ .  '/../database/review.class.php');
    require_once(__DIR__ .  '/../database/favorites.class.php');
    require_once(__DIR__ .  '/../database/chat.class.php');
    require_once(__DIR__ .  '/../database/hirings.class.php');
    require_once(__DIR__ .  '/../database/user.class.php');
    require_once(__DIR__ .  '/../database/custom_offer.class.php');

    require_once(__DIR__ .  '/../templates/category.tpl.php');
    require_once(__DIR__ .  '/../templates/review.tpl.php');
    require_once(__DIR__ .  '/../templates/service.tpl.php');
    require_once(__DIR__ .  '/../templates/chat.tpl.php');
    require_once(__DIR__ .  '/../templates/hirings.tpl.php');
    require_once(__DIR__ .  '/../templates/common/header.tpl.php');
    require_once(__DIR__ .  '/../templates/common/footer.tpl.php');
    require_once(__DIR__ .  '/../templates/common/toast.tpl.php');
    require_once(__DIR__ .  '/../templates/common/utils.tpl.php');

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

    $session = Session::getInstance();
    $user = $session->getUser();
    $userId = $user ? $user->id : 0;
    $ratingInfo = Review::getServiceRatingInfo((int)$service->id);
    $reviews = Review::getServiceReviews((int)$service->id);
    $moreFromFreelancer = Service::getMoreFromFreelancer((int)$service->freelancerId, (int)$service->id, 100);
    $relatedServices = Service::getRelated($service->categoryId, $service->id, 100);
    $averageRating = Review::getAverageRating($reviews);
    $isAdmin = Session::isAdmin();
    $eligibleHiringId = Service::getEligibleHiringIdForReview($userId, $serviceId);

    drawHeader();
    drawCategoryMenu($categories);
    drawServicePage($service, $ratingInfo);
    drawReviewBlock($service, $reviews, $averageRating, $isAdmin, $eligibleHiringId);
    drawMoreFromFreelancer($service, $moreFromFreelancer);
    drawRelatedServices($relatedServices); 
    drawCopyToast();
    drawChat();
    drawHirings();
    drawFooter();
?>