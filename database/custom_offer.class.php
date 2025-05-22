<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/database.php');

class CustomOffer {
    public int $id;
    public ?int $hiring_id;
    public int $service_id;
    public int $sender_id;
    public int $receiver_id;
    public float $price;
    public int $delivery_time;
    public int $number_of_revisions;
    public string $status;
    public string $created_at;
    public ?string $ended_at;

    public static function create(
        int $hiringId,
        int $senderId,
        int $receiverId,
        float $price,
        int $deliveryTime,
        int $revisions
    ): array {
        $db = Database::getInstance();

        $stmt = $db->prepare('
            INSERT INTO custom_offers (
                hiring_id, sender_id, receiver_id,
                price, delivery_time, number_of_revisions
            ) VALUES (?, ?, ?, ?, ?, ?)
        ');

        $success = $stmt->execute([
            $hiringId,
            $senderId,
            $receiverId,
            $price,
            $deliveryTime,
            $revisions
        ]);

        return ['success' => $success];
    }

    public static function getById(int $offerId): ?object {
        $db = Database::getInstance();

        $stmt = $db->prepare('SELECT * FROM custom_offers WHERE id = ?');
        $stmt->execute([$offerId]);
        $offer = $stmt->fetch(PDO::FETCH_OBJ);

        return $offer ?: null;
    }

    public static function getOffers(int $hiringId, int $userId1, int $userId2): array {
        $db = Database::getInstance();

        $stmt = $db->prepare('
            SELECT * FROM custom_offers
            WHERE hiring_id = ? AND
                (
                    (sender_id = ? AND receiver_id = ?) OR
                    (sender_id = ? AND receiver_id = ?)
                )
            ORDER BY created_at DESC
        ');
        $stmt->execute([$hiringId, $userId1, $userId2, $userId2, $userId1]);

        return $stmt->fetchAll(PDO::FETCH_CLASS, 'CustomOffer');
    }

    public static function updateStatus(int $offerId, string $newStatus): bool {
        $validStatuses = ['Pending', 'Accepted', 'Rejected', 'Cancelled'];

        $normalizedStatus = ucfirst(strtolower($newStatus));

        if (!in_array($normalizedStatus, $validStatuses, true)) {
            return false;
        }

        $db = Database::getInstance();

        $stmt = $db->prepare('UPDATE custom_offers SET status = ? WHERE id = ?');
        return $stmt->execute([$normalizedStatus, $offerId]);
    }
}
?>