<?php 
require_once __DIR__ . '/service.tpl.php';
function drawFavorites($favorites) { ?>
    <div class="tab-content" id="favorites">
        <div class="favorites-details">            
            <?php if (empty($favorites)): ?>
                <p class="no-favorites">You don't have any favorites yet.</p>
            <?php else:
                drawServiceGrid($favorites, true);
            endif; ?>
        </div>
    </div>
<?php } ?>
