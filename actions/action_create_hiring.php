<?php
header('Content-Type: application/json');

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/user.class.php');

$data = json_decode(file_get_contents('php://input'), true);

$serviceId = $data['serviceId'];
$client_id = $data['client_id'];
$owner_id = $data['owner_id'];

$result = Hiring::create($serviceId, $client_id, $owner_id);

if (!$result['success']) {
    http_response_code(500);
}

echo json_encode($result);