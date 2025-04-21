<?php
session_start();

$db = new PDO('sqlite:../database/sixerr.db');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user'] = $user;

        header('Location: ../dashboard.php');
        exit;
    } else {
        $error = 'Email or password is incorrect.';
    }
}

require '../templates/common/header.php';
?>

<link rel="stylesheet" href="/css/authentication.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<div class="auth-container" data-form-type="signin">
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

    <p class="auth-link">Don't have an account? <a href="register.php">Join here</a></p>
</div>

<script src="/js/password.js"></script>
<script src="/js/form.js"></script>
<script src="/js/authentication.js"></script>
<script src="https://kit.fontawesome.com/b427850aeb.js" crossorigin="anonymous"></script>

<?php require '../templates/common/footer.php'; ?>
