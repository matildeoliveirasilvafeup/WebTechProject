<?php
    require_once(__DIR__ .  '/../templates/common/header.tpl.php');
    require_once(__DIR__ .  '/../templates/category.tpl.php');
    require_once(__DIR__ .  '/../templates/service.tpl.php');
    require_once(__DIR__ .  '/../templates/search.tpl.php');
    require_once(__DIR__ .  '/../templates/common/footer.tpl.php');

    $categories = Category::getAllWithSubcategories();
    $searchQuery = $_GET['q'] ?? '';
    $services = Service::getServicesBySearch($searchQuery, 20);

    drawHeader();
    drawCategoryMenu($categories);
    echo drawSearchForm('search.php', 'Search services...', false, 'alt-style', $searchQuery);
    echo renderServiceGrid($services);
    drawFooter();
?>