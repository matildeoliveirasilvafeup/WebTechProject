<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/database.php';

class Review {
    public int $id;
    public int $serviceId;
    public int $clientId;
    public int $rating;
    public string $comment;
    public string $createdAt;
    public string $clientName;
    public string $serviceTitle;

    public function __construct(array $data) {
        $this->id = (int)$data['id'] ?? 0;
        $this->serviceId = (int)$data['service_id'] ?? 0;
        $this->clientId = (int)$data['client_id'] ?? 0;
        $this->rating = (int)$data['rating'] ?? 0;
        $this->comment = $data['comment'] ?? '';
        $this->createdAt = $data['created_at'] ?? date('Y-m-d H:i:s');
        $this->clientName = $data['client_name'] ?? '';
        $this->serviceTitle = $data['service_title'] ?? '';
    }
    
    public static function getLatestReviews(int $limit = 3): array {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT 
                reviews.*,
                users.name AS client_name,
                services.title AS service_title
            FROM reviews
            JOIN users ON reviews.client_id = users.id
            JOIN services ON reviews.service_id = services.id
            ORDER BY reviews.created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(fn($row) => new Review($row), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public static function getServiceRatingInfo(int $service_id): array {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT 
                ROUND(AVG(rating), 1) AS avg_rating,
                COUNT(*) AS total_reviews
            FROM reviews
            WHERE service_id = :service_id
        ");
        $stmt->execute([':service_id' => $service_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'avg' => $result['avg_rating'] ?? null,
            'count' => $result['total_reviews'] ?? 0
        ];
    }

    public static function getServiceReviews(int $service_id): array {
        $db = Database::getInstance();
        $reviewsStmt = $db->prepare("
            SELECT r.*, u.name AS client_name, p.profile_picture
            FROM reviews r
            JOIN users u ON u.id = r.client_id
            LEFT JOIN profiles p ON p.user_id = u.id
            WHERE r.service_id = ?
            ORDER BY r.created_at DESC
        ");
        $reviewsStmt->execute([$service_id]);
        $rows = $reviewsStmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => new Review($row), $rows);
    }
}

?>