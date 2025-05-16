<?php 
declare(strict_types=1);

require_once(__DIR__ . '/../../includes/session.php');
?>

<?php function drawHeader() { ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sixerr</title>
        <link rel="icon" type="image/png" href="/images/sixerr_favicon.png">
        <link rel="stylesheet" href="/css/main.css">
    </head>
    <body>
    <header>
        <div class="logo">
        <a href="/index.php">
            <img src="/images/sixerr_logo.png" alt="Sixerr logo">
        </a>
    </div>

        <nav>
            <?php if (Session::getInstance()->getUser()) drawMoreOptions(); else drawLoginOptions(); ?>       
        </nav>
    </header>
    <main>
<?php } ?>

<?php function drawLoginOptions() { ?>
    <a href="/pages/login.php">Login</a>
    <a href="/pages/register.php">Register</a>
<?php } ?>

<?php function drawMoreOptions() { ?>
    <a href="/pages/dashboard.php">
        <i class="fas fa-user-circle"></i> Dashboard
    </a>
    <a href="/pages/list_service.php">List Service</a>
<?php } ?>        