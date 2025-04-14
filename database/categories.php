<?php
require_once 'connection.php';

function getAllCategories(PDO $db) {
    $stmt = $db->prepare("SELECT * FROM categories ORDER BY name ASC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
