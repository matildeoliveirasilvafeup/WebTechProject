<?php
declare(strict_types=1);
require_once(__DIR__ .  '/../database/category.class.php');
?>

<?php function drawCategoryMenu(array $categories): void { ?>
    <div class="category-menu">
        <ul>
            <?php foreach ($categories as $category): ?>
                <li class="category-item">
                    <a href="#"><?php echo htmlspecialchars($category->name); ?></a>
                    <?php if (!empty($category->subcategories)): ?>
                        <ul class="subcategories">
                            <?php foreach ($category->subcategories as $subcategory): ?>
                                <li><a href="#"><?php echo htmlspecialchars($subcategory->name); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php } ?>

<?php function drawCategorySection($categories) { ?>
    <div class="category-section">
    <div class="carousel-wrapper">
        <div class="category-carousel">
            <?php foreach ($categories as $category): ?>
                <a href="search.php?category=<?= $category->id ?>" class="category-card">
                    <i class="icon <?= htmlspecialchars($category->icon) ?>"></i>
                    <p><?= htmlspecialchars($category->name) ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>    
</div>
<?php } ?>