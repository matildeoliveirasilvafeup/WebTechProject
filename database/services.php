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
?>
