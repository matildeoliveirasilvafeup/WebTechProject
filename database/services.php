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
            (
                SELECT media_url 
                FROM service_images 
                WHERE service_id = services.id 
                ORDER BY id ASC LIMIT 1
            ) AS media_url
        FROM services
        JOIN users ON services.freelancer_id = users.id
        LEFT JOIN profiles ON users.id = profiles.user_id
        WHERE services.id = :id
        LIMIT 1
    ");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $service = $stmt->fetch(PDO::FETCH_ASSOC);

    return $service ?: null;
}

?>
