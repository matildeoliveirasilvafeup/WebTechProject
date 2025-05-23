<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../database/hirings.class.php');

$hiringId = (int)trim($_POST['id'] ?? '');
$status = trim($_POST['status'] ?? '');

if (!$hiringId || empty($status)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid data."]);
    exit;
}

try {
    $result = Hiring::updateStatus($hiringId, $status);
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
