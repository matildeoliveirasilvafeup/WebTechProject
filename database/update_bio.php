<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) {
    echo "You need to be logged in.";
    exit;
}

$db = new PDO('sqlite:sixerr.db');

$userId = $_SESSION['user']['id'];
$bio = trim($_POST['bio'] ?? '');

if (empty($bio)) {
    http_response_code(400);
    exit("Invalid data");
}

$checkProfile = $db->prepare("SELECT 1 FROM profiles WHERE user_id = ?");
$checkProfile->execute([$userId]);
if (!$checkProfile->fetch()) {
    $db->prepare("INSERT INTO profiles (user_id) VALUES (?)")->execute([$userId]);
}

$sql = "UPDATE profiles SET bio = ? WHERE user_id = ?";
$stmt = $db->prepare($sql);
if ($stmt->execute([$bio, $userId])) {
    http_response_code(200);
    echo json_encode(["success" => true, "message" => "Bio updated successfully."]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error updating bio."]);
}
?>
