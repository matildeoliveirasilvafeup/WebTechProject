<?php
session_start();

if (!isset($_SESSION['user']['id'])) {
    header('Location: authentication/login.php');
    exit;
}

require 'templates/common/header.php';
?>

<link rel="stylesheet" href="css/dashboard.css">

<div class="dashboard">
    <div class="sidebar">
        <h3><i class="fa-solid fa-bars"></i><span>Menu</span></h3>
        <ul class="menu-content">
            <li><a href="#" class="tab-link active" data-tab="personal-details"><i class="fa-solid fa-user"></i><span>Personal Details</span></a></li>
            <li><a href="#" class="tab-link" data-tab="favorites"><i class="fa-solid fa-star"></i><span>Favorites</span></a></li>
            <li><a href="#" class="tab-link" data-tab="listings"><i class="fa-solid fa-clipboard"></i><span>Own Listings</span></a></li>            
            <li><a href="#" class="tab-link" data-tab="settings"><i class="fa-solid fa-gear"></i><span>Settings</span></a></li>            
            <li class="logout"><a href="/authentication/logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i><span>Logout</span></a></li>
        </ul>
    </div>
    
    <div class="dashboard-content">
        <div id="personal-details" class="tab-content active">
            <h2>Hello, <?= htmlspecialchars($_SESSION['user']['name']) ?>!</h2>
            <p>Welcome to your user dashboard. Here are your personal details:</p>
        </div>
        
        <div id="favorites" class="tab-content">
            <h2>Your Favorites</h2>
            <p>Here's a list of your favorite services or listings.</p>
        </div>
        
        <div id="listings" class="tab-content">
            <h2>Your Listings</h2>
            <p>Manage your own posted services or offers here.</p>
        </div>
    </div>
</div>

<script src="scripts/dashboard.js" defer></script>
<script src="https://kit.fontawesome.com/b427850aeb.js" crossorigin="anonymous"></script>

<?php require 'templates/common/footer.php'; ?>
