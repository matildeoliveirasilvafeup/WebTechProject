<?php
declare(strict_types=1);
require_once(__DIR__ . '/../includes/database.php');
require_once(__DIR__ . '/../database/user.class.php');

class Chat {


    public function __construct(PDO $db) {
    }

    private static function getConversationId(int $user1_id, int $user2_id): string {
        $ids = [$user1_id, $user2_id];
        sort($ids);
        return $ids[0] . '_' . $ids[1];
    }

    public static function createConversation(int $service_id, int $user1_id, int $user2_id): string {
        $db = Database::getInstance();

        $ids = [$user1_id, $user2_id];
        sort($ids);
        $conversation_id = $ids[0] . '_' . $ids[1];

        $stmt = $db->prepare("SELECT 1 FROM conversations WHERE id = ? AND service_id = ?");
        $stmt->execute([$conversation_id, $service_id]);

        if (!$stmt->fetch()) {
            $stmt = $db->prepare("INSERT INTO conversations (id, service_id, user1_id, user2_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$conversation_id, $service_id, $ids[0], $ids[1]]);
        }

        return $conversation_id;
    }

    public static function sendMessage(string $conversation_id, int $service_id, int $sender_id, int $receiver_id, ?string $message, ?string $sub_message = null, ?string $file = null): array {
        $db = Database::getInstance();

        self::createConversation($service_id, $sender_id, $receiver_id);

        $stmt = $db->prepare("
            INSERT INTO messages (conversation_id, service_id, sender_id, receiver_id, message, sub_message, file)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $success = $stmt->execute([
            $conversation_id, $service_id, $sender_id, $receiver_id, $message, $sub_message, $file
        ]);

        return [
            "success" => $success,
            "message" => $success ? "Message sent." : "Failed to send message."
        ];
    }
    
    public static function getMessages(string $conversation_id, int $service_id, int $currentUserId): array {
        $db = Database::getInstance();

        $stmt = $db->prepare("
            SELECT 
                id AS message_id,
                sender_id,
                receiver_id,
                message,
                sub_message,
                file,
                created_at AS message_created_at
            FROM messages
            WHERE conversation_id = ? AND service_id = ?
            ORDER BY created_at ASC
        ");
        $stmt->execute([$conversation_id, $service_id]);
        $rawMessages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $messages = [];

        foreach ($rawMessages as $msg) {
            $statusClass = null;

            if (!empty($msg['message']) && !empty($msg['sub_message'])) {
                if (preg_match('/\b(\w+)\!$/', $msg['message'], $matches)) {
                    $statusClass = strtolower($matches[1]);
                }
            }

            $msg['status_class'] = $statusClass;
            $messages[] = $msg;
        }

        $stmtConv = $db->prepare("
            SELECT user1_id, user2_id, service_id
            FROM conversations 
            WHERE id = ? AND service_id = ?
        ");
        $stmtConv->execute([$conversation_id, $service_id]);
        $conversation = $stmtConv->fetch(PDO::FETCH_ASSOC);

        if (!$conversation) return [];

        $receiverId = ($conversation['user1_id'] == $currentUserId)
            ? $conversation['user2_id']
            : $conversation['user1_id'];

        $stmtReceiver = $db->prepare("
            SELECT u.id, u.username, p.profile_picture
            FROM users u
            LEFT JOIN profiles p ON u.id = p.user_id
            WHERE u.id = ? AND u.is_banned = 0
        ");
        $stmtReceiver->execute([$receiverId]);
        $receiver = $stmtReceiver->fetch(PDO::FETCH_ASSOC);

        return [
            'messages' => $messages,
            'service_id' => $conversation['service_id'],
            'receiver_id' => $receiver['id'],
            'receiver_username' => $receiver['username'],
            'receiver_pIcon' => $receiver['profile_picture']
        ];
    }


    public static function getUserConversations(int $user_id): array {
        $db = Database::getInstance();

        $stmt = $db->prepare("
            SELECT * 
            FROM conversations
            WHERE user1_id = :user_id OR user2_id = :user_id
        ");
        $stmt->execute(['user_id' => $user_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getUnreadMessagesCountByConversation(int $userId): array {
        $db = Database::getInstance();

        $stmt = $db->prepare("
            SELECT conversation_id, service_id, COUNT(*) AS unread_count
            FROM messages
            WHERE receiver_id = ? AND is_read = 0
            GROUP BY conversation_id, service_id
        ");
        $stmt->execute([$userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function markConversationAsRead(string $conversationId, int $userId, int $serviceId): void {
        $db = Database::getInstance();

        $stmt = $db->prepare("
            UPDATE messages
            SET is_read = 1
            WHERE conversation_id = ? AND service_id = ? AND receiver_id = ? AND is_read = 0
        ");
        $stmt->execute([$conversationId, $serviceId, $userId]);
    }
}
