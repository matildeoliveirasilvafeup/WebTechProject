<?php
    declare(strict_types=1);
    require_once(__DIR__ . '/../includes/session.php');
    require_once(__DIR__ . '/../database/user.class.php');

    $session = Session::getInstance();
    if (!$session->validateCSRFToken($_POST['csrf_token'] ?? '')) {
        die('CSRF token validation failed');
    }

    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid user ID."]);
        exit;
    }

    $userId = (int) $_POST['id'];

    $response = User::promoteToAdmin($userId);

    header('Location: /index.php');
    echo json_encode($response);
?>