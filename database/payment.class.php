<?php
declare(strict_types=1);
require_once(__DIR__ . '/../includes/database.php');

class Payment {
    public int $id;
    public int $service_id;
    public int $client_id;
    public int $freelancer_id;
    public string $method;
    public string $status;
    public string $created_at;

    public function __construct(
        int $id, int $service_id, int $client_id, int $freelancer_id,
        string $method, string $status, string $created_at
    ) {
        $this->id = $id;
        $this->service_id = $service_id;
        $this->client_id = $client_id;
        $this->freelancer_id = $freelancer_id;
        $this->method = $method;
        $this->status = $status;
        $this->created_at = $created_at;
    }

    public static function create(array $data): array {
        $db = Database::getInstance();

        $allowedMethods = ['paypal', 'credit_card', 'pix'];
        if (!in_array($data['payment_method'], $allowedMethods)) {
            return ["success" => false, "message" => "Invalid payment method."];
        }

        $stmt = $db->prepare("
            INSERT INTO payments (
                service_id, client_id, freelancer_id, method,
                billing_name, billing_email, billing_address, billing_city, billing_postal
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $success = $stmt->execute([
            $data['service_id'],
            $data['client_id'],
            $data['freelancer_id'],
            $data['payment_method'],
            $data['full_name'],
            $data['email'],
            $data['address'],
            $data['city'],
            $data['postal_code']
        ]);

        if ($success) {
            return ["success" => true, "message" => "Payment recorded."];
        } else {
            return ["success" => false, "message" => "Database error while saving payment."];
        }
    }

    public static function getById(int $id): ?Payment {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM payments WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? self::fromRow($row) : null;
    }

    public static function getAllByClient(int $clientId): array {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM payments WHERE client_id = ? ORDER BY created_at DESC");
        $stmt->execute([$clientId]);

        $payments = [];
        while ($row = $stmt->fetch()) {
            $payments[] = self::fromRow($row);
        }
        return $payments;
    }

    private static function fromRow(array $row): Payment {
        return new Payment(
            (int)$row['id'],
            (int)$row['service_id'],
            (int)$row['client_id'],
            (int)$row['freelancer_id'],
            $row['method'],
            $row['status'],
            $row['created_at']
        );
    }
}
