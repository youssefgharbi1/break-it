<?php
namespace App\Model;
use DateTime;

class RoomMembers {
    private $roomId;    // Primary key and foreign key to Room
    private $memberId;  // Foreign key to User
    private $joinedAt;  // When the member joined the room
    private $requestStatus;

    public function __construct(
        int $roomId,
        int $memberId,
        DateTime $joinedAt = null,
        string $requestStatus = "pending",
    ) {
        $this->roomId = $roomId;
        $this->memberId = $memberId;
        $this->joinedAt = $joinedAt ?: new DateTime();
    }

    // Getters
    public function getRoomId(): int {
        return $this->roomId;
    }

    public function getMemberId(): int {
        return $this->memberId;
    }

    public function getJoinedAt(): DateTime {
        return $this->joinedAt;
    }
    public function getRequestStatus(): string {
        return $this->requestStatus;
    }

    public function setRequestStatus(string $requestStatus): void {
        $this->requestStatus = $requestStatus;
    }

    public function setRoomId(int $roomId): void {
        $this->roomId = $roomId;
    }

    public function setMemberId(int $memberId): void {
        $this->memberId = $memberId;
    }

    public function setJoinedAt(DateTime $joinedAt): void {
        $this->joinedAt = $joinedAt;
    }
    public function toArray(): array {
        return [
            'roomId' => $this->roomId,
            'memberId' => $this->memberId,
            'joinedAt' => $this->joinedAt->format('Y-m-d H:i:s'),
            'requestStatus' => $this->requestStatus,
        ];
    }

}