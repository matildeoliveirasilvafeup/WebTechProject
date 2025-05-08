<?php
declare(strict_types=1);

function drawSearchForm(string $action = 'search.php', string $placeholder = 'Search services...', bool $isButton = true, string $extraClass = '', 
    string $initialValue = '') {
?>
    <form method="GET" action="<?= htmlspecialchars($action) ?>" class="<?= htmlspecialchars($extraClass) ?>">
        <?php if (!$isButton): ?>
            <span class="search-icon"><i class="fas fa-search"></i></span>
        <?php endif; ?>

        <input type="text" name="q" id="search-service-input" placeholder="<?= htmlspecialchars($placeholder) ?>" 
            value="<?= htmlspecialchars($initialValue) ?>">

        <?php if ($isButton): ?>
            <button type="submit"><i class="fas fa-search"></i></button>
        <?php endif; ?>
    </form>
<?php } ?>

<?php
function drawFilters(array $categories) {
?>
    <aside class="filters">
        <div class="title">
            <p>Filters</p>
        </div>

        <form method="GET" action="search.php" class="filter-form">
            <div class="filter-group">
                <label for="sort">Sort by</label>
                <select name="sort">
                    <option value="newest">Newest</option>
                    <option value="oldest">Oldest</option>
                    <option value="lowest_price">Lowest Price</option>
                    <option value="highest_price">Highest Price</option>
                    <option value="lowest_rating">Lowest Rating</option>
                    <option value="highest_rating">Highest Rating</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="category">Category</label>
                <select name="category" id="category">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category->name) ?>" 
                                data-subcategories='<?= json_encode($category->subcategories ?? []) ?>'>
                            <?= htmlspecialchars($category->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group" id="subcategory-container" style="display: none;">
                <label>Subcategories</label>
                <div id="subcategory-checkboxes"></div>
            </div>

            <div class="filter-group">
                <label for="price-range">Price Range (€)</label>
                <div class="price-input-container">
                    <div class="price-field">
                        <span>Minimum Price</span>
                        <input type="number" class="min-input" value="0">
                    </div>
                    <div class="price-field">
                        <span>Maximum Price</span>
                        <input type="number" class="max-input" value="9999">
                    </div>
                </div>
            </div>  

            <div class="filter-group">
                <label for="delivery_time">Max Delivery Time (days)</label>
                <input type="number" name="delivery_time" min="1" placeholder="Infinite">
            </div>

            <div class="filter-group">
                <label for="number_of_revisions">Min Revisions</label>
                <input type="number" name="number_of_revisions" min="0" placeholder="0">
            </div>

            <div class="filter-group">
                <label for="language">Language</label>
                <input type="text" name="language" placeholder="English">
            </div>
        </form>
    </aside>

    <script src="../js/search.js"></script>
<?php
}
?>

<?php function drawSearchPage(string $action = 'search.php', string $placeholder = 'Search services...', bool $isButton = true, string $extraClass = '', 
    string $initialValue = '', array $categories, array $services){ ?>
    
    <?= drawSearchForm('search.php', 'Search services...', false, 'alt-style', $initialValue); ?>
    <div class="search-page">
        <?= drawFilters($categories); 
        drawServiceGrid($services); ?>
    </div>

<?php } ?>

