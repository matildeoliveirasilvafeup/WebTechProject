<?php
require_once '../includes/database.php';
require_once '../database/service.class.php';
require_once '../database/review.class.php';

require '../templates/common/header.tpl.php';
require '../templates/category.tpl.php';
require '../templates/review.tpl.php';

$categories = Category::getAllWithSubcategories();

drawHeader();
drawCategoryMenu($categories);

require '../templates/service_cards_slider.php';

$testimonials = Review::getLatestReviews(3);
$categories = Category::getAll();
$featuredServices = Service::getFeatured(100);
?>

<div class="hero">
    <h1 id="typing-effect">Find the perfect freelancer for your project</h1>
    <form method="GET" action="search.php">
        <input type="text" id="search-service-input" placeholder="Search services...">
        <button type="submit"><i class="fas fa-search"></i></button>
    </form>
</div>  

<?php drawCategorySection($categories) ?>

<div class="featured-services">
    <h2>Featured Services</h2>
    <?= renderServiceSlider($featuredServices) ?>
</div>

<div class="how-it-works">
    <h2>How It Works</h2>
    <div class="steps">
        <div class="step">
            <i class="fas fa-user-plus"></i>
            <h3>Create an Account</h3>
            <p>Sign up for free and create your profile as a freelancer or client.</p>
        </div>
        <div class="step">
            <i class="fas fa-search"></i>
            <h3>Find or Offer Services</h3>
            <p>Browse through categories or publish your own service with ease.</p>
        </div>
        <div class="step">
            <i class="fas fa-handshake"></i>
            <h3>Work & Get Paid</h3>
            <p>Collaborate, deliver quality work, and complete transactions securely.</p>
        </div>
    </div>
</div>

<?php drawTestimonials($testimonials); ?>

<div class="final-cta">
    <h2>Ready to join our freelance marketplace?</h2>
    <p>Whether you're here to offer your talent or find it, you're just a click away.</p>
    <div class="cta-buttons">
        <a href="register.php" class="cta-btn primary">Join Now</a>
        <a href="search.php" class="cta-btn secondary">Browse Services</a>
    </div>
</div>
<script src="../js/slider.js"></script>                 

<?php 
    require '../templates/common/footer.tpl.php'; 
    drawFooter();
?>
