<?php
session_start();
?>

<link rel="stylesheet" href="css/personal_details.css">

<?php function drawProfile($profile, $profile_preferences) { ?>
    <div id="profile" class="tab-content active">
        <div class="personal-details">
            <div class="profile-header">

                <?php drawIcon($profile); ?>
                <?php drawInfo($profile); ?>
                
            </div>
            
            <hr class="section-divider">
            
            <div class="profile-body">
            
                <?php drawAuth(); ?>
    
                <hr class="section-divider">
                
                <?php drawBio($profile); ?>
                
                <hr class="section-divider">
                
                <?php drawPreferences($profile_preferences); ?>
                
            </div>

            <?php drawControls(); ?>
        </div>

        <?php drawReviews(); ?>
    </div>
    
<?php } ?>

<?php function drawIcon($profile) { ?>
    <div class="profile-icon">
        <?php if (!empty($profile['profile_picture'])): ?>
            <img src="<?= htmlspecialchars($profile['profile_picture']) ?>" alt="Profile Picture">
        <?php else: ?>
            <i class="fa-solid fa-image-portrait"></i>
        <?php endif; ?>
    </div>
<?php } ?>

<?php function drawInfo($profile) { ?>
    <div class="profile-info">
        <h2><?= htmlspecialchars($_SESSION['user']['name']) ?></h2>
        <p class="username">@<?= htmlspecialchars($_SESSION['user']['username']) ?></p>
        <p><i class="fas fa-map-marker-alt"></i> Located in <?= htmlspecialchars($profile['location']) ?></p>
        <p><i class="fas fa-calendar-alt"></i> Joined in <?= date('F Y', strtotime($_SESSION['user']['created_at'])) ?></p>
    </div>

    <button id="editProfBtn" class="btn"><i class="fa-solid fa-pencil"></i></button>
<?php } ?>

<?php function drawAuth() { ?>
    <div class="profile-auth">
        <div class="content">
            <h3>Authentication</h3>
            <p id="authEmail"><i class="fas fa-envelope"></i> Email: <?= htmlspecialchars($_SESSION['user']['email']) ?></p>
            <p id="authPassword"><i class="fas fa-lock"></i> Password: ••••••••</p>
        </div>

        <button id="editAuthBtn" class="btn"><i class="fa-solid fa-pencil"></i></button>
    </div>
<?php } ?>

<?php function drawBio($profile) { ?>
    <div class="profile-bio">
        <div class="content">
            <h3>Bio</h3>
            <p id="bioText"><?= htmlspecialchars($profile['bio'] ?? 'No bio available') ?></p>
        </div>
        
        <button id="editBioBtn" class="btn"><i class="fa-solid fa-pencil"></i></button>
    </div>
<?php } ?>

<?php function drawPreferences($profile_preferences) { ?>
    <div class="profile-preferences">
        <div class="content">
            <h3>Preferences</h3>
            <?php drawLanguage($profile_preferences); ?>
            
            <?php drawDateTime($profile_preferences); ?>
            
        </div>

        <button id="editPrefsBtn" class="btn"><i class="fa-solid fa-pencil"></i></button>
    </div>
<?php } ?>

<?php function drawLanguage($profile_preferences) { ?>
    <p><i class="fas fa-language"></i> <?= htmlspecialchars($profile_preferences['language'] ?? 'Language not set') ?> (<?= htmlspecialchars($profile_preferences['proficiency'] ?? 'N/A') ?>): <?= htmlspecialchars($profile_preferences['communication'] ?? 'Not set') ?></p>
<?php } ?>

<?php function drawDateTime($profile_preferences) { ?>
    <p><?php 
        $preferencesJson = $profile_preferences['preferred_days_times'] ?? '{}';
        $preferencesData = json_decode($preferencesJson, true);
        
        $grouped = [];
        
        foreach ($preferencesData['days'] as $dayInfo) {
            $time = $dayInfo['time'];
            if (!isset($grouped[$time])) {
                $grouped[$time] = [];
            }
            $grouped[$time][] = $dayInfo['day'];
        }
        
        foreach ($grouped as $time => $days) {
            $dayList = implode(', ', $days);
            echo "<p><i class='fa-regular fa-calendar-days'></i> {$dayList}</p>";
            echo "<p><i class='fa-solid fa-clock'></i> {$time}</p>";
        }
    ?></p>
<?php } ?>
    
<?php function drawControls() { ?>
    <div class="profile-controls">
        <a href="#" class="btn">Preview Profile</a>
        <a href="index.php" class="btn">Explore Platform</a>
    </div>
<?php } ?>

<?php function drawReviews() { ?>
    <div class="reviews-section">
        <div class="reviews-list">
            <h3>Reviews from freelancers</h3>
            <div class="reviews">
                <!-- TODO -->
            </div>
        </div>
    </div>
<?php } ?>