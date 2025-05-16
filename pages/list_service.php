<?php
    require_once __DIR__ . '/../includes/session.php';
    require '../templates/common/header.tpl.php';
    require '../templates/category.tpl.php';
    require '../templates/service.tpl.php';
    require '../templates/common/footer.tpl.php'; 
    
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
    drawFooter();
?>    


    