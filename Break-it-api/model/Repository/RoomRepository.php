<?php
namespace App\model\Repository;

use App\Model\Room;
use App\Conf\Database;
use PDO;
use PDOException;
use RuntimeException;

class RoomRepository
{
    private PDO $db;

    public function __construct(Database $db)
    {
        $this->db = $db->getConnection();
    }

    public function create(Room $room): Room
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                INSERT INTO rooms (name, description, family_id, date_created, code)
                VALUES (:name, :description, :family_id, :date_created, :code)
            ");

            $stmt->execute([
                ':name' => $room->getName(),
                ':description' => $room->getDescription(),
                ':family_id' => $room->getFamilyId(),
                ':date_created' => $room->getDateCreated(),
                ':code' => $room->getCode() ?? $this->generateUniqueCode()
            ]);

            $room->setId((int)$this->db->lastInsertId());
            $this->db->commit();

            return $room;
        } catch (PDOException $e) {
            $this->db->rollBack();
            throw new RuntimeException("Failed to create room: " . $e->getMessage());
        }
    }
    public function findAll(): array
    {
        try {
            $stmt = $this->db->query("SELECT * FROM rooms ORDER BY date_created DESC");
            
            $rooms = [];
            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $rooms[] = $this->hydrate($data);
            }
            
            return $rooms;
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to find all rooms: " . $e->getMessage());
        }
    }

    public function findById(int $id): ?Room
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM rooms WHERE id = :id");
            $stmt->execute([':id' => $id]);
            
            if ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return $this->hydrate($data);
            }
            
            return null;
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to find room: " . $e->getMessage());
        }
    }

    public function findByCode(string $code): ?Room
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM rooms WHERE code = :code");
            $stmt->execute([':code' => $code]);
            
            if ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return $this->hydrate($data);
            }
            
            return null;
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to find room by code: " . $e->getMessage());
        }
    }

    public function findByFamilyId(int $familyId): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM rooms 
                WHERE family_id = :family_id
                ORDER BY date_created DESC
            ");
            $stmt->execute([':family_id' => $familyId]);
            
            $rooms = [];
            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $rooms[] = $this->hydrate($data);
            }
            
            return $rooms;
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to find rooms by family: " . $e->getMessage());
        }
    }

    public function update(Room $room): bool
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE rooms 
                SET name = :name, 
                    description = :description, 
                    family_id = :family_id,
                    code = :code
                WHERE id = :id
            ");

            $stmt->execute([
                ':id' => $room->getId(),
                ':name' => $room->getName(),
                ':description' => $room->getDescription(),
                ':family_id' => $room->getFamilyId(),
                ':code' => $room->getCode()
            ]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to update room: " . $e->getMessage());
        }
    }

    public function delete(int $id): bool 
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM rooms WHERE id = :id");
            $stmt->execute([':id' => $id]);
            
            if ($stmt->rowCount() === 0) {
                throw new RuntimeException("No room found with ID: " . $id);
            }
            return true;
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to delete room: " . $e->getMessage());
        }
    }

    public function codeExists(string $code, ?int $excludeRoomId = null): bool
    {
        try {
            $sql = "SELECT COUNT(*) FROM rooms WHERE code = :code";
            $params = [':code' => $code];
            
            if ($excludeRoomId !== null) {
                $sql .= " AND id != :exclude_id";
                $params[':exclude_id'] = $excludeRoomId;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return (bool)$stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to check code existence: " . $e->getMessage());
        }
    }

    public function generateUniqueCode(int $length = 6): string
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $max = strlen($characters) - 1;
        
        do {
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= $characters[random_int(0, $max)];
            }
        } while ($this->codeExists($code));
        
        return $code;
    }

    private function hydrate(array $data): Room
    {
        return new Room(
            (int)$data['id'],
            $data['name'],
            $data['description'],
            (int)$data['family_id'],
            $data['date_created'],
            $data['code']
        );
    }
}