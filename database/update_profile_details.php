<?php
session_start();
$db = new PDO('sqlite:sixerr.db');

if (!isset($_SESSION['user']['id'])) {
    http_response_code(403);
    exit("Not authorized");
}

$userId = $_SESSION['user']['id'];
$name = trim($_POST['name'] ?? '');
$username = trim($_POST['username'] ?? '');
$location = trim($_POST['location'] ?? 'Portugal');

if (empty($name) || empty($username)) {
    http_response_code(400);
    exit("Invalid data");
}

$profilePicturePath = null;
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/profile_pictures/';
    $ext = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
    $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($ext, $allowedExts)) {
        exit("Invalid file type.");
    }

    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $filename = uniqid('profile_', true) . '.' . $ext;
    $destination = $uploadDir . $filename;

    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $destination)) {
        $profilePicturePath = 'uploads/profile_pictures/' . $filename;
    }
}

$sqlUser = "UPDATE users SET name = ?, username = ? WHERE id = ?";
$paramsUser = [$name, $username, $userId];
$stmtUser = $db->prepare($sqlUser);
$stmtUser->execute($paramsUser);

$checkProfile = $db->prepare("SELECT 1 FROM profiles WHERE user_id = ?");
$checkProfile->execute([$userId]);
if (!$checkProfile->fetch()) {
    $db->prepare("INSERT INTO profiles (user_id) VALUES (?)")->execute([$userId]);
}

$updates = [];
$paramsProfile = [];

if ($profilePicturePath !== null) {
    $updates[] = "profile_picture = ?";
    $paramsProfile[] = $profilePicturePath;
}
if (!empty($location)) {
    $updates[] = "location = ?";
    $paramsProfile[] = $location;
}

if (!empty($updates)) {
    $sqlProfile = "UPDATE profiles SET " . implode(', ', $updates) . " WHERE user_id = ?";
    $paramsProfile[] = $userId;
    $stmtProfile = $db->prepare($sqlProfile);
    $stmtProfile->execute($paramsProfile);
}

$_SESSION['user']['name'] = $name;
$_SESSION['user']['username'] = $username;
if ($profilePicturePath) {
    $_SESSION['user']['profile_picture'] = $profilePicturePath;
}

header("Location: ../dashboard.php");
exit;
