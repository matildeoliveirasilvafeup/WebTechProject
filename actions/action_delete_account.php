<?php
header('Content-Type: application/json');

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/user.class.php');

$reason = trim($_POST['reason'] ?? '');

$result = User::deleteAccount($reason);

if (!$result['success']) {
    http_response_code(500);
}

echo json_encode($result);