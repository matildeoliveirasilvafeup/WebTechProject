<?php
session_start();
require '../templates/common/header.php';
?>

<link rel="stylesheet" href="/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<div class="auth-container" data-form-type="signin">
    <h2>Sign In</h2>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="../actions/action_login.php">

        <label for="email">Email</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Password</label>
        <div class="password-wrapper">
            <input type="password" name="password" id="password" required>
            <span class="eye-button" onclick="togglePassword(this)"><i class="fas fa-eye-slash"></i></span>
        </div>

        <button type="submit" id="btn" disabled>Continue</button>
    </form>

    <p class="auth-link">Don't have an account? <a href="register.php">Join here</a></p>
</div>

<script src="/js/password.js"></script>
<script src="/js/form.js"></script>
<script src="https://kit.fontawesome.com/b427850aeb.js" crossorigin="anonymous"></script>

<?php require '../templates/common/footer.php'; ?>
