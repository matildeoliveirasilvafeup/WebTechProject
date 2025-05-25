<?php
header('Content-Type: application/json');

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../database/favorites.class.php');
require_once(__DIR__ . '/../database/service.class.php');

$session = Session::getInstance();
if (!$session->validateCSRFToken($data['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
    exit;
}

$id = $_POST['id'] ?? null;
if (!$id || !is_numeric($id)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid ID."]);
    exit;
}

$action = $_POST['action'] ?? null;
if (!$action || !in_array($action, ['add', 'remove'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid action."]);
    exit;
}

switch ($action) {
    case 'add':
        $result = Favorite::addFavorite((int)$id);
        Service::increaseFavoriteCount((int)$id);
        break;
    case 'remove':
        $result = Favorite::removeFavorite((int)$id);
        Service::decreaseFavoriteCount((int)$id);
        break;
    default:
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid action."]);
        exit;
}

if (!$result['success']) {
    http_response_code(500);
}

echo json_encode($result);