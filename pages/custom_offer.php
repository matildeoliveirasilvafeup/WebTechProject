<?php
    require_once (__DIR__ . '/../includes/session.php');

    require_once (__DIR__ . '/../database/user.class.php');
    require_once (__DIR__ . '/../database/custom_offer.class.php');

    require_once (__DIR__ . '/../templates/custom_offer.tpl.php');
    require_once (__DIR__ . '/../templates/common/header.tpl.php');
    require_once (__DIR__ . '/../templates/common/footer.tpl.php');

    $session = Session::getInstance();

    if (!$session || !$session->getUser()) {
        header('Location: login.php');
        exit;
    }

    $hiringId = $_GET['hiring_id'] ?? null;
    $serviceId = $_GET['service_id'] ?? null;
    $userId1 = $_GET['user_id1'] ?? null;
    $userId2 = $_GET['user_id2'] ?? null;
    
    if (!$hiringId || !$userId1 || !$userId2 || !$serviceId) {
        header("Location: index.php");
        exit;
    }

    drawCustomOfferPageStart();
    drawHeader();
    drawCustomOfferForm($hiringId, $userId1, $userId2, $serviceId);
    drawFooter();
    drawCustomOfferPageEnd();
?>