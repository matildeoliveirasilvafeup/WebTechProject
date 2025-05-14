<?php
declare(strict_types=1);
require_once(__DIR__ . '/../includes/database.php');

class ProfilePreferences {
    public int $userId;
    public ?string $language;
    public ?string $proficiency;
    public ?string $communication;
    public array $preferredDaysTimes;

    public function __construct(array $data) {
        $this->userId = (int)$data['user_id'];
        $this->language = $data['language'] ?? '';
        $this->proficiency = $data['proficiency'] ?? '';
        $this->communication = $data['communication'] ?? '';
        $json = $data['preferred_days_times'] ?? '{}';
        $this->preferredDaysTimes = json_decode($json, true);
        if (!is_array($this->preferredDaysTimes)) {
            $this->preferredDaysTimes = [];
        }
    }

    public static function create(int $userId): void {
        $db = Database::getInstance();

        $stmt = $db->prepare('
            INSERT INTO profiles_preferences (user_id)
            VALUES (:user_id)
        ');
        $stmt->execute([':user_id' => $userId]);
    }

    public static function getByUserId(int $userId): ?ProfilePreferences {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM profiles_preferences WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $userId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new ProfilePreferences($data) : null;
    }

    public static function update(?string $language, ?string $proficiency, ?string $communication, string $preferredDaysTimesJson): array {
        $session = Session::getInstance();
        $user = $session->getUser();
        $userId = $user->id ?? null;

        if (!$userId) {
            http_response_code(401);
            return ["success" => false, "message" => "Unauthorized"];
        }

        $db = Database::getInstance();

        $decoded = json_decode($preferredDaysTimesJson, true);
        if (!is_array($decoded)) {
            return ["success" => false, "message" => "Invalid preferred_days_times format"];
        }

        $preferencesJson = json_encode($decoded);

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
            ':user_id' => $userId,
            ':language' => $language,
            ':proficiency' => $proficiency,
            ':communication' => $communication,
            ':preferences' => $preferencesJson,
        ]);

        return [
            "success" => $success,
            "message" => $success ? "Preferences updated." : "Failed to update preferences."
        ];
    }


}
