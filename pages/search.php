<?php
    
    require_once(__DIR__ .  '/../database/chat.class.php');
    require_once(__DIR__ .  '/../database/hirings.class.php');
    require_once(__DIR__ .  '/../database/user.class.php');
    require_once(__DIR__ .  '/../database/custom_offer.class.php');

    require_once(__DIR__ .  '/../templates/common/header.tpl.php');
    require_once(__DIR__ .  '/../templates/category.tpl.php');
    require_once(__DIR__ .  '/../templates/service.tpl.php');
    require_once(__DIR__ .  '/../templates/search.tpl.php');
    require_once(__DIR__ .  '/../templates/chat.tpl.php');
    require_once(__DIR__ .  '/../templates/hirings.tpl.php');
    require_once(__DIR__ .  '/../templates/common/footer.tpl.php');
    require_once (__DIR__ . '/../templates/common/utils.tpl.php'); 

    $categories = Category::getAllWithSubcategories();
    $searchQuery = $_GET['q'] ?? '';
    $page = max(1, (int)($_GET['page'] ?? 1));
    $limit = 32;
    $offset = ($page - 1) * $limit;

    $services = Service::getServicesBySearch($searchQuery, $limit, $offset);
    $totalServices = Service::getTotalSearchCount($searchQuery);
    $totalPages = (int)ceil($totalServices / $limit);

    drawHeader();
    drawCategoryMenu($categories);
    drawSearchPage('search.php', 'Search services...', false, 'alt-style', $searchQuery, $categories, $services, $page, $totalPages);
    drawChat();
    drawHirings();
    drawFooter();
?>