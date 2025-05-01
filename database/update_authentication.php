<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "You need to be logged in."
    ]);
    exit;
}

$db = new PDO('sqlite:sixerr.db');
$userId = $_SESSION['user']['id'];
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$newPassword = trim($_POST['newPassword'] ?? '');

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {

    if (!password_verify($password, $_SESSION['user']['password_hash'])) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Wrong password."
        ]);
        exit;
    }
    
    $sql = "UPDATE users SET password_hash = ? WHERE id = ?";
    $stmt = $db->prepare($sql);
    
    $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    if ($stmt->execute([$newHashedPassword, $userId])) {
        $_SESSION['user']['password_hash'] = $newHashedPassword;
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Password updated successfully."
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Error updating password."
        ]);
    }
} else {
    
    $sql = "UPDATE users SET email = ? WHERE id = ?";
    $stmt = $db->prepare($sql);

    if ($stmt->execute([$email, $userId])) {
        $_SESSION['user']['email'] = $email;
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Email updated successfully."
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Error updating email."
        ]);
    }
}
?>
