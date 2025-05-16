<?php
declare(strict_types=1);
require_once(__DIR__ . '/../includes/database.php');

class Profile {
    public int $userId;
    public ?string $bio;
    public ?string $location;
    public ?string $profilePicture;

    public function __construct(array $data) {
        $this->userId = (int)$data['user_id'];
        $this->bio = $data['bio'] ?? null;
        $this->location = $data['location'] ?? null;
        $this->profilePicture = $data['profile_picture'] ?? null;
        $this->proficiency = $data['proficiency'] ?? null;
        $this->communication = $data['communication'] ?? null;
    }

    public static function create(int $userId): void {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO profiles (user_id) VALUES (:user_id)");
        $stmt->execute([':user_id' => $userId]);
    }

    public static function getByUserId(int $userId): ?Profile {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM profiles WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $userId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new Profile($data) : null;
    }

    public static function ensureExists(int $userId): void {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT 1 FROM profiles WHERE user_id = ?");
        $stmt->execute([$userId]);
        if (!$stmt->fetch()) {
            $db->prepare("INSERT INTO profiles (user_id) VALUES (?)")->execute([$userId]);
        }
    }

    public static function updateBio(string $bio): array {
        if (empty(trim($bio))) {
            http_response_code(400);
            return ["success" => false, "message" => "Bio cannot be empty."];
        }

        $session = Session::getInstance();
        $user = $session->getUser();
        $userId = $user->id ?? null;
        if (!$userId) {
            http_response_code(401);
            return ["success" => false, "message" => "Not authorized."];
        }

        self::ensureExists($userId);
        $db = Database::getInstance();

        $sql = "UPDATE profiles SET bio = ? WHERE user_id = ?";
        $stmt = $db->prepare($sql);
        
        if ($stmt->execute([$bio, $userId])) {
            return ["success" => true, "message" => "Bio updated successfully."];
        } else {
            http_response_code(500);
            return ["success" => false, "message" => "Error updating bio."];
        }
    }

    public static function updateLocationAndIcon(string $location, ?array $file = null): array {
        $session = Session::getInstance();
        $user = $session->getUser();
        $userId = $user->id ?? null;

        if (!$userId) {
            http_response_code(401);
            return ["success" => false, "message" => "Not authorized."];
        }

        self::ensureExists($userId);

        $db = Database::getInstance();
        $updates = [];
        $params = [];

        $updates[] = "location = ?";
        $params[] = $location;
        $sql = "UPDATE profiles SET location = ? WHERE user_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$location, $userId]);

        $profile = self::getByUserId($userId);

        
        $profilePicturePath = null;

        if ($file && isset($file['error']) && $file['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/profile_pictures/';
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
            self::deleteProfileIcon($userId);
        
            $uploaded = uploadFiles(['name' => [$file['name']],
                                     'type' => [$file['type']],
                                     'tmp_name' => [$file['tmp_name']],
                                     'error' => [$file['error']],
                                     'size' => [$file['size']]],
                                     $uploadDir, $allowedTypes);
        
            if (empty($uploaded)) {
                http_response_code(500);
                error_log("Failed uploading profile image.");
                return ["success" => false, "message" => "Failed loading profile image."];
            }
        
            $profilePicturePath = str_replace(__DIR__ . '/../', '/', $uploaded[0]);
            $updates[] = "profile_picture = ?";
            $params[] = $profilePicturePath;
        }

        if (!empty($updates)) {
            $sql = "UPDATE profiles SET " . implode(", ", $updates) . " WHERE user_id = ?";
            $params[] = $userId;
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
        }

        return [
            "success" => true,
            "message" => "Profile updated successfully.",
        ];
    }

    public static function deleteProfileIcon(int $userId): void {

        self::ensureExists($userId);

        $profile = self::getByUserId($userId);

        if (isset($profile->profilePicture) && !empty($profile->profilePicture)) {
            $oldFilePath = __DIR__ . '/..' . $profile->profilePicture;
            
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
                error_log("Old profile picture deleted: $oldFilePath");
            } else {
                error_log("No old profile picture found.");
            }
        }
    }

}
