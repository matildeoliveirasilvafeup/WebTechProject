<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');

$session = Session::getInstance();
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$session->validateCSRFToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    header('Location: /');
    exit;
}

$session->logout();

header('Location: /');
exit;
?>