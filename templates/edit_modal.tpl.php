<?php
session_start();
?>

<link rel="stylesheet" href="css/edit_modal.css">

<!-- Edit Profile Modal -->

<?php function drawEditProfileModal($profile) { ?>
    <div class="modal-content">
        <h3>Edit Profile</h3>
        <form id="editProfileForm" method="POST" action="database/update_profile_details.php" enctype="multipart/form-data">
            
            <?php
            drawName();

            drawUsername();

            drawLocation($profile);

            drawProfilePicture();

            drawButtons("cancelEditProfile");
            ?>
                    
        </form>
    </div>
<?php } ?>

<?php function drawName() { ?>
    <label for="edit-name">Name:</label>
    <input type="text" id="edit-name" name="name" value="<?= htmlspecialchars($_SESSION['user']['name']) ?>" required>
<?php } ?>

<?php function drawUsername() { ?>
    <label for="edit-username">Username:</label>
    <input type="text" id="edit-username" name="username" value="<?= htmlspecialchars($_SESSION['user']['username']) ?>" required>
<?php } ?>

<?php function drawLocation($profile) { ?>
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
<?php } ?>

<?php function drawProfilePicture() { ?>
    <label for="edit-profile-picture">Profile Picture:</label>
    <input type="file" id="edit-profile-picture" name="profile_picture" accept="image/*">
<?php } ?>

<!-- Edit Bio Modal -->

<?php function drawEditBioModal($profile) { ?>
    <div class="modal-content">
        <h3>Edit Bio</h3>
        <form id="editBioForm">
            <textarea id="editBio" name="bio" rows="4" placeholder="Enter your bio..."><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea>
            
            <?php drawButtons("cancelEditBio"); ?>
        </form>
    </div>
<?php } ?>

<!-- Edit Preferences Modal -->

<?php function drawEditPreferencesModal($profile_preferences) { ?>
    <div class="modal-content">
        <h3>Edit Preferences</h3>
        <form id="preferencesForm">
            
            <?php 
            drawLanguageField($profile_preferences);

            drawProficiencyField($profile_preferences);

            drawCommunicationPrefsField($profile_preferences);

            drawDateTimePrefsField($profile_preferences);

            drawButtons("cancelEditPrefs");
            ?>

        </form>
    </div>
<?php } ?>

<?php function drawLanguageField($profile_preferences) { ?>
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
<?php } ?>

<?php function drawProficiencyField() { ?>
    <label>Proficiency:
        <select name="proficiency" required>
        <option value="Basic" <?= $profile_preferences['proficiency'] === 'Basic' ? 'selected' : '' ?>>Basic</option>
        <option value="Conversational" <?= $profile_preferences['proficiency'] === 'Conversational' ? 'selected' : '' ?>>Conversational</option>
        <option value="Fluent" <?= $profile_preferences['proficiency'] === 'Fluent' ? 'selected' : '' ?>>Fluent</option>
        <option value="Native/Bilingual" <?= $profile_preferences['proficiency'] === 'Native/Bilingual' ? 'selected' : '' ?>>Native/Bilingual</option>
        </select>
    </label>
<?php } ?>

<?php function drawCommunicationPrefsField() { ?>
    <label>Communication Preference:
        <select name="communication" required>
        <option value="Messages Only" <?= $profile_preferences['communication'] === 'Messages Only' ? 'selected' : '' ?>>Messages Only</option>
        <option value="Calls and Messages" <?= $profile_preferences['communication'] === 'Calls and Messages' ? 'selected' : '' ?>>Calls and Messages</option>
        </select>
    </label>
<?php } ?>

<?php function drawDateTimePrefsField() { ?>
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
<?php } ?>

<!-- Common -->

<?php function drawButtons($cancelButtonId) { ?>
    <div class="modal-buttons">
        <button type="submit" class="btn save">Save</button>
        <button type="button" id="<?= htmlspecialchars($cancelButtonId) ?>" class="btn cancel">Cancel</button>
    </div>
<?php } ?>