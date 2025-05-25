<?php
require_once('../includes/session.php');
require_once('../includes/database.php');
require_once('../database/chat.class.php');

$session = Session::getInstance();
if (!$session->validateCSRFToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token.']);
    exit;
}

$service_id = $_POST['service_id'] ?? null;
$user1_id = $_POST['user1_id'] ?? null;
$user2_id = $_POST['user2_id'] ?? null;

if (!$service_id || !$user1_id || !$user2_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit;
}

try {
    $conversation_id = Chat::createConversation((int)$service_id, (int)$user1_id, (int)$user2_id);

    echo json_encode([
        'success' => true,
        'conversation_id' => $conversation_id,
        'service_id' => $service_id,
        'receiver_id' => $user2_id === $user1_id ? $user1_id : $user2_id
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Server error']);
}
