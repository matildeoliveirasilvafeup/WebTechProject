<?php
function renderServiceSlider(array $services, int $minItemsToShowNav = 6): string {
    if (empty($services)) return '';

    ob_start();
    ?>
    <div class="services-slider-wrapper">
        <?php if (count($services) > $minItemsToShowNav): ?>
            <button class="slider-btn left" onclick="scrollSlider(-1)">‹</button>
        <?php endif; ?>    

        <div class="services-slider" id="servicesSlider">
            <?php foreach ($services as $service): ?>
                <a href="service.php?id=<?= $service['id'] ?>" class="service-card">
                    <img src="<?= htmlspecialchars($service['media_url'] ?? 'https://via.placeholder.com/300') ?>" alt="Service image">
                    <div class="service-info">
                        <h3><?= htmlspecialchars($service['title']) ?></h3>
                        <p class="freelancer">By <?= htmlspecialchars($service['freelancer_name']) ?></p>
                        <p class="price">€<?= number_format($service['price'], 2) ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (count($services) > $minItemsToShowNav): ?>
            <button class="slider-btn right" onclick="scrollSlider(1)">›</button>
        <?php endif; ?>    
    </div>
    <?php
    return ob_get_clean();
}
