<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/database.php');
require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/chat.class.php');
require_once(__DIR__ . '/../database/user.class.php');

header('Content-Type: application/json');

$conversationId = $_POST['conversation_id'] ?? null;
$serviceId = (int)$_POST['service_id'] ?? null;
$senderId = (int)$_POST['sender_id'] ?? null;
$receiverId = (int)$_POST['receiver_id'] ?? null;
$message = $_POST['message'] ?? null;

if (!$conversationId || !$serviceId || !$senderId || !$receiverId || !$message) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing fields']);
    exit;
}

$result = Chat::sendMessage($conversationId, $serviceId, $senderId, $receiverId, $message);

if (!$result['success']) {
    http_response_code(500);
}

echo json_encode($result);