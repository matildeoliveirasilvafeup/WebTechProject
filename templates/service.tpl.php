<?php
declare(strict_types=1);
require_once(__DIR__ .  '/../database/service.class.php');

function renderServiceCard(Service $service, bool $isDashboard = false) {
    $imageUrl = !empty($service->mediaUrls) ? reset($service->mediaUrls) : 'https://via.placeholder.com/300';
?>
    <?php if ($isDashboard) { ?>
        <div class="favorite-card">
    <?php } ?>

    <a href="service.php?id=<?= $service->id ?>" class="service-card">    
        <img src="<?= htmlspecialchars($imageUrl) ?>" alt="Service image">
        <div class="service-info">
            <h3><?= htmlspecialchars($service->title) ?></h3>
            <p class="freelancer">By <?= htmlspecialchars($service->freelancerName) ?></p>
            <p class="price">€<?= number_format($service->price, 2) ?></p>
        </div>
    </a>

    <?php if ($isDashboard) { ?>
        </div>
    <?php } ?>
<?php } ?>
<?php function renderServiceSlider(array $services, int $minItemsToShowNav = 6, string $sliderId = 'servicesSlider') {
    if (empty($services)) return;
?>
    <div class="services-slider-wrapper">
        <?php if (count($services) > $minItemsToShowNav): ?>
            <button class="slider-btn left" onclick="scrollSlider(this, -1)">‹</button>
        <?php endif; ?>    

        <div class="services-slider" id="<?= htmlspecialchars($sliderId) ?>">
            <?php foreach ($services as $service):
                renderServiceCard($service);
            endforeach; ?>
        </div>

        <?php if (count($services) > $minItemsToShowNav): ?>
            <button class="slider-btn right" onclick="scrollSlider(this, 1)">›</button>
        <?php endif; ?>    
    </div>
    <script src="../js/slider.js"></script>
<?php } ?>

<?php function drawServiceGrid(array $services, bool $isDashboard = false) {
    if (empty($services)) return;

    if ($isDashboard) { ?>
        <section class="favorites-grid">
    <?php } else { ?>    

        <section class="services-grid">
    <?php } 
        foreach ($services as $service):
            renderServiceCard($service, $isDashboard);
        endforeach; ?>
    </section>
<?php } ?>

<?php function drawServicePage($service, $ratingInfo) { ?>
    <div class="service-page">
        <div class="media-carousel">
            <div class="carousel-wrapper">
                <?php if (empty($service->mediaUrls) || (count(array_filter($service->mediaUrls)) === 0)): ?>
                    <p>No media.</p>
                <?php else: ?>
                    <?php foreach ($service->mediaUrls as $media): ?>
                        <?php if (empty($media)) continue; ?>
                        <?php if (preg_match('/\.(mp4|webm)$/i', $media)): ?>
                            <video controls>
                                <source src="<?= htmlspecialchars($media) ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        <?php else: ?>
                            <img src="<?= htmlspecialchars($media) ?>" alt="Service media">
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <span class="favorite-icon" onclick="toggleFavorite(this, <?= $service->id ?>)">
                        <i class="<?= Favorite::isFavorite($service->id) ? 'fa-solid fa-heart' : 'fa-regular fa-heart' ?>"></i>
                    </span>
                <?php endif; ?>
            </div>

            <button class="carousel-btn left" onclick="scrollMedia(-1)">‹</button>
            <button class="carousel-btn right" onclick="scrollMedia(1)">›</button>
        </div>
        
        <div class="service-details">
            <h1><?= htmlspecialchars($service->title) ?></h1>
            <div class="freelancer-box">
                <img src="<?= htmlspecialchars($service->profilePicture ?? 'https://via.placeholder.com/50') ?>" alt="Foto do freelancer">
                <p class="freelancer">
                    By <strong><?= htmlspecialchars($service->freelancerName) ?></strong><br>
                    <?php if ($ratingInfo['avg']): ?>
                        <?= renderStars($ratingInfo['avg']) ?>
                        <?= $ratingInfo['avg'] ?> (<?= $ratingInfo['count'] ?> reviews)
                    <?php else: ?>
                        No reviews yet
                    <?php endif; ?>
                </p>
            </div>
            <p class="price">€<?= number_format($service->price, 2) ?></p>
            <div class="description">
                <?= nl2br(htmlspecialchars($service->description)) ?>
            </div>
            <div class="button-group">
                <a href="contact_freelancer.php?id=<?= $service->freelancerId ?>" class="btn-hire">Contact</a>
                <a href="#" class="btn-add-cart">Add to Cart</a>
            </div>
        </div>

        <div class="info-actions">
            <button class="icon-btn favorite">
                <i class="fas fa-heart"></i>
                <span><?= $service->favoritesCount?></span>
            </button>
            <button class="icon-btn share" onclick="shareService()">
                <i class="fas fa-share-alt"></i>
            </button>
            <a href="report_service.php?id=<?= $service->id ?>" class="icon-btn report" title="Report this service">
                <i class="fas fa-flag"></i>
            </a>
        </div>

        <div class="service-info">
            <h3>Service Information</h3>
            <ul>
                <li><i class="fas fa-clock"></i><strong> Delivery: </strong> <?= $service->deliveryTime ?> days</li>
                <li><i class="fas fa-tags"></i><strong> Category: </strong> <?= htmlspecialchars($service->categoryName) ?></li>
                <li><i class="fas fa-sync-alt"></i><strong> Included Revisions: </strong> Until <?= $service->numberOfRevisions ?> revisions</li>
                <li><i class="fas fa-language"></i><strong> Language: </strong> <?= htmlspecialchars($service->language) ?></li>
            </ul>
        </div>
    </div>

    <script src="../js/favorite.js"></script>
    <script src="../js/media_scroll.js"></script>
<?php } ?>

<?php function drawFeaturedServices($featuredServices) { ?>
    <div class="featured-services">
        <h2>Featured Services</h2>
        <?= renderServiceSlider($featuredServices) ?>
    </div>
<?php } ?>

<?php function drawMoreFromFreelancer($service, $moreFromFreelancer) { ?>
    <?php if (!empty($moreFromFreelancer)): ?>
        <div class="freelancer-services">
            <h2>More Services from <?= htmlspecialchars($service->freelancerName) ?></h2>
            <?= renderServiceSlider($moreFromFreelancer, 4, 'freelancerSlider') ?>
        </div>
    <?php endif; ?>
<?php } ?>

<?php function drawRelatedServices($relatedServices) { ?>
    <?php if (!empty($relatedServices)): ?>
        <div class="freelancer-services">
            <h2>You may also like: </h2>
            <?= renderServiceSlider($relatedServices, 4, 'relatedSlider') ?>
        </div>
    <?php endif; ?>
<?php } ?>

<?php function drawListServicesForm($categories) { ?>
    <section class="service-page" id="new_service">
        <form action="/actions/action_list_service.php" method="POST" enctype="multipart/form-data" class="create-form">
            <h1>List New Service</h1>

            <label for="title">Service Title</label>
            <input type="text" id="title" name="title" required>

            <label for="description">Description</label>
            <textarea id="description" name="description" rows="6" required></textarea>

            <div class="form-grid">
                <div>
                    <label for="price">Price (€)</label>
                    <input type="number" id="price" name="price" min="0" step="0.01" required>
                </div>

                <div>
                    <label for="delivery">Delivery Time (in days)</label>
                    <input type="number" id="delivery" name="delivery" min="0" step="1" required>
                </div>

                <div>
                    <label for="category">Category</label>
                    <select id="category" name="category" required>
                        <option value="" disabled selected>Select a category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars((string)$category->id) ?>"
                                    data-subcategories='<?= json_encode($category->subcategories ?? []) ?>'>
                                <?= htmlspecialchars($category->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="subcategory">Subcategory</label>
                    <select id="subcategory" name="subcategory" required disabled>
                        <option value="" disabled selected>Select a subcategory</option>
                    </select>
                </div>

                <div>
                    <label for="revisions">Included Revisions</label>
                    <input type="number" id="revisions" name="revisions" min="0" step="1" required>
                </div>

                <div>
                    <label for="language">Language</label>
                    <input type="text" id="language" name="language">
                </div>
            </div>

            <div class="form-group">
                <label for="images">Images and Videos</label>
                <input type="file" id="images" name="images[]" accept="image/*,video/*" multiple>
            </div>

            <div id="file-preview" class="file-preview"></div>

            <div class="button-group">
                <button type="submit" class="btn-add-cart">Publish</button>
                <a href="/index.php" class="btn-hire">Cancel</a>
            </div>
        </form>
    </section>

    <script src="../js/list_service.js"></script>
<?php } ?>