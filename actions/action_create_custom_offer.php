<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../database/custom_offer.class.php');

$session = Session::getInstance();
$user = $session->getUser();

if (!$user) {
    header('Location: /login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php');
    exit;
}

$hiringId = isset($_POST['hiring_id']) ? (int)$_POST['hiring_id'] : null;
$senderId = isset($_POST['sender_id']) ? (int)$_POST['sender_id'] : null;
$receiverId = isset($_POST['receiver_id']) ? (int)$_POST['receiver_id'] : null;
$price = isset($_POST['price']) ? (float)$_POST['price'] : null;
$deliveryTime = isset($_POST['delivery']) ? (int)$_POST['delivery'] : null;
$revisions = isset($_POST['revisions']) ? (int)$_POST['revisions'] : null;

if (!isset($hiringId, $senderId, $receiverId, $price, $deliveryTime, $revisions)) {
    die("Missing required fields.");
}

$result = CustomOffer::create($hiringId, $senderId, $receiverId, $price, $deliveryTime, $revisions);

if (!$result['success']) {
    http_response_code(500);
    exit("Failed to create custom offer.");
}

header("Location: /pages/custom_offer.php?hiring_id=$hiringId&user_id1=$senderId&user_id2=$receiverId");
exit;
