<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../database/review.class.php';

$session = Session::getInstance();
$user = $session->getUser();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}

if (!$user) {
    header('Location: login.php');
    exit;
}

var_dump($_POST);

$clientId = (int)$user->id;
$serviceId = (int)($_POST['service_id'] ?? 0);
$hiringId = (int)($_POST['hiring_id'] ?? 0);
$rating = (int)($_POST['rating'] ?? 0);
$comment = trim($_POST['comment'] ?? '');

if ($serviceId <= 0 || $hiringId <= 0 || $rating < 1 || $rating > 5 || $comment === '') {
    http_response_code(400);
    exit('Invalid Data.');
}

$success = Review::addReview($clientId, $serviceId, $hiringId, $rating, $comment);

if ($success) {
    header('Location: ../pages/service.php?id=' . $serviceId);
    exit;
} else {
    http_response_code(500);
    exit('Error submitting review.');
}
?>