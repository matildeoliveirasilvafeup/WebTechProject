<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sixerr</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="icon" type="image/png" href="/images/sixerr_logo_tab.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
<header>
    <div class="logo">
    <a href="/index.php">
        <img src="/images/sixerr_logo.png" alt="Sixerr logo" height="100">
    </a>
</div>

    <nav>
        <?php if (!isset($_SESSION['user'])): ?>
            <a href="/authentication/login.php">Login</a>
            <a href="/authentication/register.php">Register</a>
        <?php else: ?>
            <a href="/dashboard.php">Dashboard</a>
            <a href="/logout.php">Logout</a>
        <?php endif; ?>
    </nav>
</header>
<main>
