<?php
declare(strict_types=1);
require_once(__DIR__ .  '/../database/service.class.php');
?>

<?php
function renderServiceSlider(array $services, int $minItemsToShowNav = 6, string $sliderId = 'servicesSlider'): string {
    if (empty($services)) return '';

    ob_start();
    ?>
    <div class="services-slider-wrapper">
        <?php if (count($services) > $minItemsToShowNav): ?>
            <button class="slider-btn left" onclick="scrollSlider(this, -1)">‹</button>
        <?php endif; ?>    

        <div class="services-slider" id="<?= htmlspecialchars($sliderId) ?>">
            <?php foreach ($services as $service): ?>
                <a href="service.php?id=<?= $service->id ?>" class="service-card">
                    <img src="<?= htmlspecialchars($service->mediaUrl ?? 'https://via.placeholder.com/300') ?>" alt="Service image">
                    <div class="service-info">
                        <h3><?= htmlspecialchars($service->title) ?></h3>
                        <p class="freelancer">By <?= htmlspecialchars($service->freelancerName) ?></p>
                        <p class="price">€<?= number_format($service->price, 2) ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (count($services) > $minItemsToShowNav): ?>
            <button class="slider-btn right" onclick="scrollSlider(this, 1)">›</button>
        <?php endif; ?>    
    </div>
    <script src="../js/slider.js"></script>
    <?php
    return ob_get_clean();
}
?>

<?php function drawServicePage($service, $ratingInfo) { ?>
    <div class="service-page">  
        <img src="<?= htmlspecialchars($service->mediaUrl ?? 'https://via.placeholder.com/480') ?>" alt="Imagem do serviço">

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

<?php function drawListServicesForm() { ?>
    <section class="service-page">
     <form action="create_listing.php" method="POST" enctype="multipart/form-data" class="create-form">
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
                     <option value="ai_services">AI Services</option>
                     <option value="business">Business</option>
                     <option value="consulting">Consulting</option>
                     <option value="digital_marketing">Digital Marketing</option>
                     <option value="graphics_design">Graphic & Design</option>
                     <option value="music_audio">Music & Audio</option>
                     <option value="programming_tech">Programming & Tech</option>
                     <option value="video_animation">Video & Animation</option>
                     <option value="writing_translation">Writing & Translation</option>
                     <option value="other">Other</option>
                 </select>
             </div>        
 
             <div>
                 <label for="revisions">Included Revisions</label>
                 <input type="number" id="revisions" name="revisions" min="0" step="1" required>    
             </div>    
         </div>
 
         <label for="image">Image</label>
         <input type="file" id="image" name="image" accept="image/*" required>
 
         <div class="button-group">
             <button type="submit" class="btn-hire">Publish</button>
             <a href="index.php" class="btn-add-cart">Cancel</a>
         </div>
     </form>
 </section>
<?php } ?>