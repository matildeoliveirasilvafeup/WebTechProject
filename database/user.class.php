<?php
declare(strict_types=1);
require_once(__DIR__ . '/../includes/database.php');

class User {
    public int $id;
    public string $name;
    public string $username;
    public string $email;
    public string $role;
    public string $createdAt;


    public function __construct(array $data) {
        $this->id = (int)$data['id'];
        $this->name = $data['name'];
        $this->username = $data['username'];
        $this->email = $data['email'];
        $this->role = $data['role'] ?? 'user';
        $this->createdAt = $data['created_at'] ?? '';
    }

    public function getName(): string {
        return $this->name;
    }

    public static function create(string $name, string $username, string $email, string $password) {
        $db = Database::getInstance();
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare('
            INSERT INTO users (name, username, email, password_hash)
            VALUES (:name, :username, :email, :password_hash)
        ');
        $stmt->execute([
            ':name' => $name,
            ':username' => $username,
            ':email' => $email,
            ':password_hash' => $passwordHash
        ]);
    }

    public static function getByEmailAndPassword($email, $password) {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute([':email' => $email]);

        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($userData && password_verify($password, $userData['password_hash'])) {
            return new User($userData);
        }

        return null;
    }
}