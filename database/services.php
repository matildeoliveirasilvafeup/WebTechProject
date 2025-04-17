<?php
require_once 'connection.php';

function getAllServices(PDO $db) {
    $stmt = $db->prepare("
        SELECT services.*, users.username, profiles.profile_picture
        FROM services
        JOIN users ON services.freelancer_id = users.id
        JOIN profiles ON users.id = profiles.user_id
        ORDER BY services.created_at DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getFeaturedServices(PDO $db, int $limit = 6): array {
    $stmt = $db->prepare("
        SELECT 
            services.id,
            services.title,
            services.description,
            services.price,
            users.name AS freelancer_name,
            service_images.media_url
        FROM services
        JOIN users ON services.freelancer_id = users.id
        LEFT JOIN service_images ON service_images.service_id = services.id
        GROUP BY services.id
        ORDER BY services.created_at DESC
        LIMIT :limit
    ");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getServiceById(PDO $db, int $id): ?array {
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
    $service = $stmt->fetch(PDO::FETCH_ASSOC);

    return $service ?: null;
}

function getMoreFromFreelancer(PDO $db, int $freelancerId, int $excludeId, int $limit = 4): array {
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
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getRelatedServices(PDO $db, int $categoryId, int $excludeId, int $limit = 4): array {
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
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>
