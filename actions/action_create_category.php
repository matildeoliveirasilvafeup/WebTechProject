<?php
declare(strict_types=1);
require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../includes/database.php');
require_once(__DIR__ . '/../database/category.class.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

$session = Session::getInstance();
if (!$session->validateCSRFToken($_POST['csrf_token'] ?? '')) {
    die('CSRF token validation failed');
}

$categoryName = trim($_POST['category_name'] ?? '');
$icon = trim($_POST['category_icon'] ?? '');
$subcategories = $_POST['subcategories'] ?? [];

if (empty($categoryName)) {
    $session->setError('Category name is required.');
    header('Location: /pages/create_category.php');
    exit();
}

try {
    $categoryId = Category::create($categoryName, $icon);

    foreach ($subcategories as $sub) {
        $subName = trim($sub);
        if (!empty($subName)) {
            Category::addSubcategory($categoryId, $subName);
        }
    }

    $_SESSION['success'] = 'Category created successfully.';
    header('Location: /pages/home_page.php');
    exit();
} catch (Exception $e) {
    $db->rollBack();
    $_SESSION['error'] = 'Error creating category: ' . $e->getMessage();
    header('Location: /pages/create_category.php');
    exit();
}
?>