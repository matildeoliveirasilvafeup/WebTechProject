<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/database.php');
require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/profile_preferences.class.php');
require_once(__DIR__ . '/../database/user.class.php');

header('Content-Type: application/json');

$language = $_POST['language'] ?? null;
$proficiency = $_POST['proficiency'] ?? null;
$communication = $_POST['communication'] ?? null;
$preferredDaysTimesJson = $_POST['preferred_days_times'] ?? '{}';

$result = ProfilePreferences::update($language, $proficiency, $communication, $preferredDaysTimesJson);

if (!$result['success']) {
    http_response_code(500);
}

echo json_encode($result);