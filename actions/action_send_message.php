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
$file = $_FILES['file'] ?? null;

if (!$conversationId || !$serviceId || !$senderId || !$receiverId || !($message || $file)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing fields']);
    exit;
}

$uploadDir = __DIR__ . '/../uploads/chat/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

$fileName = null;

if (!empty($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $fileTmp = $_FILES['file']['tmp_name'];
    $originalName = basename($_FILES['file']['name']);
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $safeName = uniqid('file_', true) . '.' . $ext;

    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'docx', 'txt'];

    if (in_array($ext, $allowed)) {
        if (move_uploaded_file($fileTmp, $uploadDir . $safeName)) {
            $fileName = $safeName;
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'File upload failed']);
            exit;
        }
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid file type']);
        exit;
    }
}

$result = Chat::sendMessage($conversationId, $serviceId, $senderId, $receiverId, $message, $fileName);

if (!$result['success']) {
    http_response_code(500);
}

echo json_encode($result);