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

$offerId = isset($_POST['offer_id']) ? (int)$_POST['offer_id'] : null;
$newStatus = ucfirst(strtolower($_POST['new_status'] ?? ''));

$validStatuses = ['Pending', 'Accepted', 'Rejected', 'Cancelled'];

if (!in_array($newStatus, $validStatuses, true)) {
    http_response_code(400);
    exit('Invalid status update');
}

if ($newStatus === 'Cancelled') {
    if ($userId !== $offer->sender_id) {
        http_response_code(403);
        exit('Unauthorized');
    }
} elseif (in_array($newStatus, ['Accepted', 'Rejected'])) {
    if ($userId !== $offer->receiver_id) {
        http_response_code(403);
        exit('Unauthorized');
    }
} else {
    http_response_code(400);
    exit('Invalid status update');
}

$success = CustomOffer::updateStatus($offerId, $newStatus);

if (!$success) {
    http_response_code(500);
    exit('Failed to update status');
}

header('Location: ' . $_SERVER['HTTP_REFERER'] ?? '/');
exit;
