<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/user.class.php');

$session = Session::getInstance();
if (!$session->validateCSRFToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
    exit;
}

$email = $_POST['email'] ?? null;
$password = $_POST['password'] ?? null;
$newPassword = $_POST['newPassword'] ?? null;

$response = User::updateAuthentication($email, $password, $newPassword);

echo json_encode($response);
