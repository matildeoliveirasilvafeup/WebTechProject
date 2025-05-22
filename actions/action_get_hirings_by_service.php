<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/session.php';

require_once __DIR__ . '/../database/user.class.php';
require_once __DIR__ . '/../database/service.class.php';
require_once __DIR__ . '/../database/hirings.class.php';

$serviceId = intval($_GET['service_id'] ?? 0);
$userId1 = intval($_GET['user_id1'] ?? 0);
$userId2 = intval($_GET['user_id2'] ?? 0);

if (!$serviceId || !$userId1 || !$userId2) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid parameters']);
    exit;
}

$hirings = Hiring::getAllByService($serviceId, $userId1, $userId2);

$hiringsArray = array_map(function($hiring) {
    return [
        'id' => $hiring->id,
        'status' => $hiring->status,
        'createdAt' => $hiring->created_at
    ];
}, $hirings);

header('Content-Type: application/json');
echo json_encode($hiringsArray);
