<?php
declare(strict_types=1);

class Session {
    private static ?Session $instance = null;

    public static function getInstance(): Session {
        if (self::$instance === null) {
            self::$instance = new Session();
        }

        return self::$instance;
    }

    public function __construct() {
        session_start();
    }

    public function getUser() {
        return $_SESSION["user"];
    }

    public function login($user) {
        $_SESSION["user"] = $user;
    }

    public function logout() {
        session_destroy();
    }
}
?>