<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../database/user.class.php';
require_once __DIR__ . '/../database/service.class.php';

$session = Session::getInstance();
$user = $session->getUser();
if (!$session->validateCSRFToken($_POST['csrf_token'] ?? '')) {
    die('CSRF token validation failed');
}

if (!$user) {
    http_response_code(403);
    exit('User not authenticated.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $serviceId = (int)$_POST['id'];
    $serviceOwnerId = Service::getOwnerId($serviceId);

    if ($user->id !== $serviceOwnerId && $user->role !== 'admin') {
        http_response_code(403);
        exit('Unauthorized');
    }

    if (Service::deleteById($serviceId)) {
        http_response_code(200);
        header('Location: ../pages/dashboard.php#listings');
        exit('Service deleted successfully.');
    } else {
        http_response_code(500);
        exit('Failed to delete service.');
    }
} else {
    http_response_code(400);
    exit('Invalid request.');
}