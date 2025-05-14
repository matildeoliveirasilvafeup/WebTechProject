<?php
    declare(strict_types=1);
    require_once __DIR__ . '/../includes/database.php';
    require_once __DIR__ . '/../database/service.class.php';
    require_once __DIR__ . '/../includes/session.php';
    require_once __DIR__ . '/../database/user.class.php';

    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        exit;
    }

    if (empty($data['title']) || empty($data['description']) || $data['price'] <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        exit;
    }

    $session = Session::getInstance();
    $user = $session->getUser();
    $userId = $user->id ?? null;
    if (!$userId) {
        http_response_code(401);
        return ["success" => false, "message" => "You need to be logged in."];
    }

    $data = [
        'title' => trim($_POST['title'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'price' => (float)($_POST['price'] ?? 0),
        'freelancer_id' => $userId,
        'category_id' => (int)($_POST['category'] ?? 0),
        'subcategory_id' => (int)($_POST['subcategory'] ?? 0),
        'delivery_time' => (int)($_POST['delivery'] ?? 0),
        'number_of_revisions' => (int)($_POST['revisions'] ?? 0),
        'language' => trim($_POST['language'] ?? '')
    ];

    try {
        $newServiceId = Service::create($data);

        echo json_encode([
            'success' => true,
            'message' => 'Service created sucessfully!',
            'service_id' => $newServiceId
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error creating service: ' . $e->getMessage()]);
    }
?>
