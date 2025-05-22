<?php
    declare(strict_types=1);

    require_once (__DIR__ . '/../includes/session.php');

    require_once (__DIR__ . '/../database/user.class.php');
    require_once (__DIR__ . '/../database/profile_preferences.class.php');
    require_once (__DIR__ . '/../database/profiles.class.php');
    require_once (__DIR__ . '/../database/chat.class.php');
    require_once (__DIR__ . '/../database/hirings.class.php');
    require_once (__DIR__ . '/../database/service.class.php');

    require_once (__DIR__ . '/../templates/chat.tpl.php');
    require_once (__DIR__ . '/../templates/hirings.tpl.php');
    require_once (__DIR__ . '/../templates/category.tpl.php');
    require_once (__DIR__ . '/../templates/personal_details.tpl.php');

    require_once (__DIR__ . '/../templates/common/header.tpl.php');
    require_once (__DIR__ . '/../templates/common/footer.tpl.php');
    require_once (__DIR__ . '/../templates/common/utils.tpl.php'); 

    $username = $_GET['user'] ?? '';

    if (!$username) {
        http_response_code(400);
        exit("User not found.");
    }

    $categoriesMenu = Category::getAllWithSubcategories();
    $userId = User::getIdByUsername($username); 
    $user = User::getById($userId);
    $profile = Profile::getByUserId($userId);
    $profile_preferences = ProfilePreferences::getByUserId($userId);
    $session = Session::getInstance();
    $isAdmin = Session::isAdmin();

    drawPublicProfileStart();
    drawHeader();
    drawCategoryMenu($categoriesMenu);
    drawProfile($profile, $profile_preferences, $user, false, $isAdmin);
    drawChat();
    drawHirings();
    drawFooter();
    drawPublicProfileEnd();
?>