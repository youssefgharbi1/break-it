<?php
namespace App\Model;

use DateTime;
use DateTimeInterface;
use JsonSerializable;

class Message implements JsonSerializable {
    public function __construct(
        public readonly int $id,
        public readonly int $roomId,
        public readonly int $userId,
        public readonly string $content,
        public readonly DateTimeInterface $createdAt,
        public readonly ?string $username = null
    ) {}
    
    public function toArray(): array {
        return [
            'id' => $this->id,
            'room_id' => $this->roomId,
            'user_id' => $this->userId,
            'username' => $this->username,
            'content' => htmlspecialchars($this->content, ENT_QUOTES, 'UTF-8'),
            'created_at' => $this->createdAt->format(DateTimeInterface::ATOM),
            'timestamp' => $this->createdAt->getTimestamp(),
            'is_recent' => $this->isRecent()
        ];
    }
    
    public function jsonSerialize(): array {
        return $this->toArray();
    }
    
    public function isRecent(): bool {
        $now = new DateTime();
        $diff = $now->diff($this->createdAt);
        return $diff->days === 0 && $diff->h < 1;
    }
    
    public static function fromArray(array $data): self {
        return new self(
            (int)$data['id'],
            (int)$data['room_id'],
            (int)$data['user_id'],
            $data['content'],
            new DateTime($data['created_at']),
            $data['username'] ?? null
        );
    }
    
    public function withUserDetails(string $username): self {
        return new self(
            $this->id,
            $this->roomId,
            $this->userId,
            $this->content,
            $this->createdAt,
            $username
        );
    }
}