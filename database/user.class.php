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
    public string $password_hash;


    public function __construct(array $data) {
        $this->id = (int)$data['id'];
        $this->name = $data['name'];
        $this->username = $data['username'];
        $this->email = $data['email'];
        $this->role = $data['role'] ?? 'user';
        $this->createdAt = $data['created_at'] ?? '';
        $this->password_hash = $data['password_hash'] ?? '';
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

    public static function updateAuthentication(?string $email, ?string $password, ?string $newPassword): array {
        $session = Session::getInstance();
        $user = $session->getUser();
        $userId = $user->id ?? null;
        if (!$userId) {
            http_response_code(401);
            return ["success" => false, "message" => "You need to be logged in."];
        }

        $db = Database::getInstance();
        
        if ($email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                return ["success" => false, "message" => "Invalid email."];
            }

            $stmt = $db->prepare("UPDATE users SET email = ? WHERE id = ?");
            if ($stmt->execute([$email, $userId])) {
                $user->email = $email;
                return ["success" => true, "message" => "Email updated successfully."];
            } else {
                http_response_code(500);
                return ["success" => false, "message" => "Failed to update email."];
            }
        }

        if ($password && $newPassword) {

            if (!$user || !password_verify($password, $user->password_hash)) {
                http_response_code(401);
                return ["success" => false, "message" => "Wrong current password."];
            }

            $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            if ($stmt->execute([$newHashedPassword, $userId])) {
                $user->password_hash = $newHashedPassword;
                return ["success" => true, "message" => "Password updated successfully."];
            } else {
                http_response_code(500);
                return ["success" => false, "message" => "Failed to update password."];
            }
        }

        http_response_code(400);
        return ["success" => false, "message" => "No data provided."];
    }

    public static function updateNameAndUsername(string $name, string $username): array {
        $session = Session::getInstance();
        $user = $session->getUser();
        $userId = $user->id ?? null;
        if (!$userId) {
            http_response_code(401);
            return ["success" => false, "message" => "You need to be logged in."];
        }

        $db = Database::getInstance();

        $stmtUser = $db->prepare("UPDATE users SET name = ?, username = ? WHERE id = ?");
        $stmtUser->execute([$name, $username, $userId]);

        $user->name = $name;
        $user->username = $username;

        return [
            'success' => true,
            'message' => 'Profile updated successfully.'
        ];
    }

    public static function deleteAccount(string $reason = ''): array {
        $session = Session::getInstance();
        $user = $session->getUser();
        $userId = $user->id ?? null;

        if (!$userId) {
            http_response_code(401);
            return ["success" => false, "message" => "You need to be logged in."];
        }

        $db = Database::getInstance();

        try {
            $db->beginTransaction();

            $stmt = $db->prepare("
                INSERT INTO deleted_users (name, username, email, role, created_at, reason)
                SELECT name, username, email, role, created_at, ? FROM users WHERE id = ?
            ");
            $stmt->execute([$reason, $userId]);

            $deleteStmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $deleteStmt->execute([$userId]);

            $db->commit();

            $session->logout();

            return [
                "success" => true,
                "message" => "Account deleted successfully."
            ];
        } catch (Exception $e) {
            $db->rollBack();
            http_response_code(500);
            return [
                "success" => false,
                "message" => "Error deleting account: " . $e->getMessage()
            ];
        }
    }
}