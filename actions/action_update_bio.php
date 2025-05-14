<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/profiles.class.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    echo json_encode(['success' => false, 'message' => 'JSON invalid']);
    exit;
}
$bio = trim($data['bio'] ?? '');

$result = Profile::updateBio($bio);

if (!$result['success']) {
    http_response_code(500);
}

echo json_encode($result);