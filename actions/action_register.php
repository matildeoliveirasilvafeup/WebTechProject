<?php
declare(strict_types=1);
require_once __DIR__ . '/../database/user.class.php';
require_once __DIR__ . '/../includes/session.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    
    try {
        User::create($name, $username, $email, $password);
    
        $db = Database::getInstance();

        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userData) {
            $user = new User($userData);
            
            $session = Session::getInstance();
            $session->login($user);

            header("Location: ../pages/dashboard.php");
            exit;
        } else {
            header("Location: ../pages/register.php");
            $error = 'User creation failed.';
        }

    } catch (PDOException $e) {
        $error = 'Error registering: ' . $e->getMessage();
    }
}

?>