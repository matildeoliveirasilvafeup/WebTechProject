<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/database.php';

$db = Database::getInstance();

$response = [];

if (isset($_GET['email'])) {
    $email = $_GET['email'];
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['email'] = ['valid' => false, 'used' => false];
    } else {
        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $exists = $stmt->fetchColumn() > 0;

        $response['email'] = ['valid' => true, 'used' => $exists];
    }
}

if (isset($_GET['username'])) {
    $username = $_GET['username'];

    if (empty($username)) {
        $response['username'] = ['used' => false];
    } else {
        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $exists = $stmt->fetchColumn() > 0;

        $response['username'] = ['used' => $exists];
    }
}

echo json_encode($response);
