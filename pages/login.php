<?php
    require_once(__DIR__ . '/../includes/session.php');
    
    $session = Session::getInstance();
    $error = $session->getError() ?? '';

    require_once(__DIR__ .  '../templates/common/header.tpl.php');
    require_once(__DIR__ .  '../templates/common/footer.tpl.php'); 
    require_once(__DIR__ .  '../templates/auth.tpl.php');

    drawHeader();
    drawLogin($error);
    drawFooter();
?>
