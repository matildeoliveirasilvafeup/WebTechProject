<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../database/custom_offer.class.php');

header('Content-Type: application/json');

$session = Session::getInstance();
if (!$session->validateCSRFToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

$hiringId = isset($_POST['hiring_id']) ? (int)$_POST['hiring_id'] : 0;

if ($hiringId <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid hiring ID.']);
    exit;
}

try {
    $status = CustomOffer::checkHiringOffersStatus($hiringId);
    echo json_encode(['success' => true, 'status' => $status]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error checking status.']);
}
