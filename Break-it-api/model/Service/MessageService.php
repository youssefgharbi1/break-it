<?php
namespace App\model\Service;
use App\Model\Message;
use App\model\Repository\MessageRepository;
use App\model\Service\RoomMembersService;
use RuntimeException;

class MessageService {
    public function __construct(
        private MessageRepository $messageRepo,
        private RoomMembersService $roomMembersService
    ) {}
    
    public function sendMessage(int $roomId, int $userId, string $content): Message {
        if (!$this->roomMembersService->isMember($roomId, $userId)) {
            throw new RuntimeException("Not a room member");
        }
        
        return $this->messageRepo->create($roomId, $userId, $content);
    }
    public function sendMessageAI(int $roomId, string $content): Message {
        
        return $this->messageRepo->create($roomId, 101, $content);
    }
    
    public function getMessages(int $roomId, int $userId): array {
        if (!$this->roomMembersService->isMember($roomId, $userId)) {
            throw new RuntimeException("Not a room member");
        }
        
        return $this->messageRepo->getByRoom($roomId);
    }
}