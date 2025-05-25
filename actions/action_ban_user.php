<?php
    declare(strict_types=1);
    require_once(__DIR__ . '/../includes/session.php');
    require_once(__DIR__ . '/../includes/database.php');
    require_once(__DIR__ . '/../database/user.class.php');

    $session = Session::getInstance();
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    if (!$session->validateCSRFToken($_POST['csrf_token'] ?? '')) {
        die('CSRF token validation failed');
    }

    $username = $_POST['username'] ?? '';

    if (empty($username)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Username is required']);
        exit;
    }

    $response = User::banUser($username);

    header('Location: /index.php');
    echo json_encode($response);
?>