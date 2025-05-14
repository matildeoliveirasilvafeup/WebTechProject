<?php
    require_once(__DIR__ . '/../includes/session.php');
    
    $session = Session::getInstance();
    $error = $session->getError() ?? '';

    require_once(__DIR__ .  '/../templates/common/header.tpl.php');
    require_once(__DIR__ .  '/../templates/common/footer.tpl.php'); 
    require_once(__DIR__ .  '/../templates/auth.tpl.php');

    drawAuthPageStart();
    drawHeader();
    drawLogin($error);
    drawFooter();
    drawAuthPageEnd();
?>
