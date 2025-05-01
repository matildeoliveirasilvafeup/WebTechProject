<?php
    require_once __DIR__ . '/../includes/session.php';
    
    $session = Session::getInstance();
    $error = $session->getError() ?? '';

    require '../templates/common/header.tpl.php';
    require '../templates/common/footer.tpl.php'; 
    require '../templates/auth.tpl.php';

    drawHeader();
    drawRegister($error);
    drawFooter();
?>



