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
            new DateTime()
        );
    }
    
    public function getByRoom(int $roomId, int $limit = 100): array {
        $stmt = $this->db->prepare("
            SELECT m.*, u.first_name
            FROM messages m
            JOIN users u ON m.user_id = u.id
            WHERE m.room_id = ?
            ORDER BY m.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$roomId, $limit]);
        
        return array_map(
            function($row) {
                return [
                    'message' => (new Message(
                        $row['id'],
                        $row['room_id'],
                        $row['user_id'],
                        $row['content'],
                        new DateTime($row['created_at'])
                    ))->toArray(),
                    'user_first_name' => $row['first_name']
                ];
            },
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }
}