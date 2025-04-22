<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sixerr</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="icon" type="image/png" href="/images/sixerr_favicon.png">
    <link rel="stylesheet" href="/css/header.css">
    <link rel="stylesheet" href="/css/footer.css">
    <link rel="stylesheet" href="/css/home_page.css">
    <link rel="stylesheet" href="/css/typing_effect.css">
    <link rel="stylesheet" href="/css/category_menu.css">
    <link rel="stylesheet" href="/css/service_cards_slider.css">
    <link rel="stylesheet" href="/css/service_page.css">
    <link rel="stylesheet" href="/css/authentication.css">
    <link rel="stylesheet" href="/css/color_scheme.css">
    <link rel="stylesheet" href="/css/toast_message.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
<header>
    <div class="logo">
    <a href="/index.php">
        <img src="/images/sixerr_logo.png" alt="Sixerr logo">
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
