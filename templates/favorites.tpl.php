<?php function drawFavorites($user, $favorites) { ?>
    <div class="tab-content" id="favorites">
        <div class="favorites-details">            
            <?php if (empty($favorites)): ?>
                <p class="no-favorites">You don't have any favorites yet.</p>
            <?php else: ?>
                <div class="favorites-grid">
                    <?php foreach ($favorites as $serviceId): ?>
                        <div class="favorite-card">
                            <?php renderServiceCard(Service::getById($serviceId)) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php } ?>
