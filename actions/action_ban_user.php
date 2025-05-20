<?php
    declare(strict_types=1);
    require_once(__DIR__ . '/../includes/session.php');
    require_once(__DIR__ . '/../includes/database.php');
    require_once(__DIR__ . '/../database/user.class.php');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    $username = $_POST['username'] ?? '';
    $reason = $_POST['reason'] ?? 'No reason provided';

    if (empty($username)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Username is required']);
        exit;
    }

    $response = User::banUser($username, $reason);

    header('Location: /index.php');
    echo json_encode($response);
?>