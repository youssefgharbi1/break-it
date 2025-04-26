<?php
namespace App\model\Repository;

use App\Conf\Database;
use PDO;
use App\Model\RoomMembers;
use DateTime;

class RoomMembersRepository {
    private PDO $db;

    public function __construct(Database $db) {
        $this->db = $db->getConnection();
    }
    public function addFirstMember(int $roomId, int $memberId): void {
        $stmt = $this->db->prepare("
            INSERT INTO room_members 
                (room_id, member_id, request_status, joined_at)
            VALUES 
                (:room_id, :member_id, 'accepted', NOW())
            ON DUPLICATE KEY UPDATE
                request_status = 'accepted',
                joined_at = NOW()
        ");

        $stmt->execute([
            ':room_id' => $roomId,
            ':member_id' => $memberId
        ]);
    }

    public function addRequest(int $roomId, int $memberId): void {
        $stmt = $this->db->prepare("
            INSERT INTO room_members 
                (room_id, member_id, request_status)
            VALUES 
                (:room_id, :member_id, 'pending')
            ON DUPLICATE KEY UPDATE
                request_status = 'pending'
        ");

        $stmt->execute([
            ':room_id' => $roomId,
            ':member_id' => $memberId
        ]);
    }

    public function approveMember(int $roomId, int $memberId): bool {
        $stmt = $this->db->prepare("
            UPDATE room_members 
            SET request_status = 'accepted',
                joined_at = NOW()
            WHERE room_id = :room_id 
            AND member_id = :member_id
            AND request_status = 'pending'
        ");

        $stmt->execute([
            ':room_id' => $roomId,
            ':member_id' => $memberId
        ]);

        return $stmt->rowCount() > 0;
    }

    public function rejectMember(int $roomId, int $memberId): bool {
        $stmt = $this->db->prepare("
            DELETE FROM room_members 
            WHERE room_id = :room_id 
            AND member_id = :member_id
            AND request_status = 'pending'
        ");

        $stmt->execute([
            ':room_id' => $roomId,
            ':member_id' => $memberId
        ]);

        return $stmt->rowCount() > 0;
    }
    public function removeMember(int $roomId, int $memberId): bool {
        $stmt = $this->db->prepare("
            DELETE FROM room_members 
            WHERE room_id = :room_id 
            AND member_id = :member_id
        ");

        $stmt->execute([
            ':room_id' => $roomId,
            ':member_id' => $memberId
        ]);

        return $stmt->rowCount() > 0;
    }
    public function hasMembership(int $roomId, int $memberId): bool {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM room_members
            WHERE room_id = :room_id 
            AND member_id = :member_id
            AND request_status = 'accepted'
        ");
        $stmt->execute([
            ':room_id' => $roomId,
            ':member_id' => $memberId
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function getMembersByRoom(int $roomId): array {
        $stmt = $this->db->prepare("
            SELECT 
                rm.room_id,
                rm.member_id,
                rm.joined_at,
                rm.request_status,
                u.first_name,  
                u.email      
            FROM room_members rm
            JOIN users u ON rm.member_id = u.id
            WHERE rm.room_id = :room_id
            AND rm.request_status = 'accepted'
            ORDER BY rm.joined_at
        ");
        
        $stmt->execute([':room_id' => $roomId]);
    
        $members = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $members[] = [
                'room_id' => $row['room_id'],
                'member_id' => $row['member_id'],
                'joined_at' => $row['joined_at'],
                'request_status' => $row['request_status'],
                'user' => [
                    'username' => $row['first_name'],
                    'email' => $row['email']
                    // Add other user fields
                ]
            ];
        }
        
        return $members;
    }

    public function getPendingRequests(int $roomId): array {
        $stmt = $this->db->prepare("
            SELECT * FROM room_members 
            WHERE room_id = :room_id
            AND request_status = 'pending'
        ");
        $stmt->execute([':room_id' => $roomId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isMember(int $roomId, int $memberId): bool {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM room_members
            WHERE room_id = :room_id 
            AND member_id = :member_id
            AND request_status = 'accepted'
        ");
        $stmt->execute([
            ':room_id' => $roomId,
            ':member_id' => $memberId
        ]);
        
        return (bool) $stmt->fetchColumn();
    }

    public function hasPendingRequest(int $roomId, int $memberId): bool {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM room_members
            WHERE room_id = :room_id 
            AND member_id = :member_id
            AND request_status = 'pending'
        ");
        $stmt->execute([
            ':room_id' => $roomId,
            ':member_id' => $memberId
        ]);
        
        return $stmt->fetchColumn() > 0;
    }
    public function getAllMembers(): array {
        $stmt = $this->db->prepare("
            SELECT 
                rm.room_id,
                rm.member_id,
                rm.joined_at,
                rm.request_status,
                u.first_name,
                u.email
            FROM room_members rm
            JOIN users u ON rm.member_id = u.id
            ORDER BY rm.room_id, rm.joined_at
        ");
        
        $stmt->execute();

        $members = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $members[] = [
                'room_id' => $row['room_id'],
                'member_id' => $row['member_id'],
                'joined_at' => $row['joined_at'],
                'request_status' => $row['request_status'],
                'user' => [
                    'username' => $row['first_name'],
                    'email' => $row['email']
                    // Add other user fields
                ]
            ];
        }

        return $members;
    }
    public function getRoomsByMemberId(int $memberId): array {
        $stmt = $this->db->prepare("
            SELECT 
                rm.room_id,
                rm.member_id,
                rm.joined_at,
                rm.request_status,
                r.name AS room_name
            FROM room_members rm
            JOIN rooms r ON rm.room_id = r.id
            WHERE rm.member_id = :member_id
            AND rm.request_status = 'accepted'
            ORDER BY rm.joined_at
        ");
        
        $stmt->execute([':member_id' => $memberId]);

        $rooms = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $rooms[] = [
                'room_id' => $row['room_id'],
                'member_id' => $row['member_id'],
                'joined_at' => $row['joined_at'],
                'request_status' => $row['request_status'],
                'room_name' => $row['room_name']
            ];
        }

        return $rooms;
    }
    public function findByStatus(int $roomId, string $status): array {
        $stmt = $this->db->prepare("
            SELECT * FROM room_members 
            WHERE room_id = :room_id
            AND request_status = :status
        ");
        $stmt->execute([
            ':room_id' => $roomId,
            ':status' => $status
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function hydrate(array $data): RoomMembers {
        return new RoomMembers(
            $data['room_id'],
            $data['member_id'],
            isset($data['joined_at']) ? new DateTime($data['joined_at']) : null
        );
    }
}