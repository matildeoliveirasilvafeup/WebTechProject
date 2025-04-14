<?php
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Fake login for testing
    if ($email === 'user@example.com' && $password === 'password123') {
        $_SESSION['user'] = $email;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid email or password.';
    }
}

require 'includes/header.php';
?>

<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<div class="login-container">
    <h2>Sign In</h2>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="login.php">

        <label for="email">Email</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Password</label>
        <div class="password-wrapper">
            <input type="password" name="password" id="password" required>
            <span class="eye-button" onclick="togglePassword(this)"><i class="fas fa-eye-slash"></i></span>
        </div>

        <button type="submit" id="btn" disabled>Continue</button>
    </form>

    <p class="signup-link">Don't have an account? <a href="register.php">Join here</a></p>
</div>

<script src="scripts/script.js"></script>
<script src="https://kit.fontawesome.com/b427850aeb.js" crossorigin="anonymous"></script>

<?php require 'includes/footer.php'; ?>
