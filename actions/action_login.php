<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/session.php';

require_once __DIR__ . '/../database/user.class.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $user = User::getByEmailAndPassword($email,$password);

    if ($user !== null) {
        Session::getInstance()->login($user);

        header('Location: ../pages/dashboard.php');
        exit;
    } else {
        $error = 'Email or password is incorrect.';
    }
}

?>