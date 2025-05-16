<?php
    require_once __DIR__ . '/../includes/session.php';
    
    $session = Session::getInstance();

    if (!$session || !$session->getUser()) {
        header('Location: login.php');
        exit;
    }

    require '../templates/common/header.tpl.php';
    require '../templates/category.tpl.php';
    require '../templates/service.tpl.php';
    require '../templates/common/footer.tpl.php'; 

    $categories_sub = Category::getAllWithSubcategories();

    drawHeader();
    drawCategoryMenu($categories_sub);
    drawListServicesForm($categories_sub);
    drawFooter();
?>    


    