<?php
declare(strict_types=1);

require_once(__DIR__ . '/../includes/database.php');
require_once(__DIR__ . '/../includes/session.php');
require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../database/payment.class.php');

header('Content-Type: application/json');

$session = Session::getInstance();
if (!$session->validateCSRFToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token.']);
    exit;
}

$requiredFields = [
    'service_id', 'client_id', 'freelancer_id',
    'payment_method', 'full_name', 'email', 'address', 'city', 'postal_code'
];

foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['success' => false, 'error' => "Missing field: $field"]);
        exit;
    }
}

$data = [
    'service_id'     => (int)$_POST['service_id'],
    'client_id'      => (int)$_POST['client_id'],
    'freelancer_id'  => (int)$_POST['freelancer_id'],
    'payment_method' => $_POST['payment_method'],
    'full_name'      => $_POST['full_name'],
    'email'          => $_POST['email'],
    'address'        => $_POST['address'],
    'city'           => $_POST['city'],
    'postal_code'    => $_POST['postal_code'],
];

try {
    $result = Payment::create($data);

    if ($result['success']) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $result['message']]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Exception: ' . $e->getMessage()
    ]);
}
