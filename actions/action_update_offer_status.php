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

$session = Session::getInstance();
$user = $session->getUser();

if (!$user) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$offerId = isset($_POST['offer_id']) ? (int)$_POST['offer_id'] : null;
$hiringId = isset($_POST['hiring_id']) ? (int)$_POST['hiring_id'] : null;
$newStatus = ucfirst(strtolower($_POST['new_status'] ?? ''));

$validStatuses = ['Pending', 'Accepted', 'Rejected', 'Cancelled'];
if (!$offerId || !$hiringId || !in_array($newStatus, $validStatuses, true)) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}

$currentOffer = CustomOffer::getById($offerId);
if (!$currentOffer) {
    echo json_encode(['success' => false, 'error' => 'Offer not found']);
    exit;
}

$currentStatus = ucfirst(strtolower($currentOffer->status ?? ''));

if ($currentStatus === 'Cancelled' && in_array($newStatus, ['Accepted', 'Rejected'])) {
    echo json_encode(['success' => false, 'error' => 'Offer has been cancelled']);
    exit;
}

if ($currentStatus === 'Rejected' && $newStatus === 'Cancelled') {
    echo json_encode(['success' => false, 'error' => "Offer already {$currentStatus}"]);
    exit;
}

$success = CustomOffer::updateStatus($offerId, $hiringId, $newStatus);

if (!$success) {
    echo json_encode(['success' => false, 'error' => 'Failed to update status']);
    exit;
}

echo json_encode(['success' => true, 'message' => "Offer successfully {$newStatus}"]);
exit;
