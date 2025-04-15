<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: authentication/login.php');
    exit;
}

require 'templates/common/header.php';
?>

<link rel="stylesheet" href="/css/style.css">

<div class="login-container">
    <h2>Hello, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h2>
    <p>Welcome to your user dashboard.</p>
</div>

<?php require 'templates/common/footer.php'; ?>
