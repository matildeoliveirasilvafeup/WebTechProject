<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/database.php';
session_start();

$db = Database::getInstance();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $db->prepare("INSERT INTO users (name, username, email, password_hash) 
                              VALUES (:name, :username, :email, :password_hash)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password_hash', $password_hash);
        $stmt->execute();

        $user_id = $db->lastInsertId();

        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $_SESSION['user'] = $user;

        header("Location: /index.php");
        exit;

    } catch (PDOException $e) {
        $error = 'Error registering: ' . $e->getMessage();
    }
}

require '../templates/common/header.php';
?>

<link rel="stylesheet" href="/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css">

<div class="auth-container" data-form-type="signup">
    <h2>Create an Account</h2>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="register.php">

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
            <span class="eye-button" onclick="togglePassword(this)"><i class="fas fa-eye-slash"></i></span>
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
<script src="/js/form.js"></script>
<script src="https://kit.fontawesome.com/b427850aeb.js" crossorigin="anonymous"></script>

<?php require '../templates/common/footer.php'; ?>
