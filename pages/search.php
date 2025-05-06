<?php
    require_once(__DIR__ .  '/../templates/common/header.tpl.php');
    require_once(__DIR__ .  '/../templates/category.tpl.php');
    require_once(__DIR__ .  '/../templates/search.tpl.php');
    require_once(__DIR__ .  '/../templates/common/footer.tpl.php');

    $categories = Category::getAllWithSubcategories();
    $searchQuery = $_GET['q'] ?? '';

    drawHeader();
    drawCategoryMenu($categories);
    echo drawSearchForm('search.php', 'Search services...', false, 'alt-style', $searchQuery);
    drawFooter();
?>