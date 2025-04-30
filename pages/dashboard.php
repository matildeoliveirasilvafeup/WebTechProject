<?php
declare(strict_types=1);

require_once __DIR__ . '/../database/user.class.php'; 

require_once __DIR__ . '/../includes/session.php';

$session = Session::getInstance();

if (!$session || !$session->getUser()) {
    header('Location: login.php');
    exit;
}

require '../templates/common/header.php';
?>

<link rel="stylesheet" href="/css/style.css">

<div class="login-container">
    <h2>Hello, <?= htmlspecialchars($session->getUser()->getName()) ?>!</h2>
    <p>Welcome to your user dashboard.</p>
</div>

<?php require '../templates/common/footer.php'; ?>
