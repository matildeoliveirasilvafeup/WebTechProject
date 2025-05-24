<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../database/custom_offer.class.php');

$session = Session::getInstance();
$user = $session->getUser();

if (!$user || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$hiringId = (int)($_POST['hiring_id'] ?? 0);
$serviceId = (int)($_POST['service_id'] ?? 0);
$senderId = (int)($_POST['sender_id'] ?? 0);
$receiverId = (int)($_POST['receiver_id'] ?? 0);
$price = (float)($_POST['price'] ?? -1);
$deliveryTime = (int)($_POST['delivery'] ?? 0);
$revisions = (int)($_POST['revisions'] ?? -1);

if (!$hiringId || !$senderId || !$receiverId || $price < 0 || $deliveryTime < 1 || $revisions < 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing or invalid fields.']);
    exit;
}

$result = CustomOffer::create($hiringId, $senderId, $receiverId, $price, $deliveryTime, $revisions);

if (!$result['success']) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to create custom offer.']);
    exit;
}

echo json_encode([
    'success' => true,
    'id' => $result['id'],
    'message' => 'Custom offer created successfully.'
]);
exit;
