<?php
namespace App\model\Repository;
use App\Conf\Database;
use App\Model\Message;
use PDO;
use DateTime;

class MessageRepository {
    private PDO $db;

    public function __construct(Database $db) {
        $this->db = $db->getConnection();
    }
    public function create(int $roomId, int $userId, string $content): Message {
        $stmt = $this->db->prepare("
            INSERT INTO messages (room_id, user_id, content)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$roomId, $userId, $content]);
        
        return new Message(
            $this->db->lastInsertId(),
            $roomId,
            $userId,
            $content,
            new DateTime(),
            $_SESSION["user"]["username"]
        );
    }
    
    public function getByRoom(int $roomId, int $limit = 50): array {
        $stmt = $this->db->prepare("
            SELECT m.*, CONCAT(u.first_name, ' ', u.last_name) AS username
            FROM messages m
            JOIN users u ON m.user_id = u.id
            WHERE m.room_id = ?
            ORDER BY m.created_at asc
            LIMIT ?
        ");
        $stmt->execute([$roomId, $limit]);
        
        $messages = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $createdAt = new DateTime($row['created_at']);
            $messages[] = [
                'message' => [
                    'id' => (int)$row['id'],
                    'room_id' => (int)$row['room_id'],
                    'user_id' => (int)$row['user_id'],
                    'username' => $row['username'],
                    'content' => $row['content'],
                    'created_at' => $createdAt->format(DateTime::ATOM)
                ]
            ];
        }
        
        return $messages;
    }
}