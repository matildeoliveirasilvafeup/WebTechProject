<?php
declare(strict_types=1);
?>

<?php function drawHero() { ?>
    <div class="hero">
        <h1 id="typing-effect">Find the perfect freelancer for your project</h1>
        <form method="GET" action="search.php">
            <input type="text" id="search-service-input" placeholder="Search services...">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>  
<?php } ?>

<?php function drawHowItWorks() { ?>
    <div class="how-it-works">
        <h2>How It Works</h2>
        <div class="steps">
            <div class="step">
                <i class="fas fa-user-plus"></i>
                <h3>Create an Account</h3>
                <p>Sign up for free and create your profile as a freelancer or client.</p>
            </div>
            <div class="step">
                <i class="fas fa-search"></i>
                <h3>Find or Offer Services</h3>
                <p>Browse through categories or publish your own service with ease.</p>
            </div>
            <div class="step">
                <i class="fas fa-handshake"></i>
                <h3>Work & Get Paid</h3>
                <p>Collaborate, deliver quality work, and complete transactions securely.</p>
            </div>
        </div>
    </div>
<?php } ?>

<?php function drawFinalCTA() { ?>
    <div class="final-cta">
        <h2>Ready to join our freelance marketplace?</h2>
        <p>Whether you're here to offer your talent or find it, you're just a click away.</p>
        <div class="cta-buttons">
            <a href="register.php" class="cta-btn primary">Join Now</a>
            <a href="search.php" class="cta-btn secondary">Browse Services</a>
        </div>
    </div>
<?php } ?>