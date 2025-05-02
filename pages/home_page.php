<?php
    require_once(__DIR__ .  '../includes/database.php');
    require_once(__DIR__ .  '../database/service.class.php');
    require_once(__DIR__ .  '../database/review.class.php');

    require_once(__DIR__ .  '../templates/common/header.tpl.php');
    require_once(__DIR__ .  '../templates/category.tpl.php');
    require_once(__DIR__ .  '../templates/review.tpl.php');
    require_once(__DIR__ .  '../templates/common/footer.tpl.php'); 
    require_once(__DIR__ .  '../templates/service.tpl.php');
    require_once(__DIR__ .  '../templates/home.tpl.php');

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
