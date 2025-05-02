<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "You need to be logged in."
    ]);
    exit;
}

require_once 'connection.php';

$user = $_SESSION['user'];
$reason = trim($_POST['reason'] ?? '');

try {
    $db->beginTransaction();

    $sql = "INSERT INTO deleted_users (name, username, email, role, created_at, reason) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        $user['name'],
        $user['username'],
        $user['email'],
        $user['role'],
        $user['created_at'],
        $reason
    ]);

    $deleteSql = "DELETE FROM users WHERE id = ?";
    $deleteStmt = $db->prepare($deleteSql);
    $deleteStmt->execute([$user['id']]);

    $db->commit();

    session_destroy();

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "message" => "User deactivated successfully."
    ]);

} catch (Exception $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error deactivating user: " . $e->getMessage()
    ]);
}
?>
