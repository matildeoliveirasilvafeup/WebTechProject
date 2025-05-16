<?php
    require_once(__DIR__ .  '/../includes/database.php');
    require_once(__DIR__ .  '/../includes/session.php');
    
    require_once(__DIR__ .  '/../database/service.class.php');
    require_once(__DIR__ .  '/../database/chat.class.php');

    require_once(__DIR__ .  '/../templates/common/header.tpl.php');
    require_once(__DIR__ .  '/../templates/category.tpl.php');
    require_once(__DIR__ .  '/../templates/review.tpl.php');
    require_once(__DIR__ .  '/../templates/common/footer.tpl.php'); 
    require_once(__DIR__ .  '/../templates/service.tpl.php');
    require_once(__DIR__ .  '/../templates/home.tpl.php');
    require_once(__DIR__ .  '/../templates/chat.tpl.php');

    $categoriesMenu = Category::getAllWithSubcategories();
    $testimonials = Review::getLatestReviews(3);
    $featuredServices = Service::getFeatured(100);

    drawHeader();
    drawCategoryMenu($categoriesMenu); 
    drawHero();
    drawCategorySection($categoriesMenu); 
    drawFeaturedServices($featuredServices);
    drawHowItWorks();
    drawTestimonials($testimonials);
    drawFinalCTA();
    drawChat();
    drawFooter();
?>
