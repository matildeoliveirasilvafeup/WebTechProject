<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/database.php';
session_start();

$db = Database::getInstance();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $db->prepare("INSERT INTO users (name, username, email, password_hash) 
                              VALUES (:name, :username, :email, :password_hash)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password_hash', $password_hash);
        $stmt->execute();

        $user_id = $db->lastInsertId();

        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $_SESSION['user'] = $user;

        header("Location: /index.php");
        exit;

    } catch (PDOException $e) {
        $error = 'Error registering: ' . $e->getMessage();
    }
}

?>