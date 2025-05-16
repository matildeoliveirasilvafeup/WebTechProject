<?php function drawDashboard($profile, $user, $profile_preferences, $favorites, $ownServices) { ?>

    <div class="dashboard">
        <?php 
            drawSidebar();

            drawContent($profile, $user, $profile_preferences, $favorites, $ownServices);

            drawEditModal($profile, $user, $profile_preferences);
        ?>       
    </div>

    <script type="module" src="../js/dashboard.js" defer></script>
<?php } ?>

<?php function drawSidebar() { ?>
    <div class="sidebar">
        <h3><i class="fa-solid fa-bars"></i><span>Menu</span></h3>
        <ul class="menu-content">
            <li><a href="#" class="tab-link active" data-tab="profile"><i class="fa-solid fa-user"></i><span>Personal Details</span></a></li>
            <li><a href="#" class="tab-link" data-tab="favorites"><i class="fa-solid fa-heart"></i><span>Favorites</span></a></li>
            <li><a href="#" class="tab-link" data-tab="listings"><i class="fa-solid fa-clipboard"></i><span>Own Listings</span></a></li>
            <li><a href="#" class="tab-link" data-tab="settings"><i class="fa-solid fa-gear"></i><span>Settings</span></a><li>
            <li class="logout"><a href="/actions/action_logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i><span>Logout</span></a></li>
        </ul>
    </div>
<?php } ?>

<?php function drawContent($profile, $user, $profile_preferences, $favorites, $ownServices) { ?>
    <div class="dashboard-content">
        <?php 
            drawProfile($profile, $profile_preferences, $user);
            drawFavorites($favorites); 
            drawOwnListings($ownServices);
            drawSettings($user); 
        ?>
    </div>
<?php } ?>

<?php function drawEditModal($profile, $user, $profile_preferences) { ?>
    <div id="editProfileModal" class="modal hidden">
        <?php drawEditProfileModal($profile, $user); ?>
    </div>

    <div id="editBioModal" class="modal hidden">
        <?php drawEditBioModal($profile); ?>
    </div>

    <div id="editModalPrefs" class="modal hidden">
        <?php drawEditPreferencesModal($profile); ?>
    </div>
<?php } ?>

<?php function drawFavorites($favorites) {
    drawFavoritesOrOwnListings($favorites, true);
} ?>

<?php function drawOwnListings($services) {
    drawFavoritesOrOwnListings($services, false);
} ?>

<?php function drawFavoritesOrOwnListings($services, $isFavorites) { 
    $id = $isFavorites ? 'favorites' : 'listings';
?>
    <div class="tab-content" id= "<?= $id ?>">
        <div class="dashboard-details">
            <?php if ($isFavorites) : ?>
                <h2>Favorites</h2>
            <?php else : ?>
                <h2>Own Listings</h2>
            <?php endif;   ?>          
            <?php if (empty($services)): ?>
                <p class="no"> <p>
                    <?php if ($isFavorites) : ?>
                        You don't have any favorites yet.
                    <?php else : ?>
                        You don't have any own listings yet.
                    <?php endif; ?>   
                </p> <p>
            <?php else:
                drawServiceGrid($services, true);
            endif; ?>
        </div>
    </div>
<?php } ?>