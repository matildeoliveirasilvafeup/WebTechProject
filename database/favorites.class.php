<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/database.php');
require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/user.class.php');

class Favorite {

    public static function addFavorite(int $listingId): array {
        $session = Session::getInstance();
        $user = $session->getUser();
        $userId = $user->id ?? null;

        if (!$userId) {
            http_response_code(401);
            return ["success" => false, "message" => "Not authorized."];
        }

        $db = Database::getInstance();

        $stmt = $db->prepare('
            INSERT OR IGNORE INTO favorites (user_id, listing_id)
            VALUES (:user_id, :listing_id)
        ');

        $success = $stmt->execute([
            ':user_id' => $userId,
            ':listing_id' => $listingId
        ]);

        if ($success) {
            return ["success" => true, "message" => "Favorite added."];
        } else {
            return ["success" => false, "message" => "Failed to add favorite."];
        }
    }

    public static function removeFavorite(int $listingId): array {
        $session = Session::getInstance();
        $user = $session->getUser();
        $userId = $user->id ?? null;

        if (!$userId) {
            http_response_code(401);
            return ["success" => false, "message" => "Not authorized."];
        }

        $db = Database::getInstance();

        $stmt = $db->prepare('
            DELETE FROM favorites
            WHERE user_id = :user_id AND listing_id = :listing_id
        ');

        $success = $stmt->execute([
            ':user_id' => $userId,
            ':listing_id' => $listingId
        ]);

        if ($success) {
            return ["success" => true, "message" => "Favorite removed."];
        } else {
            return ["success" => false, "message" => "Failed to remove favorite."];
        }
    }


    public static function isFavorite(int $listingId): bool {
        $session = Session::getInstance();
        $user = $session->getUser();

        if (!$user) return false;

        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT 1 FROM favorites WHERE user_id = ? AND listing_id = ?');
        $stmt->execute([$user->id, $listingId]);

        return (bool) $stmt->fetchColumn();
    }

    public static function getByUserId(int $userId): array {
        $db = Database::getInstance();

        $stmt = $db->prepare('
            SELECT listing_id FROM favorites
            WHERE user_id = :user_id
        ');

        $stmt->execute([':user_id' => $userId]);
        $serviceIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $services = [];
        foreach ($serviceIds as $serviceId) {
            $service = Service::getById((int)$serviceId);
            if ($service) {
                $services[] = $service;
            }
        }

        return $services;
    }
}
