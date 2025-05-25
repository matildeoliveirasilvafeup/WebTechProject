<?php 
declare(strict_types=1);

require_once __DIR__ . '/../database/service.class.php';

$searchQuery = $_GET['q'] ?? '';
$category = $_GET['category'] ?? null;
$subcategories = $_GET['subcategory'] ?? [];
$minPrice = $_GET['min_price'] ?? null;
$maxPrice = $_GET['max_price'] ?? null;
$deliveryTime = $_GET['delivery_time'] ?? null;
$numberOfRevisions = $_GET['number_of_revisions'] ?? null;
$language = $_GET['language'] ?? null;
$sort = $_GET['sort'] ?? 'newest';

$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 32;
$offset = ($page - 1) * $limit;

$filters = [
    'category' => $category,
    'subcategories' => $subcategories,
    'min_price' => $minPrice,
    'max_price' => $maxPrice,
    'delivery_time' => $deliveryTime,
    'number_of_revisions' => $numberOfRevisions,
    'language' => $language,
    'sort' => $sort,
];

$services = Service::getFilteredServices($searchQuery, $filters, $limit, $offset);
$totalServices = Service::getFilteredServicesCount($searchQuery, $filters);
$totalPages = (int)ceil($totalServices / $limit);

echo json_encode([
    'services' => $services,
    'totalPages' => $totalPages,
    'page' => $page
]);
?>