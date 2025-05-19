<?php
declare(strict_types=1);
require_once(__DIR__ .  '/../database/service.class.php');

function renderServiceCard(Service $service, bool $isDashboard = false, bool $isOwner = false) {
    $imageUrl = !empty($service->mediaUrls) ? reset($service->mediaUrls) : 'https://via.placeholder.com/300';
?>
    <?php if ($isDashboard) { ?>
        <div class="dashboard-card">
    <?php } ?>

    <a href="service.php?id=<?= $service->id ?>" class="service-card">
        <img src="<?= htmlspecialchars($imageUrl) ?>" alt="Service image">

    <?php if ($isDashboard) { ?>
        </a>
    <?php } ?>
        <div class="service-info">
            <h3><?= htmlspecialchars($service->title) ?></h3>
            <p class="freelancer">By <?= htmlspecialchars($service->freelancerName) ?></p>
            <div class="price-actions">
                <p class="price">€<?= number_format($service->price, 2) ?></p>

                <?php if ($isOwner): ?>
                <div class="card-actions">
                    <a href="list_service.php?id=<?= $service->id ?>" class="icon-btn" title="Edit" onclick="event.stopPropagation();">
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                    <form method="POST" action="../actions/action_delete_service.php" class="inline-form" onsubmit="return confirm('Are you sure you want to delete this service?');">
                        <input type="hidden" name="id" value="<?= $service->id ?>">
                        <button type="submit" class="icon-btn delete" title="Delete" onclick="event.stopPropagation();">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>
    <?php if ($isDashboard) { ?>
        </div>
    <?php } else { ?>
        </a>
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

<?php function drawServiceGrid(array $services, bool $isDashboard = false, bool $isFavorites = false) {
    if (empty($services)) return;

    if ($isDashboard) { ?>
        <section class="dashboard-grid">
    <?php } else { ?>
        <section class="services-grid">
    <?php }
        foreach ($services as $service):
            renderServiceCard($service, $isDashboard, !$isFavorites);
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
                    <?php if (!empty($service->profilePicture)): ?>
                        <img src="<?= htmlspecialchars($service->profilePicture) ?>" alt="Foto do freelancer">
                    <?php else: ?>
                        <i class="fa-solid fa-image-portrait"></i>
                    <?php endif; ?>

                    <p class="freelancer">
                        By <strong><?= renderUserLink($service->freelancerUsername, $service->freelancerName) ?></strong><br>
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
                    <a href="#" class="btn-hire" onclick="startConversation(<?= $service->id ?>, <?= Session::getInstance()->getUser()->id ?>, <?= $service->freelancerId ?>)">Contact</a>
                    <a href="#" class="btn-add-cart" onclick="
                        startConversation(<?= $service->id ?>, <?= Session::getInstance()->getUser()->id ?>, <?= $service->freelancerId ?>);
                        createHiring(<?= $service->id ?>, <?= Session::getInstance()->getUser()->id ?>, <?= $service->freelancerId ?>);
                        sendStatusMessage(event, 'Pending', <?= Session::getInstance()->getUser()->id ?>, <?= $service->freelancerId ?>, '<?= htmlspecialchars($service->title, ENT_QUOTES) ?>');
                        ">Hire
                    </a>
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
    <script src="/js/chat.js"></script>
    <script src="/js/hirings.js"></script>
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

<?php function drawListServicesForm($categories, ?Service $service = null) { ?>
    <section class="service-page" id="<?= $service ? 'edit_service' : 'new_service' ?>">
        <form action="<?= $service ? '/actions/action_edit_service.php' : '/actions/action_list_service.php' ?>"
              method="POST" enctype="multipart/form-data" class="create-form">
            <h1><?= $service ? 'Edit Service' : 'List New Service' ?></h1>

            <?php if ($service): ?>
                <input type="hidden" name="id" value="<?= $service->id ?>">
            <?php endif; ?>

            <label for="title">Service Title</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($service->title ?? '') ?>" required>

            <label for="description">Description</label>
            <textarea id="description" name="description" rows="6" required><?= htmlspecialchars($service->description ?? '') ?></textarea>

            <div class="form-grid">
                <div>
                    <label for="price">Price (€)</label>
                    <input type="number" id="price" name="price" min="0" step="0.01"
                           value="<?= htmlspecialchars(number_format((float)($service->price ?? 0), 2, '.', '')) ?>" required>
                </div>

                <div>
                    <label for="delivery">Delivery Time (in days)</label>
                    <input type="number" id="delivery" name="delivery" min="0" step="1"
                           value="<?= $service->deliveryTime ?? '' ?>" required>
                </div>

                <div>
                    <label for="category">Category</label>
                    <select id="category" name="category" required>
                        <option value="" disabled <?= $service ? '' : 'selected' ?>>Select a category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars((string)$category->id) ?>"
                                    data-subcategories='<?= json_encode($category->subcategories ?? []) ?>'
                                    <?= $service && $service->categoryId == $category->id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="subcategory">Subcategory</label>
                    <select id="subcategory" name="subcategory" required <?= $service ? '' : 'disabled' ?>>
                        <option value="" disabled selected>Select a subcategory</option>
                        <?php if ($service && $service->subcategoryId): ?>
                            <option value="<?= htmlspecialchars((string)$service->subcategoryId) ?>" selected>
                                <?= htmlspecialchars((string)$service->subcategoryName) ?>
                            </option>
                        <?php endif; ?>
                    </select>
                </div>

                <div>
                    <label for="revisions">Included Revisions</label>
                    <input type="number" id="revisions" name="revisions" min="0" step="1"
                           value="<?=$service->numberOfRevisions ?? '' ?>" required>
                </div>

                <div>
                    <label for="language">Language</label>
                    <input type="text" id="language" name="language"
                           value="<?= htmlspecialchars($service->language ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <?php if ($service && !empty($service->mediaUrls)): ?>
                    <h3>Old Media</h3>
                    <p>Select the media files to remove.</p>
                    <div id="old-media-preview" class="file-preview">
                        <?php foreach ($service->mediaUrls as $media): ?>
                            <?php $isImage = !preg_match('/\.(mp4|webm)$/i', $media); ?>
                            <div class="file-item" data-media-url="<?= htmlspecialchars($media) ?>" data-is-image="<?= $isImage ? 'true' : 'false' ?>">
                                <?php if (!$isImage): ?>
                                    <video controls>
                                        <source src="<?= htmlspecialchars($media) ?>" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                <?php else: ?>
                                    <img src="<?= htmlspecialchars($media) ?>" alt="Service media">
                                <?php endif; ?>
                                <div class="file-actions">
                                    <input type="checkbox" name="delete_media[]" value="<?= htmlspecialchars($media) ?>" id="delete-<?= htmlspecialchars(basename($media)) ?>">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <h3><?= $service ? 'Add New Media' : 'Add Media' ?></h3>
                <label for="images">Images and Videos</label>
                <input type="file" id="images" name="images[]" accept="image/*,video/*" multiple>
                <div id="new-media-preview" class="file-preview"></div>
            </div>

            <div class="button-group">
                <button type="submit" class="btn-add-cart"><?= $service ? 'Update' : 'Publish' ?></button>
                <a href="/index.php" class="btn-hire">Cancel</a>
            </div>
        </form>
    </section>

    <script src="../js/list_service.js"></script>
<?php } ?>