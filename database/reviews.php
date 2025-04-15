<?php
function getLatestReviews(PDO $db, int $limit = 3): array {
    $stmt = $db->prepare("
        SELECT 
            reviews.rating,
            reviews.comment,
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
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getServiceRatingInfo(PDO $pdo, int $service_id): array {
    $stmt = $pdo->prepare("
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

?>