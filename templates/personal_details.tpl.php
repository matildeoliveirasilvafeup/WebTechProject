
<?php function drawProfile($profile, $profile_preferences, $user, $isPrivate = true, $isAdmin = false) { ?>
    <?php if (!$isPrivate) { ?>
        <h1><?= htmlspecialchars($user->name) ?>'s Profile</h1>
    <?php } ?>
    
    <div id="profile" class="tab-content active">
        <div class="personal-details">
            <div class="profile-header">

                <?php drawIcon($profile); ?>
                <?php drawInfo($profile, $user, $isPrivate, $isAdmin); ?>
                
            </div>
            
            <hr class="section-divider">
            
            <div class="profile-body">
                
                <?php drawBio($profile, $isPrivate); ?>
                
                <hr class="section-divider">
                
                <?php drawPreferences($profile_preferences, $isPrivate); ?>
                
            </div>

            <?php if ($isPrivate)
                drawControls($user); ?>
        </div>

        <?php if ($isPrivate)
            drawReviews(); ?>
    </div>
    
<?php } ?>

<?php function drawIcon($profile) { ?>
    <div class="profile-icon">
        <?php if (!empty($profile->profilePicture)): ?>
            <img src="<?= htmlspecialchars($profile->profilePicture) ?>" alt="Profile Picture">
        <?php else: ?>
            <i class="fa-solid fa-image-portrait"></i>
        <?php endif; ?>
    </div>
<?php } ?>

<?php function drawInfo($profile, $user, $isPrivate, $isAdmin) { ?>
    <div class="profile-info">
        <div class="name-line">
            <h2><?php if ($user->role === 'admin') { ?> Admin <?php } ?>
            <?= htmlspecialchars($user->name) ?></h2>

            <?php if (!$isPrivate && $isAdmin && $user->role !== 'admin'): ?>
                <div class="admin-buttons">
                    <form method="POST" action="/actions/action_promote_admin.php" class="admin-action-form">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($user->id) ?>">
                        <button type="submit" class="btn btn-promote" title="Promote to Admin">
                            <i class="fa-solid fa-user-shield"></i> Promote to Admin
                        </button>
                    </form>

                    <form method="POST" action="/actions/action_ban_user.php">
                        <input type="hidden" name="username" value="<?= htmlspecialchars($user->username) ?>">
                        <button type="submit" class="btn btn-ban">
                            <i class="fa-solid fa-user-slash"></i> Ban
                        </button>
                    </form>
                </div>    
            <?php endif; ?>
        </div>
        <p class="username">@<?= htmlspecialchars($user->username) ?></p>
        <p><i class="fas fa-map-marker-alt"></i> Located in <?= htmlspecialchars($profile->location) ?></p>
        <p><i class="fas fa-calendar-alt"></i> Joined in <?= date('F Y', strtotime($user->createdAt)) ?></p>
    </div>
    <?php if ($isPrivate) { ?>
        <button id="editProfBtn" class="btn"><i class="fa-solid fa-pencil"></i></button>
    <?php } 
} ?>

<?php function drawBio($profile, $isPrivate) { ?>
    <div class="profile-bio">
        <div class="content">
            <h3>Bio</h3>
            <p id="bioText"><?= htmlspecialchars($profile->bio ?? 'No bio available') ?></p>
        </div>

        <?php if ($isPrivate) { ?>
            <button id="editBioBtn" class="btn"><i class="fa-solid fa-pencil"></i></button>
        <?php } ?>    
    </div>
<?php } ?>

<?php function drawPreferences($profile_preferences, $isPrivate) { ?>
    <div class="profile-preferences">
        <div class="content">
            <h3>Preferences</h3>
            <?php drawLanguage($profile_preferences); ?>
            
            <?php drawDateTime($profile_preferences); ?>
            
        </div>

        <?php if ($isPrivate) { ?>
            <button id="editPrefsBtn" class="btn"><i class="fa-solid fa-pencil"></i></button>
        <?php } ?>    
    </div>
<?php } ?>

<?php function drawLanguage($profile_preferences) { ?>
    <p><i class="fas fa-language"></i> <?= htmlspecialchars($profile_preferences->language ?? 'Language not set') ?> (<?= htmlspecialchars($profile_preferences->proficiency ?? 'N/A') ?>): <?= htmlspecialchars($profile_preferences->communication ?? 'Not set') ?></p>
<?php } ?>

<?php function drawDateTime($profile_preferences) { 
    $preferencesData = $profile_preferences->preferredDaysTimes ?? ['days' => []];
    
    if (isset($preferencesData['days']) && is_array($preferencesData['days'])) {
        $grouped = [];

        foreach ($preferencesData['days'] as $dayInfo) {
            $time = $dayInfo['time'] ?? '';
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
    } else {
        echo "<p>No preferences set for days and times.</p>";
    }
} ?>

    
<?php function drawControls($user) { ?>
    <div class="profile-controls">
        <a href="/pages/profile.php?user=<?= $user->username ?>" class="btn">Preview Profile</a>
        <a href="/index.php" class="btn">Explore Platform</a>
    </div>
<?php } ?>

<?php function drawReviews() { ?>
    <div class="freelancer-reviews-section">
        <div class="reviews-list">
            <h3>Reviews from freelancers</h3>

            <hr class="section-divider">

            <div class="freelancer-reviews">
                <!-- TODO -->
            </div>
        </div>
    </div>
<?php } ?>

<?php function drawPublicProfileStart() { ?>
    <div class="public-profile">
<?php } 

function drawPublicProfileEnd() { ?>
    </div>
<?php } ?>