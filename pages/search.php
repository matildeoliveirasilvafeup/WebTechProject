<?php
    require_once(__DIR__ .  '/../templates/common/header.tpl.php');
    require_once(__DIR__ .  '/../templates/category.tpl.php');

    $categories = Category::getAllWithSubcategories();

    drawHeader();
    drawCategoryMenu($categories); 


    drawFooter();
?>