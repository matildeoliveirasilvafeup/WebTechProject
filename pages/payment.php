<?php
    require_once(__DIR__ . '/../includes/session.php');
    require_once(__DIR__ . '/../includes/database.php');

    require_once(__DIR__ . '/../database/user.class.php');
    require_once(__DIR__ . '/../database/service.class.php');
    require_once(__DIR__ . '/../database/payment.class.php');

    require_once(__DIR__ .  '/../templates/common/header.tpl.php');
    require_once(__DIR__ .  '/../templates/common/footer.tpl.php'); 
    // require_once(__DIR__ .  '/../templates/payment.tpl.php');

    $serviceId = $_GET['service_id'] ?? null;
    $clientId = $_GET['client_id'] ?? null;
    $freelancerId = $_GET['freelancer_id'] ?? null;

    $service = Service::getById($serviceId);
    $client = User::getById($clientId);
    $freelancer = User::getById($freelancerId);

    if (!$service || !$client || !$freelancer) {
        die("Missing required parameters.");
    }

    drawHeader();
    drawPaymentForm($service, $client, $freelancer);
    drawFooter();

?>

<?php
function drawPaymentForm(Service $service, User $client, User $freelancer): void {
?>
    <div class="payment-wrapper">
        <h2>Complete Your Hire</h2>

        <!-- Step 1: Billing Info -->
        <div class="payment-step" id="step-billing">
            <h3>Step 1: Billing Information</h3>
            <form id="billing-form">
                <input type="hidden" name="service_id" value="<?= htmlspecialchars($service->id) ?>">
                <input type="hidden" name="client_id" value="<?= htmlspecialchars($client->id) ?>">
                <input type="hidden" name="freelancer_id" value="<?= htmlspecialchars($freelancer->id) ?>">

                <label>Full Name
                    <input type="text" name="full_name" value="<?= htmlspecialchars($client->name) ?>" required>
                </label>
                <label>Email Address
                    <input type="email" name="email" value="<?= htmlspecialchars($client->email) ?>" required>
                </label>
                <label>Address
                    <input type="text" name="address" required>
                </label>
                <label>City
                    <input type="text" name="city" required>
                </label>
                <label>Postal Code
                    <input type="text" name="postal_code" required>
                </label>

                <button type="button" id="to-payment">Continue to Payment</button>
            </form>
        </div>

        <!-- Step 2: Payment Method -->
        <div class="payment-step hidden" id="step-payment">
            <h3>Step 2: Select Payment Method</h3>
            <form id="payment-form"
                data-service-id="<?= $service->id ?>"
                data-client-id="<?= $client->id ?>"
                data-freelancer-id="<?= $freelancer->id ?>"
                data-service-title="<?= htmlspecialchars($service->title, ENT_QUOTES) ?>">

                <input type="hidden" name="service_id" value="<?= htmlspecialchars($service->id) ?>">
                <input type="hidden" name="client_id" value="<?= htmlspecialchars($client->id) ?>">
                <input type="hidden" name="freelancer_id" value="<?= htmlspecialchars($freelancer->id) ?>">

                <label><input type="radio" name="payment_method" value="paypal" required> PayPal</label>
                <label><input type="radio" name="payment_method" value="credit_card"> Credit Card</label>
                <label><input type="radio" name="payment_method" value="pix"> PIX</label>

                <button type="submit" id="confirm-payment">Confirm Payment</button>
            </form>
        </div>
<!-- 
        <div class="payment-step hidden" id="step-go-back">
            <h3>Redirecting to Home Page</h3>
        </div> -->

        <div id="result"></div>
    </div>

    <script src="/js/chat.js"></script>
    <script type="module" src="/js/hirings.js"></script>
    <script type="module" src="/js/payment.js"></script>
<?php }
