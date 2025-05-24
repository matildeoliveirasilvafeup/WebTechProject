<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../database/hirings.class.php');

header('Content-Type: application/json');

$hiringId = (int)trim($_POST['id'] ?? '');
$newStatus = ucfirst(strtolower(trim($_POST['status'] ?? '')));

if (!$hiringId || empty($newStatus)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid data."]);
    exit;
}

try {
    $hiring = Hiring::getById($hiringId);
    if (!$hiring) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Hiring not found."]);
        exit;
    }

    $currentStatus = ucfirst(strtolower($hiring->status ?? ''));

    if (
        ($currentStatus === 'Cancelled' && in_array($newStatus, ['Accepted', 'Rejected', 'Completed'])) ||
        ($currentStatus === 'Rejected' && $newStatus === 'Cancelled') ||
        ($currentStatus === 'Completed' && $newStatus === 'Cancelled')
    ) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid status transition from $currentStatus to $newStatus."]);
        exit;
    }

    $result = Hiring::updateStatus($hiringId, $newStatus);

    if (!$result['success']) {
        http_response_code(500);
    }

    echo json_encode($result);

} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Internal error: " . $e->getMessage()]);
}
