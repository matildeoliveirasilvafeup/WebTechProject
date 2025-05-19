<?php
declare(strict_types=1);
require_once(__DIR__ .  '/../database/category.class.php');
?>

<?php function drawCategoryMenu(array $categories): void { ?>
    <div class="category-menu">
        <ul>
            <?php foreach ($categories as $category): ?>
                <li class="category-item">
                    <a href="search.php?sort=newest&category=<?= $category->id ?>&min_price=0&max_price=9999&delivery_time=&number_of_revisions=&language=">
                        <?= htmlspecialchars($category->name); ?>
                    </a>
                    <?php if (!empty($category->subcategories)): ?>
                        <ul class="subcategories">
                            <?php foreach ($category->subcategories as $subcategory): ?>
                                <li>
                                    <a href="search.php?sort=newest&category=<?= $category->id ?>&subcategory[]=<?= $subcategory->id ?>&min_price=0&max_price=9999&delivery_time=&number_of_revisions=&language=">                                  
                                        <?= htmlspecialchars($subcategory->name); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php } ?>

<?php function drawCategorySection($categories, $isAdmin) { 
    $counter = $isAdmin ? count($categories) + 1: count($categories)?>
    <div class="category-section">
        <div class="carousel-wrapper">
            <?php if ($counter > 10): ?>
                <button class="slider-btn left" onclick="scrollCategorySlider(this, -1)">‹</button>
            <?php endif; ?>
            <div class="category-carousel">
                <?php foreach ($categories as $category): ?>
                    <a href="search.php?category=<?= $category->id ?>" class="category-card">
                        <i class="icon <?= htmlspecialchars($category->icon) ?>"></i>
                        <p><?= htmlspecialchars($category->name) ?></p>
                    </a>
                <?php endforeach; ?>

                <?php if ($isAdmin): ?>
                    <a href="create_category.php" class="category-card">
                        <i class="icon fas fa-plus"></i>
                        <p>Create New Category</p>
                    </a>
                <?php endif; ?>
            </div>
            <?php if ($counter > 10): ?>
                <button class="slider-btn right" onclick="scrollCategorySlider(this, 1)">›</button>
            <?php endif; ?>
        </div>    
    </div>

    <script src="../js/slider.js"></script>
<?php } ?>

<?php function drawCategoryForm() { ?>
    <section class="service-page" id="create_category">
        <form action="/actions/action_create_category.php" method="POST" enctype="multipart/form-data" class="create-form">
            <h1>New Category</h1>

            <label for="category-name">Category Name</label>
            <input type="text" id="category-name" name="category_name" required>

            <label for="category-icon">Category Icon</label>
            <input type="text" id="category-icon" name="category_icon" placeholder="e.g., fsa fa-solid fsa fa-code" required>

            <div class="form-group">
                <h3>Subcategories</h3>
                <p>Add one or more subcategories for this category:</p>
                <div id="subcategory-container">
                    <div class="subcategory-input">
                        <input type="text" name="subcategories[]" placeholder="Subcategory name" required>
                    </div>
                </div>
            </div>

            <div class="button-group">
                <button type="submit" class="btn-add-cart">Create Category</button>
                <a href="/index.php" class="btn-hire">Cancel</a>
            </div>
        </form>
    </section>

<script src="../js/category_form.js"></script>

<?php } ?>