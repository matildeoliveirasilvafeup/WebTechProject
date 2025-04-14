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
