<?php
session_start();
require_once 'database/connection.php';
require_once 'database/profiles.php';
require_once 'database/profile_preferences.php';

if (!isset($_SESSION['user']['id'])) {
    header('Location: authentication/login.php');
    exit;
}
$userId = $_SESSION['user']['id'];

require 'templates/common/header.php';
require 'templates/personal_details.tpl.php';
require 'templates/edit_modal.tpl.php';

$profile = getProfile($db, $userId);
$profile_preferences = getProfilePreferences($db, $userId);
?>

<link rel="stylesheet" href="css/dashboard.css">

<div class="dashboard">
    <div class="sidebar">
        <h3><i class="fa-solid fa-bars"></i><span>Menu</span></h3>
        <ul class="menu-content">
            <li><a href="#" class="tab-link active" data-tab="profile"><i class="fa-solid fa-user"></i><span>Personal Details</span></a></li>
            <li><a href="#" class="tab-link" data-tab="favorites"><i class="fa-solid fa-heart"></i><span>Favorites</span></a></li>
            <li><a href="#" class="tab-link" data-tab="listings"><i class="fa-solid fa-clipboard"></i><span>Own Listings</span></a></li>            
            <li><a href="#" class="tab-link" data-tab="settings"><i class="fa-solid fa-gear"></i><span>Settings</span></a></li>            
            <li class="logout"><a href="/authentication/logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i><span>Logout</span></a></li>
        </ul>
    </div>
    
    <div class="dashboard-content">
        <?php drawProfile($profile, $profile_preferences); ?>

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

        <div class="tab-content" id="settings">
            <div class="settings-details">
                <h2>Settings</h2>
                <p>Manage your account settings here.</p>
            </div>
        </div>

    </div>
    
    <div id="editProfileModal" class="modal hidden">
        <?php drawEditProfileModal($profile); ?>
    </div>
    
    <div id="editBioModal" class="modal hidden">
        <?php drawEditBioModal($profile); ?>
    </div>

    <div id="editModalPrefs" class="modal hidden">
        <?php drawEditPreferencesModal($profile); ?>
    </div>
</div>

<script src="js/dashboard.js" defer></script>
<script src="https://kit.fontawesome.com/b427850aeb.js" crossorigin="anonymous"></script>

<?php require 'templates/common/footer.php'; ?>
