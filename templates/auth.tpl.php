<?php
declare(strict_types=1);
?>

<?php function drawRegister(string $error = ''): void { ?>
    <div class="auth-container" data-form-type="signup">
        <h2>Create an Account</h2>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" action="../actions/action_register.php">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::getInstance()->getCSRFToken()) ?>">

            <label for="name">Name</label>
            <input type="name" name="name" id="name" required>
            
            <label for="username">Username</label>
            <input type="username" name="username" id="username" required>
            <span id="username-status" class="validation-msg"></span>

            <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="user@example.com" required>
            <span id="email-status" class="validation-msg"></span>

            <label for="password">Password</label>
            <div class="password-wrapper">
                <input type="password" name="password" id="password" required>
                <span class="eye-button" onclick="togglePassword(this, 'password')"><i class="fas fa-eye-slash"></i></span>
            </div>

            <ul id="password-requirements">
                <li id="min-length"><i class="fa-regular fa-circle-check"></i> At least 8 characters</li>
                <li id="uppercase"><i class="fa-regular fa-circle-check"></i> At least 1 uppercase letter</li>
                <li id="lowercase"><i class="fa-regular fa-circle-check"></i> At least 1 lowercase letter</li>
                <li id="number"><i class="fa-regular fa-circle-check"></i> At least 1 number</li>
                <li id="special-char"><i class="fa-regular fa-circle-check"></i> At least 1 special character</li>
            </ul>

            <button type="submit" id="btn" disabled>Create Account</button>
        </form>

        <p class="auth-link">Already have an account? <a href="login.php">Sign in</a></p>
    </div>
    <script src="/js/password.js"></script>
    <script type="module" src="/js/form.js"></script>
    <script src="https://kit.fontawesome.com/b427850aeb.js" crossorigin="anonymous"></script>
<?php } ?>

<?php function drawLogin(string $error = ''): void { ?>
    <div class="auth-container" data-form-type="signin">
        <h2>Sign In</h2>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" action="../actions/action_login.php">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::getInstance()->getCSRFToken()) ?>">

            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Password</label>
            <div class="password-wrapper">
                <input type="password" name="password" id="password" required>
                <span class="eye-button" onclick="togglePassword(this, 'password')"><i class="fas fa-eye-slash"></i></span>
            </div>

            <button type="submit" id="btn" disabled>Continue</button>
        </form>

        <p class="auth-link">Don't have an account? <a href="register.php">Join here</a></p>
    </div>

    <script src="/js/password.js"></script>
    <script type="module" src="/js/form.js"></script>
    <script src="https://kit.fontawesome.com/b427850aeb.js" crossorigin="anonymous"></script>
<?php } ?>

<?php function drawAuthPageStart() { ?>
    <div class="auth-page-wrapper">
<?php } 

function drawAuthPageEnd() { ?>
    </div>
<?php } ?>
