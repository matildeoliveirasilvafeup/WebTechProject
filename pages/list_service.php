<?php
    require_once (__DIR__ . '/../includes/session.php');
    require_once (__DIR__ . '/../database/chat.class.php');

    require_once (__DIR__ . '/../templates/common/header.tpl.php');
    require_once (__DIR__ . '/../templates/category.tpl.php');
    require_once (__DIR__ . '/../templates/service.tpl.php');
    require_once (__DIR__ . '/../templates/chat.tpl.php');
    require_once (__DIR__ . '/../templates/common/footer.tpl.php'); 
    require_once (__DIR__ . '/../templates/common/utils.tpl.php');
    
    $session = Session::getInstance();

    if (!$session || !$session->getUser()) {
        header('Location: login.php');
        exit;
    }

    $serviceId = (int)($_GET['id'] ?? 0);
    $service = Service::getById($serviceId);

    $categories_sub = Category::getAllWithSubcategories();

    drawHeader();
    drawCategoryMenu($categories_sub);
    drawListServicesForm($categories_sub, $service);
    drawChat();
    drawFooter();
?>    


    