<?php
require_once 'database/connection.php';
require_once 'database/categories.php';
session_start();
require 'templates/common/header.php';
require 'templates/category-menu.php';

$categories = getAllCategories($db);
?>

<div class="hero">
    <h1 id="typing-effect">Find the perfect freelancer for your project</h1>
    <form method="GET" action="search.php">
        <input type="text" name="q" placeholder="Search services...">
        <button type="submit"><i class="fas fa-search"></i></button>
    </form>
</div>  

<div class="services">
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

<?php require 'templates/common/footer.php'; ?>
