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
    public array $mediaUrls = [];
    public ?string $categoryName;
    public ?int $categoryId;
    public ?int $freelancerId;
    public ?int $deliveryTime;     
    public ?int $numberOfRevisions;
    public ?string $language;
    public int $favoritesCount;

    public function __construct(array $data) {
        $this->id = (int)$data['id'];
        $this->title = $data['title'];
        $this->description = $data['description'];
        $this->price = (float)$data['price'];
        $this->freelancerName = $data['freelancer_name'];
        $this->profilePicture = $data['profile_picture'] ?? null;
        $this->mediaUrls = $data['mediaUrls'] ?? [];
        $this->categoryName = $data['category_name'] ?? null;
        $this->categoryId = isset($data['category_id']) ? (int)$data['category_id'] : null;
        $this->freelancerId = (int)$data['freelancer_id'] ?? null;
        $this->deliveryTime = isset($data['delivery_time']) ? (int)$data['delivery_time'] : null;
        $this->numberOfRevisions = isset($data['number_of_revisions']) ? (int)$data['number_of_revisions'] : null;
        $this->language = $data['language'] ?? null;
        $this->favoritesCount = (int)($data['favorites_count'] ?? 0);
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
                services.*,
                users.name AS freelancer_name,
                (
                    SELECT GROUP_CONCAT(media_url)
                    FROM service_images 
                    WHERE service_id = services.id 
                    ORDER BY id ASC LIMIT 1
                ) AS media_urls
            FROM services
            JOIN users ON services.freelancer_id = users.id
            ORDER BY services.created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($rows as &$row) {
            $row['mediaUrls'] = array_filter(explode(',', $row['media_urls'] ?? ''));
        }
    
        return array_map(fn($row) => new Service($row), $rows);
    }    

    public static function getById(int $id): ?Service {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT 
                services.*,
                users.name AS freelancer_name,
                profiles.profile_picture,
                categories.name AS category_name,
                subcategories.name AS subcategory_name,
                (
                    SELECT GROUP_CONCAT(media_url)
                    FROM service_images 
                    WHERE service_id = services.id 
                    ORDER BY id ASC
                ) AS media_urls
            FROM services
            JOIN users ON services.freelancer_id = users.id
            LEFT JOIN profiles ON users.id = profiles.user_id
            LEFT JOIN categories ON services.category_id = categories.id
            LEFT JOIN subcategories ON services.subcategory_id = subcategories.id
            WHERE services.id = :id
            LIMIT 1
        ");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($data) {
            $data['mediaUrls'] = array_filter(explode(',', $data['media_urls'] ?? ''));
            return new Service($data);
        }
    
        return null;
    }    

    public static function getMoreFromFreelancer(int $freelancerId, int $excludeId, int $limit = 4): array {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT s.*, u.name AS freelancer_name,
            (   
                SELECT GROUP_CONCAT(media_url)
                FROM service_images 
                WHERE service_id = s.id 
                ORDER BY id ASC
                LIMIT 1
            ) AS media_urls
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
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($rows as &$row) {
            $row['mediaUrls'] = array_filter(explode(',', $row['media_urls'] ?? ''));
        }
    
        return array_map(fn($row) => new Service($row), $rows);
    }

    public static function getRelated(int $categoryId, int $excludeId, int $limit = 4): array {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT s.*, u.name AS freelancer_name,
            (
                SELECT GROUP_CONCAT(media_url)
                FROM service_images 
                WHERE service_id = s.id 
                ORDER BY id ASC
                LIMIT 1
            ) AS media_urls
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

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($rows as &$row) {
            $row['mediaUrls'] = array_filter(explode(',', $row['media_urls'] ?? ''));
        }
    
        return array_map(fn($row) => new Service($row), $rows);
    }

    public static function getServicesBySearch(string $search, int $limit = 30): array {
        $db = Database::getInstance();
    
        $stmt = $db->prepare("
            SELECT 
                services.*,
                users.name AS freelancer_name,
                profiles.profile_picture,
                (
                    SELECT GROUP_CONCAT(media_url)
                    FROM service_images 
                    WHERE service_id = services.id 
                    ORDER BY id ASC LIMIT 1
                ) AS media_urls
            FROM services
            JOIN users ON services.freelancer_id = users.id
            LEFT JOIN profiles ON users.id = profiles.user_id
            WHERE services.title LIKE :search OR users.name LIKE :search
            ORDER BY services.created_at DESC
            LIMIT :limit
        ");
        
        $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
    
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$row) {
            $row['mediaUrls'] = array_filter(explode(',', $row['media_urls'] ?? ''));
        }

        return array_map(fn($row) => new Service($row), $rows);
    }

    public static function getFilteredServices(string $search, array $filters, int $limit = 30): array {
        $db = Database::getInstance();
        $query = "
            SELECT 
                services.*,
                users.name AS freelancer_name,
                (
                    SELECT GROUP_CONCAT(media_url) 
                    FROM service_images 
                    WHERE service_id = services.id 
                    ORDER BY id ASC LIMIT 1
                ) AS media_urls
            FROM services
            JOIN users ON services.freelancer_id = users.id
            WHERE (services.title LIKE :search OR users.name LIKE :search)
        ";
    
        if (!empty($filters['category'])) {
            $query .= " AND services.category_id = :category";
        }
        if (!empty($filters['subcategories'])) {
            $subcatPlaceholders = [];
            foreach ($filters['subcategories'] as $index => $subcategory) {
                $subcatPlaceholders[] = ":subcat_$index";
            }
            $query .= " AND services.subcategory_id IN (" . implode(',', $subcatPlaceholders) . ")";
        }
        if (!empty($filters['min_price'])) {
            $query .= " AND services.price >= :min_price";
        }
        if (!empty($filters['max_price'])) {
            $query .= " AND services.price <= :max_price";
        }
        if (!empty($filters['delivery_time'])) {
            $query .= " AND services.delivery_time <= :delivery_time";
        }
        if (!empty($filters['number_of_revisions'])) {
            $query .= " AND services.number_of_revisions >= :number_of_revisions";
        }
        if (!empty($filters['language'])) {
            $query .= " AND services.language LIKE :language";
        }

        switch ($filters['sort'] ?? 'newest') {
            case 'oldest':
                $query .= " ORDER BY services.created_at ASC";
                break;
            case 'lowest_price':
                $query .= " ORDER BY services.price ASC";
                break;
            case 'highest_price':
                $query .= " ORDER BY services.price DESC";
                break;
            case 'lowest_rating':
                $query .= " ORDER BY services.average_rating ASC"; 
                break;
            case 'highest_rating':
                $query .= " ORDER BY services.average_rating DESC";
                break;
            default:
                $query .= " ORDER BY services.created_at DESC";
                break;
        }
    
        $query .= " LIMIT :limit";
    
        $stmt = $db->prepare($query);
        $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
        if (!empty($filters['category'])) {
            $stmt->bindValue(':category', $filters['category'], PDO::PARAM_INT);
        }
        if (!empty($filters['min_price'])) {
            $stmt->bindValue(':min_price', $filters['min_price'], PDO::PARAM_STR);
        }
        if (!empty($filters['max_price'])) {
            $stmt->bindValue(':max_price', $filters['max_price'], PDO::PARAM_STR);
        }
        if (!empty($filters['delivery_time'])) {
            $stmt->bindValue(':delivery_time', $filters['delivery_time'], PDO::PARAM_INT);
        }
        if (!empty($filters['number_of_revisions'])) {
            $stmt->bindValue(':number_of_revisions', $filters['number_of_revisions'], PDO::PARAM_INT);
        }
        if (!empty($filters['language'])) {
            $stmt->bindValue(':language', "%{$filters['language']}%", PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    
        if (!empty($filters['subcategories'])) {
            foreach ($filters['subcategories'] as $index => $subcategory) {
                $stmt->bindValue(":subcat_$index", $subcategory, PDO::PARAM_INT);
            }
        }

        $stmt->execute();
    
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$row) {
            $row['mediaUrls'] = array_filter(explode(',', $row['media_urls'] ?? ''));
        }

        return array_map(fn($row) => new Service($row), $rows);
    }

    public static function create(array $data): int {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO services (title, description, price, freelancer_id, category_id, subcategory_id,
                delivery_time, number_of_revisions, language
            ) VALUES (:title, :description, :price, :freelancer_id, :category_id, :subcategory_id,
                :delivery_time, :number_of_revisions, :language
            )
        ");
    
        $stmt->bindValue(':title', $data['title'], PDO::PARAM_STR);
        $stmt->bindValue(':description', $data['description'], PDO::PARAM_STR);
        $stmt->bindValue(':price', $data['price'], PDO::PARAM_STR);
        $stmt->bindValue(':freelancer_id', $data['freelancer_id'], PDO::PARAM_INT);
        $stmt->bindValue(':category_id', $data['category_id'], PDO::PARAM_INT);
        $stmt->bindValue(':subcategory_id', $data['subcategory_id'], PDO::PARAM_INT);
        $stmt->bindValue(':delivery_time', $data['delivery_time'], PDO::PARAM_INT);
        $stmt->bindValue(':number_of_revisions', $data['number_of_revisions'], PDO::PARAM_INT);
        $stmt->bindValue(':language', $data['language'], PDO::PARAM_STR);
    
        $stmt->execute();
    
        return (int)$db->lastInsertId();
    }

    public static function addMedia(int $serviceId, string $mediaUrl): bool {
        $db = Database::getInstance();
        
        $stmt = $db->prepare("
            INSERT INTO service_images (service_id, media_url)
            VALUES (:service_id, :media_url)
        ");
        $stmt->bindValue(':service_id', $serviceId, PDO::PARAM_INT);
        $stmt->bindValue(':media_url', $mediaUrl, PDO::PARAM_STR);
    
        return $stmt->execute();
    }

    public static function increaseFavoriteCount(int $serviceId): void {
        $db = Database::getInstance();

        $stmt = $db->prepare('UPDATE services SET favorites_count = favorites_count + 1 WHERE id = ?');

        $stmt->execute([$serviceId]);
    }

    public static function decreaseFavoriteCount(int $serviceId): void {
        $db = Database::getInstance();

        $stmt = $db->prepare('SELECT favorites_count FROM services WHERE id = ?');
        $stmt->execute([$serviceId]);
        $favoritesCount = $stmt->fetchColumn();

        if ($favoritesCount > 0) {
            $stmt = $db->prepare('UPDATE services SET favorites_count = favorites_count - 1 WHERE id = ?');
            $stmt->execute([$serviceId]);
        }
    }

    public static function getByUserId(int $userId): array {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT services.*, users.name AS freelancer_name,
            (
                SELECT GROUP_CONCAT(media_url) 
                FROM service_images 
                WHERE service_id = services.id 
                ORDER BY id ASC LIMIT 1
            ) AS media_urls
            FROM services
            JOIN users ON services.freelancer_id = users.id
            JOIN profiles ON users.id = profiles.user_id
            WHERE services.freelancer_id = :freelancer_id
            ORDER BY services.created_at DESC
        ");
        $stmt->bindValue(':freelancer_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
   
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$row) {
            $row['mediaUrls'] = array_filter(explode(',', $row['media_urls'] ?? ''));
        }

        return array_map(fn($row) => new Service($row), $rows);
    }
}
