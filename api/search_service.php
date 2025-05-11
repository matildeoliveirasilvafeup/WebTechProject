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

$filters = [
    'category' => $category,
    'subcategories' => $subcategories,
    'min_price' => $minPrice,
    'max_price' => $maxPrice,
    'delivery_time' => $deliveryTime,
    'number_of_revisions' => $numberOfRevisions,
    'language' => $language,
];

$services = Service::getFilteredServices($searchQuery, $filters, 30);

echo json_encode($services);
?>