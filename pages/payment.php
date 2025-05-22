<?php
    require_once(__DIR__ . '/../includes/session.php');
    require_once(__DIR__ . '/../includes/database.php');

    require_once(__DIR__ . '/../database/user.class.php');
    require_once(__DIR__ . '/../database/service.class.php');
    require_once(__DIR__ . '/../database/payment.class.php');

    require_once(__DIR__ .  '/../templates/common/header.tpl.php');
    require_once(__DIR__ .  '/../templates/common/footer.tpl.php'); 
    require_once(__DIR__ .  '/../templates/payment.tpl.php');

    $serviceId = (int)$_GET['service_id'] ?? null;
    $clientId = (int)$_GET['client_id'] ?? null;
    $freelancerId = (int)$_GET['freelancer_id'] ?? null;

    $service = Service::getById($serviceId);
    $client = User::getById($clientId);
    $freelancer = User::getById($freelancerId);

    if (!$service || !$client || !$freelancer) {
        header("Location: /pages/login.php");
    }

    drawPaymentPageStart();
    drawHeader();
    drawPaymentForm($service, $client, $freelancer);
    drawFooter();
    drawPaymentPageEnd();
?>