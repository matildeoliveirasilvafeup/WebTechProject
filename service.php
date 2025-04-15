<?php
    require_once 'database/connection.php';
    require_once 'database/services.php';
    require_once 'database/categories.php';
    require_once 'database/reviews.php';

    $serviceId = $_GET['id'] ?? null;

    if (!$serviceId || !is_numeric($serviceId)) {
        header("Location: index.php");
        exit;
    }

    require 'templates/common/header.php';
    require 'templates/category-menu.php';
    require 'templates/reviews_stars.php';

    $service = getServiceById($db, (int)$serviceId);
    $ratingInfo = getServiceRatingInfo($db, (int)$serviceId);

    if (!$service) {
        echo "Service not found.";
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?= htmlspecialchars($service['title']) ?> | Serviços</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div class="service-page">  
            <img src="<?= htmlspecialchars($service['media_url'] ?? 'https://via.placeholder.com/480') ?>" alt="Imagem do serviço">

            <div class="service-details">
                <h1><?= htmlspecialchars($service['title']) ?></h1>
                <div class="freelancer-box">
                    <img src="<?= htmlspecialchars($service['profile_picture'] ?? 'https://via.placeholder.com/50') ?>" alt="Foto do freelancer">
                    <p class="freelancer">
                        By <strong><?= htmlspecialchars($service['freelancer_name']) ?></strong><br>
                        <?php if ($ratingInfo['avg']): ?>
                            <?= renderStars($ratingInfo['avg']) ?>
                            <?= $ratingInfo['avg'] ?> (<?= $ratingInfo['count'] ?>)
                        <?php else: ?>
                            No reviews yet
                        <?php endif; ?>
                    </p>
                </div>
                <p class="price">€<?= number_format($service['price'], 2) ?></p>
                <div class="description">
                    <?= nl2br(htmlspecialchars($service['description'])) ?>
                </div>
                <div class="button-group">
                    <a href="contact_freelancer.php?id=<?= $service['freelancer_id'] ?>" class="btn-hire">Contact</a>
                    <a href="#" class="btn-add-cart">Add to Cart</a>
                </div>
            </div>
        </div>
    </body>
</html>

<?php require 'templates/common/footer.php'; ?>