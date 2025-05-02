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

require_once 'connection.php';
$userId = $_SESSION['user']['id'];
$reason = trim($_POST['email'] ?? '');

$sql = "INSERT INTO deleted_users (name, username, email, password_hash, role, created_at, deleted_at, reason) VALUES
($_SESSION[user]['name'], $_SESSION[user]['username'], $_SESSION[user]['email'], $_SESSION[user]['password_hash'], $_SESSION[user]['role'], NOW(), ?)";
$stmt = $db->prepare($sql);
$stmt->execute([$reason]);

if ($stmt->execute([$reason])) {
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
?>