<?php
    require_once(__DIR__ .  '/../templates/common/header.tpl.php');
    require_once(__DIR__ .  '/../templates/category.tpl.php');
    require_once(__DIR__ .  '/../templates/service.tpl.php');
    require_once(__DIR__ .  '/../templates/search.tpl.php');
    require_once(__DIR__ .  '/../templates/chat.tpl.php');
    require_once(__DIR__ .  '/../templates/common/footer.tpl.php');
    require_once (__DIR__ . '/../templates/common/utils.tpl.php'); 

    $categories = Category::getAllWithSubcategories();
    $searchQuery = $_GET['q'] ?? '';
    $services = Service::getServicesBySearch($searchQuery, 20);

    drawHeader();
    drawCategoryMenu($categories);
    drawSearchPage('search.php', 'Search services...', false, 'alt-style', $searchQuery, $categories, $services);
    drawChat();
    drawFooter();
?>