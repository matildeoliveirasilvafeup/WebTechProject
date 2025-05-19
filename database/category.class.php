<?php
declare(strict_types=1);
require_once(__DIR__ . '/../includes/database.php');

class Subcategory {
    public int $id;
    public string $name;
    public int $categoryId;

    public function __construct(array $data) {
        $this->id = (int)$data['id'];
        $this->name = $data['name'];
        $this->categoryId = (int)$data['category_id'];
    }
}

class Category {
    public int $id;
    public string $name;
    public string $icon;
    public array $subcategories = [];

    public function __construct(array $data) {
        $this->id = (int)$data['id'];
        $this->name = $data['name'];
        $this->icon = $data['icon'];
    }
    public static function getAll() {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM categories ORDER BY name ASC");
        $stmt->execute();

        return array_map(fn($row) => new Category($row), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public static function getAllWithSubcategories() {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM categories ORDER BY name ASC");
        $stmt->execute();
        $categoriesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $categories = array_map(fn($row) => new Category($row), $categoriesData);

        $stmtSub = $db->prepare("SELECT * FROM subcategories ORDER BY name ASC");
        $stmtSub->execute();
        $subsData = $stmtSub->fetchAll(PDO::FETCH_ASSOC);
        $subs = array_map(fn($row) => new Subcategory($row), $subsData);
    
        foreach ($categories as $category) {
            foreach ($subs as $sub) {
                if ($sub->categoryId === $category->id) {
                    $category->subcategories[] = $sub;
                }
            }
        }
    
        return $categories;
    }

    public static function create(string $name, string $icon, PDO $db): int {
        $stmt = $db->prepare("INSERT INTO categories (name, icon) VALUES (?, ?)");
        $stmt->execute([$name, $icon]);
        return (int)$db->lastInsertId();
    }

    public static function addSubcategory(int $categoryId, string $subName, PDO $db): void {
        $stmt = $db->prepare("INSERT INTO subcategories (category_id, name) VALUES (?, ?)");
        $stmt->execute([$categoryId, $subName]);
    }
}
?>
