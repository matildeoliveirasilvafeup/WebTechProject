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
        <div class="modal-content">
            <h3>Edit Profile</h3>
            <form id="editProfileForm" method="POST" action="database/update_profile_details.php" enctype="multipart/form-data">
                <label for="edit-name">Name:</label>
                <input type="text" id="edit-name" name="name" value="<?= htmlspecialchars($_SESSION['user']['name']) ?>" required>

                <label for="edit-username">Username:</label>
                <input type="text" id="edit-username" name="username" value="<?= htmlspecialchars($_SESSION['user']['username']) ?>" required>

                <label for="edit-location">Location:</label>
                <select id="edit-location" name="location" required>
                    <?php
                    $countries = [
                        "Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Antigua and Barbuda", "Argentina",
                        "Armenia", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados",
                        "Belarus", "Belgium", "Belize", "Benin", "Bhutan", "Bolivia", "Bosnia and Herzegovina",
                        "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Cabo Verde", "Cambodia",
                        "Cameroon", "Canada", "Central African Republic", "Chad", "Chile", "China", "Colombia", "Comoros",
                        "Congo (Congo-Brazzaville)", "Costa Rica", "Croatia", "Cuba", "Cyprus", "Czech Republic",
                        "Democratic Republic of the Congo", "Denmark", "Djibouti", "Dominica", "Dominican Republic",
                        "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Eswatini",
                        "Ethiopia", "Fiji", "Finland", "France", "Gabon", "Gambia", "Georgia", "Germany", "Ghana",
                        "Greece", "Grenada", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Honduras",
                        "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Israel", "Italy",
                        "Ivory Coast", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Kuwait",
                        "Kyrgyzstan", "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein",
                        "Lithuania", "Luxembourg", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta",
                        "Marshall Islands", "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Monaco",
                        "Mongolia", "Montenegro", "Morocco", "Mozambique", "Myanmar (Burma)", "Namibia", "Nauru",
                        "Nepal", "Netherlands", "New Zealand", "Nicaragua", "Niger", "Nigeria", "North Korea",
                        "North Macedonia", "Norway", "Oman", "Pakistan", "Palau", "Palestine", "Panama", "Papua New Guinea",
                        "Paraguay", "Peru", "Philippines", "Poland", "Portugal", "Qatar", "Romania", "Russia", "Rwanda",
                        "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa",
                        "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia", "Seychelles",
                        "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia",
                        "South Africa", "South Korea", "South Sudan", "Spain", "Sri Lanka", "Sudan", "Suriname",
                        "Sweden", "Switzerland", "Syria", "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Timor-Leste",
                        "Togo", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Tuvalu",
                        "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "Uruguay",
                        "Uzbekistan", "Vanuatu", "Vatican City", "Venezuela", "Vietnam", "Yemen", "Zambia", "Zimbabwe"
                    ];
                    $selectedLocation = htmlspecialchars($profile['location'] ?? 'Portugal');

                    foreach ($countries as $country) {
                        $selected = ($country === $selectedLocation) ? 'selected' : '';
                        echo "<option value=\"$country\" $selected>$country</option>";
                    }
                    ?>
                </select>

                <label for="edit-profile-picture">Profile Picture:</label>
                <input type="file" id="edit-profile-picture" name="profile_picture" accept="image/*">

                <div class="modal-buttons">
                    <button type="submit" class="btn save">Save</button>
                    <button type="button" id="cancelEditProfile" class="btn cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
    <div id="editBioModal" class="modal hidden">
        <div class="modal-content">
            <h3>Edit Bio</h3>
            <form id="editBioForm">
                <textarea id="editBio" name="bio" rows="4" placeholder="Enter your bio..."><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea>
                <div class="modal-buttons">
                    <button type="submit" class="btn save">Save</button>
                    <button type="button" id="cancelEditBio" class="btn cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="modal hidden">
        <div class="modal-content">
            <h3>Edit Preferences</h3>
            <form id="preferencesForm">
                <label for="edit-language">Language:</label>
                <select id="edit-language" name="language" required>
                    <?php
                    $languages = [
                        "Afrikaans", "Albanian", "Arabic", "Armenian", "Basque", "Bengali", "Bosnian", "Bulgarian", "Catalan",
                        "Chinese", "Croatian", "Czech", "Danish", "Dutch", "English", "Estonian", "Finnish", "French", "Georgian",
                        "German", "Greek", "Gujarati", "Haitian Creole", "Hebrew", "Hindi", "Hungarian", "Icelandic", "Indonesian",
                        "Irish", "Italian", "Japanese", "Javanese", "Kannada", "Kazakh", "Khmer", "Korean", "Kurdish", "Latvian",
                        "Lithuanian", "Macedonian", "Malay", "Maltese", "Marathi", "Norwegian", "Persian", "Polish", "Portuguese",
                        "Punjabi", "Romanian", "Russian", "Serbian", "Sindhi", "Sinhala", "Slovak", "Slovenian", "Spanish", "Swahili",
                        "Swedish", "Tamil", "Telugu", "Thai", "Turkish", "Ukrainian", "Urdu", "Vietnamese", "Welsh", "Xhosa", "Yoruba",
                        "Zulu"
                    ];
                    $selectedLanguage = htmlspecialchars($profile_preferences['language'] ?? 'English');

                    foreach ($languages as $language) {
                        $selected = ($language === $selectedLanguage) ? 'selected' : '';
                        echo "<option value=\"$language\" $selected>$language</option>";
                    }
                    ?>
                </select>

                <label>Proficiency:
                    <select name="proficiency" required>
                    <option value="Basic" <?= $profile_preferences['proficiency'] === 'Basic' ? 'selected' : '' ?>>Basic</option>
                    <option value="Conversational" <?= $profile_preferences['proficiency'] === 'Conversational' ? 'selected' : '' ?>>Conversational</option>
                    <option value="Fluent" <?= $profile_preferences['proficiency'] === 'Fluent' ? 'selected' : '' ?>>Fluent</option>
                    <option value="Native/Bilingual" <?= $profile_preferences['proficiency'] === 'Native/Bilingual' ? 'selected' : '' ?>>Native/Bilingual</option>
                    </select>
                </label>

                <label>Communication Preference:
                    <select name="communication" required>
                    <option value="Messages Only" <?= $profile_preferences['communication'] === 'Messages Only' ? 'selected' : '' ?>>Messages Only</option>
                    <option value="Calls and Messages" <?= $profile_preferences['communication'] === 'Calls and Messages' ? 'selected' : '' ?>>Calls and Messages</option>
                    </select>
                </label>

                <fieldset>
                    <legend>Preferred Days and Times</legend>
                    <div class="day-time">
                        <label><input type="checkbox" name="days" value="Monday"> Monday</label>
                        <label><input type="checkbox" name="days" value="Tuesday"> Tuesday</label>
                        <label><input type="checkbox" name="days" value="Wednesday"> Wednesday</label>
                        <label><input type="checkbox" name="days" value="Thursday"> Thursday</label>
                        <label><input type="checkbox" name="days" value="Friday"> Friday</label>
                        <label><input type="checkbox" name="days" value="Saturday"> Saturday</label>
                        <label><input type="checkbox" name="days" value="Sunday"> Sunday</label>
                    </div>
                    <div>
                        <label>Start Time: <input type="time" name="start_time" required></label>
                        <label>End Time: <input type="time" name="end_time" required></label>
                    </div>
                </fieldset>


                <button type="submit">Save</button>
                <button type="button" id="cancelEdit">Cancel</button>
            </form>
        </div>
    </div>
</div>

<script src="js/dashboard.js" defer></script>
<script src="https://kit.fontawesome.com/b427850aeb.js" crossorigin="anonymous"></script>

<?php require 'templates/common/footer.php'; ?>
