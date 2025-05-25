<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/chat.class.php');
require_once(__DIR__ . '/../database/user.class.php');

$session = Session::getInstance();
if (!$session->validateCSRFToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    exit;
}
$user = $session->getUser();
$userId = $user->id;

$conversationId = $_POST['conversation_id'] ?? null;
$serviceId = $_POST['service_id'] ?? null;

if (!$conversationId || !$serviceId || !is_numeric($serviceId)) {
    http_response_code(400);
    exit;
}

Chat::markConversationAsRead($conversationId, $userId, (int)$serviceId);
