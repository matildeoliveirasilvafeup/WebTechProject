

<?php function drawSettings($user) { ?>
    <div id="settings" class="tab-content">
        <div id="settings-body">
            <?php
                drawAccountDetails($user);

                drawDeleteAccountSection();
            ?>
        </div>
    </div>

    <script src="../js/password.js"></script>
    <script type="module" src="../js/settings.js"></script>

<?php } ?>

<?php function encodeEmail($email) { 
    $parts = explode('@', $email);
    $local = $parts[0];
    $domain = $parts[1];

    $length = strlen($local);
    if ($length <= 2) {
        $masked = str_repeat('*', $length);
    } else {
        $masked = $local[0] . str_repeat('*', $length - 2) . $local[$length - 1];
    }

    return $masked . '@' . $domain;
} ?>

<?php function drawAccountDetails($user) { ?>
    <div class="account-details">
        <div id="notification" class="notification hidden">
            <p>Your changes have been updated successfully.</p>
        </div>

        <?php
            drawEmailSection($user); 
        
            drawPasswordSection($user);
        ?>

    </div>
<?php } ?>

<?php function drawEmailSection($user) { ?>
    <div id="email-section" class="auth-section">
        <form id="editEmailForm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::getInstance()->getCSRFToken()) ?>">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="<?= htmlspecialchars(encodeEmail($user->email ?? '')) ?>" required>
            
            <?php drawSaveBtn("save-btn email"); ?>
        </form>
    </div>

    <hr class="section-divider">
<?php } ?>

<?php function drawPasswordSection($user) { ?>
    <div id="password-section" class="auth-section">
        <form id="editPasswordForm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::getInstance()->getCSRFToken()) ?>">
            <label for="password">Current Password</label>
            <div class="password-wrapper">
                <input type="password" name="password" id="password" required>
                <span class="eye-button" onclick="togglePassword(this, 'password')"><i class="fas fa-eye-slash"></i></span>
            </div>

            <label for="new-password">New Password</label>
            <div class="password-wrapper">
                <input type="password" name="new-password" id="new-password" required>
                <span class="eye-button" onclick="togglePassword(this, 'new-password')"><i class="fas fa-eye-slash"></i></span>
            </div>

            <label for="confirm-password">Confirm Password</label>
            <div class="password-wrapper">
                <input type="password" name="confirm-password" id="confirm-password" required>
                <span class="eye-button" onclick="togglePassword(this, 'confirm-password')"><i class="fas fa-eye-slash"></i></span>
            </div>

            <ul id="password-requirements">
                <li id="min-length"><i class="fa-regular fa-circle-check"></i> At least 8 characters</li>
                <li id="uppercase"><i class="fa-regular fa-circle-check"></i> At least 1 uppercase letter</li>
                <li id="lowercase"><i class="fa-regular fa-circle-check"></i> At least 1 lowercase letter</li>
                <li id="number"><i class="fa-regular fa-circle-check"></i> At least 1 number</li>
                <li id="special-char"><i class="fa-regular fa-circle-check"></i> At least 1 special character</li>
            </ul>

            <?php drawSaveBtn("save-btn password"); ?>
            
        </form>
    </div>
<?php } ?>

<?php function drawDeleteAccountSection() { ?>
    <div class="account-deactivation">
        <h3>Account Deactivation</h3>

        <hr class="section-divider">

        <p><strong>Consequences of deactivating your account</strong></p>
        <ul>
            <li>Your profile and all listed services will be permanently removed from the platform.</li>
            <li>You will lose access to your order history, messages, and client reviews.</li>
            <li>Any pending or active projects will be canceled without payment.</li>
            <li>You won't be able to reactivate the same account or retrieve deleted data.</li>
        </ul>

        <br>
        
        <?php drawReason(); ?>
    
        <button id="deactivate-btn" class="btn danger">Deactivate Account</button>
    </div>
<?php } ?>

<?php function drawReason() { ?>
    <div class="reason">
        <label for="reason">I'm leaving because...</label>
        <select id="reason" name="reason">
            <option disabled selected>Choose a reason</option>

            <optgroup label="Account">
                <option value="duplicate-account">I have another account</option>
                <option value="account-other">Other account-related reason</option>
            </optgroup>

            <optgroup label="Buying">
                <option value="cant-find">I can't find what I need</option>
                <option value="too-complicated">The platform is too complicated</option>
                <option value="negative-experience">Negative experience with sellers</option>
                <option value="buying-other">Other buying-related reason</option>
            </optgroup>

            <option value="something-else">Something else</option>
        </select>
    </div>
<?php } ?>

<?php function drawSaveBtn($id) { ?>
    <div class="modal-buttons">
        <button type="submit" id="<?= htmlspecialchars($id) ?>" class="btn save" disabled>Save</button>
    </div>
<?php } ?>