<?php
require_once 'connection.php';

function getAllCategories(PDO $db) {
    $stmt = $db->prepare("SELECT * FROM categories ORDER BY name ASC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getAllCategoriesWithSubcategories(PDO $db) {
    $stmt = $db->prepare("SELECT * FROM categories ORDER BY name ASC");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmtSub = $db->prepare("SELECT * FROM subcategories ORDER BY name ASC");
    $stmtSub->execute();
    $subcategories = $stmtSub->fetchAll(PDO::FETCH_ASSOC);

    $groupedSubs = [];
    foreach ($subcategories as $sub) {
        $groupedSubs[$sub['category_id']][] = $sub;
    }

    foreach ($categories as &$cat) {
        $cat['subcategories'] = $groupedSubs[$cat['id']] ?? [];
    }

    return $categories;
}
?>
