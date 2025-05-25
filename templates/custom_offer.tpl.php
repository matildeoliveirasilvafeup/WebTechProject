<?php function drawCustomOfferForm(int $hiringId, int $senderId, int $receiverId, int $serviceId): void { ?>
    <?php
        $offers = CustomOffer::getOffers($hiringId, $senderId, $receiverId);

        $session = Session::getInstance();
        $user = $session->getUser();
        $userId = $user->id;
    ?>

    <section class="custom-offer-list">
        <h2>Custom Offers</h2>

        <?php if (empty($offers)) : ?>
            <p>No custom offers have been made yet.</p>
        <?php endif; ?>

        <ul class="offer-cards">
            <?php foreach ($offers as $offer) : ?>
            <li class="offer-card">

                <?php
                    drawOfferLeft($offer);

                    drawOfferRight($userId, $offer, $hiringId, $senderId, $receiverId, $serviceId);
                ?>
                
            </li>
            <?php endforeach; ?>
        </ul>
        
        <?php drawCustomOfferButton($serviceId, $hiringId); ?>
        
    </section>

    <div id="custom-offer-modal" class="custom-offer-modal hidden">
        <?php drawCustomOfferModal($hiringId, $serviceId, $senderId, $receiverId); ?>
    </div>

    <script type="module" src="/js/custom_offer.js" defer></script>
<?php } ?>

<?php function drawOfferLeft($offer) { ?>
    <div class="offer-left">
        <p><strong>Price: </strong>€<?= htmlspecialchars($offer->price) ?></p>
        <p><strong>Delivery: </strong><?= htmlspecialchars($offer->delivery_time) ?> days</p>
        <p><strong>Revisions: </strong><?= htmlspecialchars($offer->number_of_revisions) ?></p>
    </div>
<?php } ?>

<?php function drawOfferRight($userId, $offer, $hiringId, $senderId, $receiverId, $serviceId) { ?>
    <div class="offer-right">
        <div class="status-createdAt-badge">
            <div class="css-status-badge-corrector">
                <span class="status-badge status-<?= strtolower(htmlspecialchars($offer->status)) ?>">
                    <?= htmlspecialchars($offer->status) ?>
                </span>
            </div>
            <span 
                class="createdAt-badge"
                data-datetime="<?= htmlspecialchars($offer->created_at) ?>">
                <?= htmlspecialchars(date('Y-m-d H:i', strtotime($offer->created_at))) ?>
            </span>
        </div>

        <div class="offer-actions">
            <?php if (strtolower($offer->status) === 'pending'): ?>
                <?php if ($userId === $offer->sender_id): ?>
                    <form onsubmit="return false;">
                        <input type="hidden" name="offer_id" value="<?= htmlspecialchars($offer->id) ?>">
                        <input type="hidden" name="hiring_id" value="<?= htmlspecialchars($hiringId) ?>">
                        <input type="hidden" name="new_status" value="Cancelled">
                        <button type="submit" class="cancel-btn" onclick="updateOfferStatus('Cancelled', <?= $offer->id ?>, <?= $hiringId ?>, <?= $senderId ?>, <?= $receiverId ?>, <?= $serviceId ?>)">Cancel</button>
                    </form>
                <?php elseif ($userId === $offer->receiver_id): ?>
                    <form onsubmit="return false;">
                        <input type="hidden" name="offer_id" value="<?= htmlspecialchars($offer->id) ?>">
                        <input type="hidden" name="hiring_id" value="<?= htmlspecialchars($hiringId) ?>">
                        <button type="submit" name="new_status" value="Accepted" class="accept-btn" onclick="updateOfferStatus('Accepted', <?= $offer->id ?>, <?= $hiringId ?>, <?= $senderId ?>, <?= $receiverId ?>, <?= $serviceId ?>)">Accept</button>
                        <button type="submit" name="new_status" value="Rejected" class="reject-btn" onclick="updateOfferStatus('Rejected', <?= $offer->id ?>, <?= $hiringId ?>, <?= $senderId ?>, <?= $receiverId ?>, <?= $serviceId ?>)">Reject</button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
<?php } ?>

<?php function drawCustomOfferButton($serviceId, $hiringId) { ?>
    <div class="custom-offer-button">
        <?php
            $hasAcceptedOffer = CustomOffer::checkHiringOffersStatus($hiringId) === 'Accepted';
        ?>
        <button class="create-offer-btn" <?= $hasAcceptedOffer ? 'disabled data-accepted-exists="true"' : '' ?>>New Offer</button>
        <button class="go-to-service-btn" onclick="location.href='/pages/service.php?id=<?= htmlspecialchars($serviceId) ?>'">Go to original service</button>
    </div>
<?php } ?>

<?php function drawCustomOfferModal($hiringId, $serviceId, $senderId, $receiverId) { ?>
    <form id="custom-offer-form" class="custom-offer-form">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::getInstance()->getCSRFToken()) ?>">
        <h1>Custom Offer</h1>

        <input type="hidden" name="offer_id" id="offer_id">
        <input type="hidden" name="hiring_id" value="<?= $hiringId ?>">
        <input type="hidden" name="service_id" value="<?= $serviceId ?>">
        <input type="hidden" name="sender_id" value="<?= $senderId ?>">
        <input type="hidden" name="receiver_id" value="<?= $receiverId ?>">

        <label for="price">Price (€)</label>
        <input type="number" id="price" name="price" min="0" step="0.01" required>

        <label for="delivery">Delivery Time (in days)</label>
        <input type="number" id="delivery" name="delivery" min="1" step="1" required>

        <label for="revisions">Included Revisions</label>
        <input type="number" id="revisions" name="revisions" min="0" step="1" required>

        <div class="button-group">
            <button type="submit" class="btn-add-cart">Save</button>
            <button type="button" class="btn-hire close-modal">Cancel</button>
        </div>
    </form>
<?php } ?>

<?php function drawCustomOfferPageStart() { ?>
    <div class="custom-offer-page-wrapper">
<?php } 

function drawCustomOfferPageEnd() { ?>
    </div>
<?php } ?>