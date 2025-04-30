<?php
session_start();

$db = new PDO('sqlite:../database/sixerr.db');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user'] = $user;

        header('Location: ../pages/dashboard.php');
        exit;
    } else {
        $error = 'Email or password is incorrect.';
    }
}

require '../templates/common/header.php';
?>