<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/user.class.php');

$email = $_POST['email'] ?? null;
$password = $_POST['password'] ?? null;
$newPassword = $_POST['newPassword'] ?? null;

$response = User::updateAuthentication($email, $password, $newPassword);

echo json_encode($response);
