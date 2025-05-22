<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../database/chat.class.php');

$session = Session::getInstance();
$user = $session->getUser();
$userId = $user->id;

header('Content-Type: application/json');

$unread = Chat::getUnreadMessagesCountByConversation($userId);
echo json_encode($unread);
