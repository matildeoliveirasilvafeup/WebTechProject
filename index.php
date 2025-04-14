<?php
require_once 'database/connection.php';
require_once 'database/categories.php';
require_once 'database/services.php';
require_once 'database/reviews.php';
session_start();
require 'templates/common/header.php';
require 'templates/category-menu.php';

$testimonials = getLatestReviews($db);
$categories = getAllCategories($db);
$featuredServices = getFeaturedServices($db);;
?>

<div class="hero">
    <h1 id="typing-effect">Find the perfect freelancer for your project</h1>
    <form method="GET" action="search.php">
        <input type="text" name="q" placeholder="Search services...">
        <button type="submit"><i class="fas fa-search"></i></button>
    </form>
</div>  

<div class="category-section">
    <div class="carousel-wrapper">
        <div class="category-carousel">
            <?php foreach ($categories as $category): ?>
                <a href="search.php?category=<?= $category['id'] ?>" class="category-card">
                    <i class="icon <?= htmlspecialchars($category['icon']) ?>"></i>
                    <p><?= htmlspecialchars($category['name']) ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>    
</div>

<div class="featured-services">
    <h2>Featured Services</h2>
    <div class="services-grid">
        <?php foreach ($featuredServices as $service): ?>
            <a href="service.php?id=<?= $service['id'] ?>" class="service-card">
                <img src="<?= htmlspecialchars($service['media_url'] ?? 'https://via.placeholder.com/300') ?>" alt="Service image">
                <div class="service-info">
                    <h3><?= htmlspecialchars($service['title']) ?></h3>
                    <p class="freelancer">By <?= htmlspecialchars($service['freelancer_name']) ?></p>
                    <p class="price">€<?= number_format($service['price'], 2) ?></p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
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

<div class="testimonials">
    <h2>What Clients Are Saying</h2>
    <div class="testimonial-cards">
        <?php foreach ($testimonials as $review): ?>
            <div class="testimonial">
                <p class="comment">"<?= htmlspecialchars($review['comment']) ?>"</p>
                <p class="client">– <?= htmlspecialchars($review['client_name']) ?> on <strong><?= htmlspecialchars($review['service_title']) ?></strong></p>
                <div class="rating">
                    <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                        <i class="fas fa-star"></i>
                    <?php endfor; ?>
                    <?php for ($i = $review['rating']; $i < 5; $i++): ?>
                        <i class="far fa-star"></i>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require 'templates/common/footer.php'; ?>
