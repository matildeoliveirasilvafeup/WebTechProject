<?php
    require_once '../includes/database.php';
    require_once '../database/service.class.php';
    require_once '../database/review.class.php';

    require '../templates/common/header.tpl.php';
    require '../templates/category.tpl.php';
    require '../templates/review.tpl.php';
    require '../templates/common/footer.tpl.php'; 
    require '../templates/service.tpl.php';
    require '../templates/home.tpl.php';

    $categories = Category::getAllWithSubcategories();
    $testimonials = Review::getLatestReviews(3);
    $categories = Category::getAll();
    $featuredServices = Service::getFeatured(100);

    drawHeader();
    drawCategoryMenu($categories); 
    drawHero();
    drawCategorySection($categories); 
    drawFeaturedServices($featuredServices);
    drawHowItWorks();
    drawTestimonials($testimonials);
    drawFinalCTA();
    drawFooter();
?>
