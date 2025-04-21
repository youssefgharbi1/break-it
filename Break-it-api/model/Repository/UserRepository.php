<?php
namespace App\model\Repository;

use App\Model\User;
use App\Conf\Database;
use PDO;
use PDOException;
use RuntimeException;

class UserRepository
{
    private PDO $db;

    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function create(User $user): User
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                INSERT INTO users 
                (email, first_name, last_name, password, phone, age, gender, role, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $user->getEmail(),
                $user->getFirstName(),
                $user->getLastName(),
                $user->getPassword(),
                $user->getPhone(),
                $user->getAge(),
                $user->getGender(),
                $user->getRole()
            ]);

            $userId = (int)$this->db->lastInsertId();
            $this->db->commit();

            return $this->findById($userId) ?? throw new RuntimeException("Failed to retrieve created user");
        } catch (PDOException $e) {
            $this->db->rollBack();
            throw new RuntimeException("User creation failed: " . $e->getMessage());
        }
    }

    public function update(User $user): bool
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE users SET
                    first_name = ?,
                    last_name = ?,
                    phone = ?,
                    age = ?,
                    gender = ?,
                    password = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");
            
            $result = $stmt->execute([
                $user->getFirstName(),
                $user->getLastName(),
                $user->getPhone(),
                $user->getAge(),
                $user->getGender(),
                $user->getPassword(),
                $user->getId()
            ]);

            if ($stmt->rowCount() === 0) {
                throw new RuntimeException("No user found with ID: " . $user->getId());
            }

            return $result;
        } catch (PDOException $e) {
            throw new RuntimeException("User update failed: " . $e->getMessage());
        }
    }

    public function findById(int $userId): ?User
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM users 
                WHERE id = ? 
                LIMIT 1
            ");
            $stmt->execute([$userId]);
            
            if ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return $this->hydrate($data);
            }
            
            return null;
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to find user: " . $e->getMessage());
        }
    }

    public function findByEmail(string $email): ?User
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM users 
                WHERE email = ? 
                LIMIT 1
            ");
            $stmt->execute([$email]);
            
            if ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return $this->hydrate($data);
            }
            
            return null;
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to find user by email: " . $e->getMessage());
        }
    }

    public function emailExists(string $email, ?int $excludeUserId = null): bool
    {
        try {
            $sql = "SELECT COUNT(*) FROM users WHERE email = ?";
            $params = [$email];
            
            if ($excludeUserId !== null) {
                $sql .= " AND id != ?";
                $params[] = $excludeUserId;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return (bool)$stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new RuntimeException("Email check failed: " . $e->getMessage());
        }
    }

    public function updatePassword(int $userId, string $newHashedPassword): void
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE users SET
                    password = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");
            
            $stmt->execute([$newHashedPassword, $userId]);
            
            if ($stmt->rowCount() === 0) {
                throw new RuntimeException("No user found with ID: " . $userId);
            }
        } catch (PDOException $e) {
            throw new RuntimeException("Password update failed: " . $e->getMessage());
        }
    }
    public function canManageRoom(int $userId): bool {
        $stmt = $this->db->prepare("
            SELECT role FROM users
            WHERE id = :user_id ;
        ");
        $stmt->execute([
            ':user_id' => $userId,
        ]);
        $role = $stmt->fetchColumn(); // Gets first column (role)
        return strtolower($role) === 'p';

    }

    private function hydrate(array $data): User
    {
        return new User(
            (int)$data['id'],
            $data['email'],
            $data['first_name'],
            $data['last_name'],
            $data['password'],
            $data['phone'] ?? null,
            $data['age'] ?? null,
            $data['gender'] ?? null,
            $data['role'] ?? null,
            $data['created_at'] ?? null,
            $data['updated_at'] ?? null
        );
    }
}