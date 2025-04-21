<?php
namespace App\Model;
use DateTime;
class Message {
    public function __construct(
        public readonly int $id,
        public readonly int $roomId,
        public readonly int $userId,
        public readonly string $content,
        public readonly DateTime $createdAt
    ) {}
    
    public function toArray(): array {
        return [
            'id' => $this->id,
            'room_id' => $this->roomId,
            'user_id' => $this->userId,
            'content' => $this->content,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s')
        ];
    }
}