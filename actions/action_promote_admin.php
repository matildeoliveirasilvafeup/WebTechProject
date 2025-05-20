<?php
    declare(strict_types=1);
    require_once(__DIR__ . '/../../includes/session.php');
    require_once(__DIR__ . '/../../database/user.class.php');

    if (!isset($_POST['user_id']) || !is_numeric($_POST['user_id'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid user ID."]);
        exit;
    }

    $userId = (int) $_POST['user_id'];

    $response = User::promoteToAdmin($userId);

    header('Content-Type: application/json');
    echo json_encode($response);
?>