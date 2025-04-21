<?php
namespace App\model\Service;

use App\model\Repository\UserRepository;
use App\model\Repository\RoomMembersRepository;
use RuntimeException;

class RoomMembersService {
    private $roomMembersRepository;
    private $userRepository;

    public function __construct(
        RoomMembersRepository $roomMembersRepo,
        UserRepository $userRepo
    ) {
        $this->roomMembersRepository = $roomMembersRepo;
        $this->userRepository = $userRepo;
    }

    public function requestToJoin(int $roomId, int $userId): void
    {
        if ($this->roomMembersRepository->hasMembership($roomId, $userId)) {
            throw new RuntimeException("User already has membership status");
        }

        $this->roomMembersRepository->addRequest($roomId, $userId);
    }

    public function approveMember(int $roomId, int $approverId, int $memberId): void
    {
        $this->verifyApprover($roomId, $approverId);
        
        if (!$this->roomMembersRepository->approveMember(
            $roomId, 
            $memberId
        )) {
            throw new RuntimeException("Approval failed");
        }
    }

    public function rejectMember(int $roomId, int $approverId, int $memberId): void
    {
        $this->verifyApprover($roomId, $approverId);
        if (!$this->roomMembersRepository->hasPendingRequest($roomId, $memberId)){
            throw new RuntimeException("Member has no Pending Request in room ");
        }
        if (!$this->roomMembersRepository->rejectMember($roomId, $memberId)) {
            throw new RuntimeException("Failed to remove member from the room");
        }
    }

    public function kickMember(int $roomId, int $kickerId, int $memberId): void
    {
        $this->verifyApprover($roomId, $kickerId);
        if (!$this->roomMembersRepository->isMember($roomId, $memberId)){
            throw new RuntimeException("Member does not exist in room ".$roomId);
        }
        if (!$this->roomMembersRepository->removeMember($roomId, $memberId)) {
            throw new RuntimeException("Failed to remove member from the room");
        }
    }

    public function getPendingRequests(int $roomId): array
    {
        return $this->roomMembersRepository->findByStatus($roomId, 'pending');
    }

    private function verifyApprover( int $userId): void
    {
        if (!$this->userRepository->canManageRoom($userId)) {
            throw new RuntimeException("Unauthorized approval attempt");
        }
    }
    public function getRoomMembers(int $roomId): array
    {
        return $this->roomMembersRepository->getMembersByRoom($roomId);
    }
    public function isMember(int $roomId, int $userId): bool
    {
        return $this->roomMembersRepository->hasMembership($roomId, $userId);
    }
    public function getAllMembers(): array
    {
        //Indexed By Room id
        $allMembers = $this->roomMembersRepository->getAllMembers();
        $indexedMembers = [];

        foreach ($allMembers as $member) {
            $roomId = $member['room_id'];
            if (!isset($indexedMembers[$roomId])) {
                $indexedMembers[$roomId] = [];
            }
            $indexedMembers[$roomId][] = $member;
        }

        return $indexedMembers;
    }
    
}