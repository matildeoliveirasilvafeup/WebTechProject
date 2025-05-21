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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function getUser() {
        return isset($_SESSION['user']) ? $_SESSION['user'] : null;
    }

    public function login($user) {
        $_SESSION["user"] = $user;
        $_SESSION["role"] = $user->role ?? null;
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

    public static function getRole(): ?string {
        return $_SESSION["role"] ?? null;
    }

    public static function setRole(string $role): void {
        $_SESSION["role"] = $role;
    }

    public static function isAdmin(): bool {
        return isset($_SESSION["role"]) && $_SESSION["role"] === "admin";
    }
}
?>