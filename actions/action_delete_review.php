<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../database/review.class.php';

$session = Session::getInstance();
$user = $session->getUser();
if (!$session->validateCSRFToken($_POST['csrf_token'] ?? '')) {
    die('CSRF token validation failed');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not authorized');
}

$reviewId = (int)($_POST['review_id'] ?? 0);

if (!$user || $reviewId <= 0) {
    http_response_code(403);
    exit('Unauthorized');
}

$db = Database::getInstance();
$stmt = $db->prepare("SELECT client_id FROM reviews WHERE id = :id");
$stmt->execute([':id' => $reviewId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    http_response_code(404);
    exit('Review not found');
}

if ($row['client_id'] != $user->id && !Session::isAdmin()) {
    http_response_code(403);
    exit('Unauthorized');
}

$success = Review::deleteReview($reviewId);

if ($success) {
    $redirect = $_SERVER['HTTP_REFERER'] ?? '../pages/dashboard.php';
    header('Location: ' . $redirect);
    exit;
} else {
    http_response_code(500);
    exit('Error deleting review');
}

?>