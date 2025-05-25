<?php
header('Content-Type: application/json');

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../database/hirings.class.php');

$session = Session::getInstance();
$data = json_decode(file_get_contents('php://input'), true);
if (!$session->validateCSRFToken($data['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
    exit;
}

if (!$data || !isset($data['serviceId'], $data['client_id'], $data['owner_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid or missing input data.']);
    exit;
}

$serviceId = (int)$data['serviceId'];
$client_id = (int)$data['client_id'];
$owner_id = (int)$data['owner_id'];

$result = Hiring::create($serviceId, $client_id, $owner_id);

if (!$result['success']) {
    http_response_code(500);
}

echo json_encode($result);
