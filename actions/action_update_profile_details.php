<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../includes/database.php');
require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../database/profiles.class.php');

header('Content-Type: application/json');

try {

    $name = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $location = trim($_POST['location'] ?? 'Portugal');
    $file = $_FILES['profile_picture'] ?? null;

    error_log("Name: $name, Username: $username, Location: $location, File: " . json_encode($file));

    // Update name + username via User class
    $response = User::updateNameAndUsername($name, $username);
    if (!$response['success']) {
        http_response_code(409);
        echo json_encode($response);
        exit;
    }

    // Update location + profile icon via Profile class
    $response = Profile::updateLocationAndIcon($location, $file);
    if (!$response['success']) {
        http_response_code(409);
        echo json_encode($response);
        exit;
    }

    echo json_encode([
        "success" => true,
        "message" => "Profile updated successfully.",
    ]);
} catch (Exception $e) {
    http_response_code(500);
    error_log("Error updating profile: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Internal server error."]);
}
