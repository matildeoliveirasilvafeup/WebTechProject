<?php
declare(strict_types=1);

require_once __DIR__ . '/../database/user.class.php'; 

require __DIR__ . '/../database/profiles.class.php';
require __DIR__ . '/../database/profile_preferences.class.php';

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/database.php';

$session = Session::getInstance();
$db = Database::getInstance();

if (!$session || !$session->getUser()) {
    header('Location: login.php');
    exit;
}

$user = $session->getUser();

        
if ($user && isset($user->id)) {
    $userId = $user->id;
}else {
    http_response_code(403);
    exit("User not authenticated.");
}
$profile = Profile::getByUserId($userId);
$profile_preferences = ProfilePreferences::getByUserId($userId);

require '../templates/personal_details.tpl.php';
require '../templates/settings.tpl.php';
require '../templates/edit_modal.tpl.php';

require '../templates/common/header.tpl.php';

drawHeader();
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


<div class="dashboard">
    <div class="sidebar">
        <h3><i class="fa-solid fa-bars"></i><span>Menu</span></h3>
        <ul class="menu-content">
            <li><a href="#" class="tab-link active" data-tab="profile"><i class="fa-solid fa-user"></i><span>Personal Details</span></a></li>
            <li><a href="#" class="tab-link" data-tab="favorites"><i class="fa-solid fa-heart"></i><span>Favorites</span></a></li>
            <li><a href="#" class="tab-link" data-tab="listings"><i class="fa-solid fa-clipboard"></i><span>Own Listings</span></a></li>
            <li><a href="#" class="tab-link" data-tab="settings"><i class="fa-solid fa-gear"></i><span>Settings</span></a><li>
            <li class="logout"><a href="/authentication/logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i><span>Logout</span></a></li>
        </ul>
    </div>
    
    <div class="dashboard-content">
        <?php drawProfile($profile, $profile_preferences, $user); ?>

        <div class="tab-content" id="favorites">
            <div class="favourites-details">
                <h2>Your Favorites</h2>
                <p>Here's a list of your favorite services or listings.</p>
            </div>
        </div>
        
        <div class="tab-content" id="listings">
            <div class="own-listings">
                <h2>Your Listings</h2>
                <p>Manage your own posted services or offers here.</p>
            </div>
        </div>
        
        <?php drawSettings($user); ?>
    </div>
    
    <div id="editProfileModal" class="modal hidden">
        <?php drawEditProfileModal($profile, $user); ?>
    </div>

    <div id="editBioModal" class="modal hidden">
        <?php drawEditBioModal($profile); ?>
    </div>

    <div id="editModalPrefs" class="modal hidden">
        <?php drawEditPreferencesModal($profile); ?>
    </div>
</div>

<script type="module" src="../js/dashboard.js" defer></script>

<script src="https://kit.fontawesome.com/b427850aeb.js" crossorigin="anonymous"></script>

<?php 
    require '../templates/common/footer.tpl.php'; 
    drawFooter(); 
?>
