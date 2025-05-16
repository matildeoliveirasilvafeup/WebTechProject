<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/database.php');
require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/chat.class.php');
require_once(__DIR__ . '/../database/user.class.php');

if (isset($_GET['conversation_id'], $_GET['service_id'], $_GET['user_id'])) {
    $conversationId = $_GET['conversation_id'];
    $serviceId = (int)$_GET['service_id'];
    $userId = (int)$_GET['user_id'];

    $messagesData = Chat::getMessages($conversationId, $serviceId, $userId);

    if ($messagesData) {
        echo json_encode([
            'messages' => $messagesData['messages'],
            'service_id' => $messagesData['service_id'],
            'receiver_id' => $messagesData['receiver_id'],
            'receiver_username' => $messagesData['receiver_username'],
            'receiver_pIcon' => $messagesData['receiver_pIcon']
        ]);
    } else {
        echo json_encode([
            'error' => 'No messages found for this conversation.'
        ]);
    }
} else {
    echo json_encode([
        'error' => 'Invalid parameters.'
    ]);
}
?>
