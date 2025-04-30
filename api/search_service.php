<?php 
declare(strict_types=1);

require_once __DIR__ . '/../database/service.class.php';

$service = Service::getServicesBySearch($_GET['search'], 20);

echo json_encode($service)
?>