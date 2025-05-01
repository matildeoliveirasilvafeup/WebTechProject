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
        return isset($_SESSION['user']) ? $_SESSION['user'] : null;

    }

    public function login($user) {
        $_SESSION["user"] = $user;
    }

    public function logout() {
        session_destroy();
    }

    public function setError(string $message) {
        $_SESSION["error"] = $message;
    }

    public function getError(): ?string {
        if (isset($_SESSION["error"])) {
            $error = $_SESSION["error"];
            unset($_SESSION["error"]);
            return $error;
        }
        return null;
    }
}
?>