<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/database.php';

class Service {
    public int $id;
    public string $title;
    public string $description;
    public float $price;
    public string $freelancerName;
    public ?string $profilePicture;
    public ?string $mediaUrl;
    public ?string $categoryName;

    public function __construct(array $data) {
        $this->id = (int)$data['id'];
        $this->title = $data['title'];
        $this->description = $data['description'];
        $this->price = (float)$data['price'];
        $this->freelancerName = $data['freelancer_name'];
        $this->profilePicture = $data['profile_picture'] ?? null;
        $this->mediaUrl = $data['media_url'] ?? null;
        $this->categoryName = $data['category_name'] ?? null;
    }

    public static function getAll(): array {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT services.*, users.username AS freelancer_name, profiles.profile_picture
            FROM services
            JOIN users ON services.freelancer_id = users.id
            JOIN profiles ON users.id = profiles.user_id
            ORDER BY services.created_at DESC
        ");
        $stmt->execute();

        return array_map(fn($row) => new Service($row), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public static function getFeatured(int $limit = 6): array {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT 
                services.id,
                services.title,
                services.description,
                services.price,
                users.name AS freelancer_name,
                (
                    SELECT media_url 
                    FROM service_images 
                    WHERE service_id = services.id 
                    ORDER BY id ASC LIMIT 1
                ) AS media_url
            FROM services
            JOIN users ON services.freelancer_id = users.id
            ORDER BY services.created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(fn($row) => new Service($row), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public static function getById(int $id): ?Service {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT 
                services.*,
                users.name AS freelancer_name,
                profiles.profile_picture,
                categories.name AS category_name,
                (
                    SELECT media_url 
                    FROM service_images 
                    WHERE service_id = services.id 
                    ORDER BY id ASC LIMIT 1
                ) AS media_url
            FROM services
            JOIN users ON services.freelancer_id = users.id
            LEFT JOIN profiles ON users.id = profiles.user_id
            LEFT JOIN categories ON services.category_id = categories.id
            WHERE services.id = :id
            LIMIT 1
        ");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? new Service($data) : null;
    }

    public static function getMoreFromFreelancer(int $freelancerId, int $excludeId, int $limit = 4): array {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT s.*, si.media_url, u.name AS freelancer_name 
            FROM services s
            JOIN users u ON s.freelancer_id = u.id
            LEFT JOIN service_images si ON si.service_id = s.id
            WHERE s.freelancer_id = :freelancer_id AND s.id != :exclude_id
            GROUP BY s.id
            LIMIT :limit
        ");
        $stmt->bindValue(':freelancer_id', $freelancerId, PDO::PARAM_INT);
        $stmt->bindValue(':exclude_id', $excludeId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(fn($row) => new Service($row), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public static function getRelated(int $categoryId, int $excludeId, int $limit = 4): array {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT s.*, si.media_url, u.name AS freelancer_name 
            FROM services s
            JOIN users u ON s.freelancer_id = u.id
            LEFT JOIN service_images si ON si.service_id = s.id
            WHERE s.category_id = :category_id AND s.id != :exclude_id
            GROUP BY s.id
            LIMIT :limit
        ");
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':exclude_id', $excludeId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(fn($row) => new Service($row), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public static function getServicesBySearch(string $search, int $limit = 10): array {
        $db = Database::getInstance();
    
        $stmt = $db->prepare("
            SELECT 
                services.*,
                users.name AS freelancer_name,
                profiles.profile_picture,
                (
                    SELECT media_url 
                    FROM service_images 
                    WHERE service_id = services.id 
                    ORDER BY id ASC LIMIT 1
                ) AS media_url
            FROM services
            JOIN users ON services.freelancer_id = users.id
            LEFT JOIN profiles ON users.id = profiles.user_id
            WHERE services.title LIKE :search OR users.name LIKE :search
            ORDER BY services.created_at DESC
            LIMIT :limit
        ");
        
        $stmt->bindValue(':search', "$search%", PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
    
        return array_map(fn($row) => new Service($row), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}
