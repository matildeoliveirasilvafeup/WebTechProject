<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../database/user.class.php';
require_once __DIR__ . '/../database/service.class.php';
require_once __DIR__ . '/action_upload_file.php';

$session = Session::getInstance();
$user = $session->getUser();

if (!$user) {
    http_response_code(401);
    exit('Unauthorized');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
    http_response_code(400);
    exit('Invalid request.');
}

$serviceId = (int)$_POST['id'];
$service = Service::getById($serviceId);

if (!$service || $service->freelancerId !== $user->id) {
    http_response_code(403);
    exit('You are not authorized to edit this service.');
}

$data = [
    'id' => $serviceId,
    'title' => trim($_POST['title']),
    'description' => trim($_POST['description']),
    'price' => (float)$_POST['price'],
    'category_id' => (int)$_POST['category'],
    'subcategory_id' => (int)$_POST['subcategory'],
    'delivery_time' => (int)$_POST['delivery'],
    'number_of_revisions' => (int)$_POST['revisions'],
    'language' => trim($_POST['language'])
];

try {
    Service::update($data);

    if (!empty($_POST['delete_media'])) {
        foreach ($_POST['delete_media'] as $mediaUrl) {
            Service::deleteSingleFile($mediaUrl);
            Service::deleteMedia($serviceId, $mediaUrl);
        }
    }

    if (!empty($_FILES['images'])) {
        $uploadDir = __DIR__ . '/../uploads/service_media/';
        $uploadedFiles = uploadFiles($_FILES['images'], $uploadDir);

        foreach ($uploadedFiles as $filePath) {
            $mediaPath = str_replace(__DIR__ . '/../', '/', $filePath);
            Service::addMedia($serviceId, $mediaPath);
        }
    }

    header('Location: /pages/dashboard.php#listings');
    exit;

} catch (Exception $e) {
    http_response_code(500);
    exit('Error updating service: ' . $e->getMessage());
}