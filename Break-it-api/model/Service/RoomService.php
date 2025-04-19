<?php
namespace App\model\Service;

use App\Model\Room;
use App\model\Repository\RoomRepository;
use Exception;

class RoomService
{
    private $roomRepository;

    public function __construct(RoomRepository $roomRepository)
    {
        $this->roomRepository = $roomRepository;
    }

    /**
     * Create a new room with validation and automatic code generation
     */
    public function createRoom(
        string $name,
        string $description,
        int $familyId,
        ?string $dateCreated = null
    ): Room {
        // Validate input
        $this->validateRoomData($name, $description, $familyId);

        // Create room object
        $room = new Room(
            null, // id will be set by database
            $name,
            $description,
            $familyId,
            $dateCreated ?? date('Y-m-d H:i:s')
        );

        // Generate and set unique code
        $room->setCode($this->generateUniqueRoomCode($room));

        // Save to database
        return $this->roomRepository->create($room);
    }

    /**
     * Update an existing room
     */
    public function updateRoom(
        int $roomId,
        string $name,
        string $description,
        int $familyId
    ): Room {
        // Validate input
        $this->validateRoomData($name, $description, $familyId);

        // Get existing room
        $room = $this->getRoomById($roomId);

        // Update room properties
        $room->setName($name);
        $room->setDescription($description);
        $room->setFamilyId($familyId);

        // Save changes
        $this->roomRepository->update($room);

        return $room;
    }

    /**
     * Get a room by ID with validation
     */
    public function getRoomById(int $roomId): Room
    {
        $room = $this->roomRepository->findById($roomId);

        if (!$room) {
            throw new Exception("Room not found with ID: $roomId");
        }

        return $room;
    }

    /**
     * Get a room by code with validation
     */
    public function getRoomByCode(string $code): Room
    {
        $room = $this->roomRepository->findByCode($code);

        if (!$room) {
            throw new Exception("Room not found with code: $code");
        }

        return $room;
    }

    /**
     * Get all rooms for a family
     */
    public function getRoomsByFamilyId(int $familyId): array
    {
        return $this->roomRepository->findByFamilyId($familyId);
    }

    /**
     * Delete a room
     */
    public function deleteRoom(int $roomId): void
    {
        $room = $this->getRoomById($roomId);
        
        if (!$this->roomRepository->delete($room->getId())) {
            throw new Exception("Failed to delete room with ID: $roomId");
        }
    }

    /**
     * Regenerate a room code
     */
    public function regenerateRoomCode(int $roomId): Room
    {
        $room = $this->getRoomById($roomId);
        
        $newCode = $this->generateUniqueRoomCode($room);
        $room->setCode($newCode);
        
        $this->roomRepository->update($room);
        
        return $room;
    }

    /**
     * Validate room data before creation/update
     */
    private function validateRoomData(string $name, string $description, int $familyId): void
    {
        if (empty($name)) {
            throw new Exception("Room name cannot be empty");
        }

        if (strlen($name) > 100) {
            throw new Exception("Room name cannot exceed 100 characters");
        }

        if (strlen($description) > 500) {
            throw new Exception("Room description cannot exceed 500 characters");
        }

        if ($familyId <= 0) {
            throw new Exception("Invalid family ID");
        }
    }

    /**
     * Generate a unique room code
     */
    private function generateUniqueRoomCode(): string
    {
        return $this->roomRepository->generateUniqueCode();
    }

    /**
     * Verify room ownership
     */
    public function verifyRoomOwnership(int $roomId, int $familyId): bool
    {
        $room = $this->getRoomById($roomId);
        return $room->getFamilyId() === $familyId;
    }
    public function getAllRooms(): array
{
    return array_map(function($room) {
        return $room->toArray();
    }, $this->roomRepository->findAll());
}
}