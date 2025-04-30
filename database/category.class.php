<?php
declare(strict_types=1);
require_once(__DIR__ . '/../includes/database.php');

class Category {
    public int $id;
    public string $name;
    public string $icon;

    public function __construct(array $data) {
        $this->id = (int)$data['id'];
        $this->name = $data['name'];
        $this->icon = $data['icon'];
    }
    public static function getAll() {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM categories ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllWithSubcategories() {
        $db = Database::getInstance();
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
}
?>
