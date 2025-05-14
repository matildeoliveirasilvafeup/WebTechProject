<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/session.php';

require_once __DIR__ . '/../database/user.class.php'; 
require_once __DIR__ . '/../database/profiles.class.php';
require_once __DIR__ . '/../database/profile_preferences.class.php';
require_once __DIR__ . '/../database/favorites.class.php';
require_once __DIR__ . '/../database/service.class.php';

require_once __DIR__ . '/../templates/dashboard.tpl.php';
require_once __DIR__ . '/../templates/personal_details.tpl.php';
require_once __DIR__ . '/../templates/settings.tpl.php';
require_once __DIR__ . '/../templates/edit_modal.tpl.php';
require_once __DIR__ . '/../templates/favorites.tpl.php';
require_once __DIR__ . '/../templates/service.tpl.php';

require_once __DIR__ . '/../templates/common/header.tpl.php';
require_once __DIR__ . '/../templates/common/footer.tpl.php'; 

$session = Session::getInstance();
if (!$session || !$session->getUser()) {
    header('Location: login.php');
    exit;
}

$user = $session->getUser();
if ($user && isset($user->id)) {
    $userId = $user->id;
} else {
    http_response_code(403);
    exit("User not authenticated.");
}        

$profile = Profile::getByUserId($userId);
$profile_preferences = ProfilePreferences::getByUserId($userId);
$favorites = Favorite::getByUserId($userId);

drawHeader();
drawDashboard($profile, $user, $profile_preferences, $favorites);
drawFooter(); 
?>
