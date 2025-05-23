<?php
    declare(strict_types=1);

    require_once(__DIR__ . '/../includes/session.php');

    require_once(__DIR__ . '/../database/category.class.php');
    require_once(__DIR__ . '/../database/chat.class.php');
    require_once(__DIR__ . '/../database/hirings.class.php');
    require_once(__DIR__ . '/../database/service.class.php');

    require_once(__DIR__ . '/../templates/category.tpl.php');
    require_once(__DIR__ . '/../templates/chat.tpl.php');
    require_once(__DIR__ . '/../templates/hirings.tpl.php');
    require_once(__DIR__ . '/../templates/common/header.tpl.php');
    require_once(__DIR__ . '/../templates/common/footer.tpl.php');

    $session = Session::getInstance();
    $categoriesMenu = Category::getAllWithSubcategories();

    if (!Session::isAdmin()) {
        header('Location: home_page.php');
        exit;
    }   

    drawHeader();
    drawCategoryMenu($categoriesMenu);
    drawCategoryForm();
    drawChat();
    drawHirings();
    drawFooter();
?>