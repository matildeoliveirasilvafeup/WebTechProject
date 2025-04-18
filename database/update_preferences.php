<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$db = new PDO('sqlite:sixerr.db');

$userId = $_SESSION['user']['id'];

$language = $_POST['language'] ?? null;
$proficiency = $_POST['proficiency'] ?? null;
$communication = $_POST['communication'] ?? null;
$preferredDaysTimesJson = $_POST['preferred_days_times'] ?? '{}';

$stmt = $db->prepare("
        INSERT INTO profiles_preferences (
            user_id, language, proficiency, communication, preferred_days_times
        ) VALUES (
            :user_id, :language, :proficiency, :communication, :preferences
        )
        ON CONFLICT(user_id) DO UPDATE SET
            language = excluded.language,
            proficiency = excluded.proficiency,
            communication = excluded.communication,
            preferred_days_times = excluded.preferred_days_times
");

$success = $stmt->execute([
    ':language' => $language,
    ':proficiency' => $proficiency,
    ':communication' => $communication,
    ':preferences' => $preferredDaysTimesJson,
    ':user_id' => $userId
]);

echo json_encode(['success' => $success]);
