<?php
declare(strict_types=1);
require_once(__DIR__ . '/../includes/database.php');
require_once(__DIR__ . '/../database/user.class.php');

class Hiring {
    public int $id;
    public int $service_id;
    public int $client_id;
    public int $owner_id;
    public string $status;
    public string $created_at;
    public ?string $ended_at;

    public function __construct(int $id, int $service_id, int $client_id, int $owner_id, string $status, string $created_at, ?string $ended_at) {
        $this->id = $id;
        $this->service_id = $service_id;
        $this->client_id = $client_id;
        $this->owner_id = $owner_id;
        $this->status = $status;
        $this->created_at = $created_at;
        $this->ended_at = $ended_at;
    }

    public static function getById(int $id): ?Hiring {
        $db = Database::getInstance();

        $stmt = $db->prepare("SELECT * FROM hirings WHERE id = ?");
        $stmt->execute([$id]);

        $row = $stmt->fetch();
        return $row ? self::fromRow($row) : null;
    }

    public static function create(int $service_id, int $client_id, int $owner_id): array {
        $db = Database::getInstance();

        $stmt = $db->prepare("INSERT INTO hirings (service_id, client_id, owner_id) VALUES (?, ?, ?)");
        $success = $stmt->execute([$service_id, $client_id, $owner_id]);
        
        if ($success) {
            return ["success" => true, "message" => "Hiring created successfully."];
        } else {
            return ["success" => false, "message" => "Error creating hiring."];
        }
    }

    public static function getAllByService(int $serviceId, int $userId1, int $userId2): array {
        $db = Database::getInstance();

        $stmt = $db->prepare("
            SELECT * FROM hirings 
            WHERE service_id = ?
            AND (
                (client_id = ? AND owner_id = ?)
                OR
                (client_id = ? AND owner_id = ?)
            )
            ORDER BY created_at DESC
        ");
        $stmt->execute([$serviceId, $userId1, $userId2, $userId2, $userId1]);

        $hirings = [];
        while ($row = $stmt->fetch()) {
            $hirings[] = self::fromRow($row);
        }

        return $hirings;
    }

    public static function getAllByUser(int $userId, string $position): array {
        $db = Database::getInstance();

        $stmt = $db->prepare("SELECT * FROM hirings WHERE $position = ?  ORDER BY created_at DESC");
        $stmt->execute([$userId]);

        $hirings = [];
        while ($row = $stmt->fetch()) {
            $hirings[] = self::fromRow($row);
        }

        return $hirings;
    }

    public static function updateStatus(int $id, string $newStatus): array {
        $db = Database::getInstance();

        $validStatuses = ['Pending', 'Accepted', 'Rejected', 'Cancelled', 'Completed', 'Closed', 'Disabled'];
        if (!in_array($newStatus, $validStatuses)) {
            throw new InvalidArgumentException("Invalid status: $newStatus");
        }

        $ended_at = in_array($newStatus, ['Rejected', 'Cancelled', 'Closed']) ? date('Y-m-d H:i:s') : null;

        $stmt = $db->prepare("UPDATE hirings SET status = ?, ended_at = ? WHERE id = ?");
        $success = $stmt->execute([$newStatus, $ended_at, $id]);

        if ($success) {
            return ["success" => true, "message" => "Status updated successfully."];
        } else {
            return ["success" => false, "message" => "Error updating Status."];
        }
    }

    private static function fromRow(array $row): Hiring {
        return new Hiring(
            (int)$row['id'],
            (int)$row['service_id'],
            (int)$row['client_id'],
            (int)$row['owner_id'],
            $row['status'],
            $row['created_at'],
            $row['ended_at'] ?? null
        );
    }

    public function getStatus(): string {
        return $this->status;
    }
}
