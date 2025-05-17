<?php
declare(strict_types=1);
require_once(__DIR__ . '/../includes/database.php');
require_once(__DIR__ . '/../database/user.class.php');

class Hire {
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

    public static function getById(int $id): ?Hire {
        $db = Database::getInstance();

        $stmt = $db->prepare("SELECT * FROM hires WHERE id = ?");
        $stmt->execute([$id]);

        $row = $stmt->fetch();
        return $row ? self::fromRow($row) : null;
    }

    public static function create(int $service_id, int $client_id, int $owner_id): Hire {
        $db = Database::getInstance();

        $stmt = $db->prepare("INSERT INTO hires (service_id, client_id, owner_id) VALUES (?, ?, ?)");
        $stmt->execute([$service_id, $client_id, $owner_id]);

        $id = (int)$db->lastInsertId();
        return self::getById($db, $id);
    }

    public static function getAllByUser(int $userId, string $position): array {
        $db = Database::getInstance();

        $stmt = $db->prepare("SELECT * FROM hires WHERE $position = ?  ORDER BY created_at DESC");
        $stmt->execute([$userId]);

        $hires = [];
        while ($row = $stmt->fetch()) {
            $hires[] = self::fromRow($row);
        }

        return $hires;
    }

    public function updateStatus(string $newStatus): void {
        $db = Database::getInstance();

        $validStatuses = ['Pending', 'Accepted', 'Rejected', 'Cancelled', 'Completed'];
        if (!in_array($newStatus, $validStatuses)) {
            throw new InvalidArgumentException("Invalid status: $newStatus");
        }

        $this->status = $newStatus;
        $this->ended_at = in_array($newStatus, ['Rejected', 'Cancelled', 'Completed']) ? date('Y-m-d H:i:s') : null;

        $stmt = $db->prepare("UPDATE hires SET status = ?, ended_at = ? WHERE id = ?");
        $stmt->execute([$this->status, $this->ended_at, $this->id]);
    }

    private static function fromRow(array $row): Hire {
        return new Hire(
            (int)$row['id'],
            (int)$row['service_id'],
            (int)$row['client_id'],
            (int)$row['owner_id'],
            $row['status'],
            $row['created_at'],
            $row['ended_at'] ?? null
        );
    }
}
