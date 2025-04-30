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

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Invalid or missing email."
    ]);
    exit;
}

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
?>
