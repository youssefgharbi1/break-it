<?php
namespace App\Model\Repository;

use App\Model\Task;
use App\Conf\Database;
use PDO;
use PDOException;
use RuntimeException;
use DateTime;

class TaskRepository
{
    private PDO $db;
    
    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function create(Task $task): Task
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                INSERT INTO tasks (
                    title, description, status, category, priority, date_created,
                    start_time, due_time, estimated_duration, created_by,
                    assigned_to, family_id, recurring_pattern, completion_notes,
                    points_value, is_approved, room_id
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute($this->extractTaskData($task));
            $task->setId((int)$this->db->lastInsertId());

            $this->db->commit();
            return $task;
        } catch (PDOException $e) {
            $this->db->rollBack();
            throw new RuntimeException("Failed to create task: " . $e->getMessage());
        }
    }

    public function findById(int $id): ?Task
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM tasks WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return $this->hydrate($data);
            }
            
            return null;
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to find task: " . $e->getMessage());
        }
    }

    public function update(Task $task): void
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE tasks SET
                    title = :title,
                    description = :description,
                    status = :status,
                    category = :category,
                    priority = :priority,
                    start_time = :start_time,
                    due_time = :due_time,
                    estimated_duration = :estimated_duration,
                    assigned_to = :assigned_to,
                    recurring_pattern = :recurring_pattern,
                    completion_notes = :completion_notes,
                    points_value = :points_value,
                    is_approved = :is_approved
                    room_id = :room_id
                WHERE id = :id
            ");

            $params = [
                ':id' => $task->getId(),
                ':title' => $task->getTitle(),
                ':description' => $task->getDescription(),
                ':status' => $task->getStatus(),
                ':category' => $task->getCategory(),
                ':priority' => $task->getPriority(),
                ':start_time' => $task->getStartTime() ? $task->getStartTime()->format('Y-m-d H:i:s') : null,
                ':due_time' => $task->getDueTime() ? $task->getDueTime()->format('Y-m-d H:i:s') : null,
                ':estimated_duration' => $task->getEstimatedDuration(),
                ':assigned_to' => $task->getAssignedTo(),
                ':recurring_pattern' => $task->getRecurringPattern(),
                ':completion_notes' => $task->getCompletionNotes(),
                ':points_value' => $task->getPointsValue(),
                ':is_approved' => $task->IsApproved(),
                'room_id' =>$task->getRoomId()
            ];

            $stmt->execute($params);

            if ($stmt->rowCount() === 0) {
                throw new RuntimeException("No task found with ID: " . $task->getId());
            }
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to update task: " . $e->getMessage());
        }
    }

    public function delete(int $id): void
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM tasks WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() === 0) {
                throw new RuntimeException("No task found with ID: " . $id);
            }
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to delete task: " . $e->getMessage());
        }
    }

    public function setAssignedTo(int $taskId, int $newAssigneeId): void
    {
        try {
            $stmt = $this->db->prepare("UPDATE tasks SET assigned_to = ? WHERE id = ?");
            $stmt->execute([$newAssigneeId, $taskId]);
            
            if ($stmt->rowCount() === 0) {
                throw new RuntimeException("No task found with ID: " . $taskId);
            }
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to reassign task: " . $e->getMessage());
        }
    }

    public function findByFamilyId(int $familyId): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM tasks 
                WHERE family_id = ?
                ORDER BY 
                    CASE priority
                        WHEN 'urgent' THEN 1
                        WHEN 'high' THEN 2
                        WHEN 'medium' THEN 3
                        WHEN 'low' THEN 4
                    END,
                    due_time ASC
            ");
            $stmt->execute([$familyId]);
            
            $tasks = [];
            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $tasks[] = $this->hydrate($data);
            }
            
            return $tasks;
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to find tasks by family: " . $e->getMessage());
        }
    }
    public function findAll(): array
    {
        try {
            $stmt = $this->db->query("SELECT * FROM tasks ORDER BY id ASC");
            
            $tasks = [];
            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $tasks[] = $this->hydrate($data)->toArray();
            }
            
            // Return structured response with data wrapper
            return [
                'success' => true,
                'data' => $tasks,
                'meta' => [
                    'count' => count($tasks),
                    'timestamp' => (new DateTime())->format(DateTime::ATOM)
                ]
            ];
            
        } catch (PDOException $e) {
            // Return error structure
            return [
                'success' => false,
                'error' => [
                    'message' => 'Failed to retrieve tasks',
                    'code' => $e->getCode(),
                    'details' => $e->getMessage()
                ],
                'meta' => [
                    'timestamp' => (new DateTime())->format(DateTime::ATOM)
                ]
            ];
        }
    }

    public function findByUser(int $userId): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM tasks 
                WHERE assigned_to = ?
                ORDER BY 
                    CASE 
                        WHEN status = 'completed' THEN 2
                        WHEN status = 'in_progress' THEN 1
                        ELSE 0
                    END,
                    due_time ASC
            ");
            $stmt->execute([$userId]);
            
            $tasks = [];
            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $tasks[] = $this->hydrate($data);
            }
            
            return $tasks;
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to find user tasks: " . $e->getMessage());
        }
    }

    public function findByRoomId(int $roomId): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                t.*, 
                CONCAT(assigned_user.first_name, ' ', assigned_user.last_name) AS assigned_to_name,
                CONCAT(creator_user.first_name, ' ', creator_user.last_name) AS created_by_name
            FROM tasks t
            LEFT JOIN users assigned_user ON assigned_user.id = t.assigned_to
            LEFT JOIN users creator_user ON creator_user.id = t.created_by
            WHERE t.room_id = ?
            ORDER BY 
                CASE t.priority
                    WHEN 'urgent' THEN 1
                    WHEN 'high' THEN 2
                    WHEN 'medium' THEN 3
                    WHEN 'low' THEN 4
                END,
                t.due_time ASC
            ");
            $stmt->execute([$roomId]);
            
            $tasks = [];
            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $tasks[] = $this->hydrate($data);
            }
            
            return $tasks;
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to find tasks by room ID: " . $e->getMessage());
        }
    }

    public function completeTask(int $taskId, ?string $notes = null): void
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE tasks 
                SET status = 'completed', 
                    completion_notes = ?,
                    is_approved = 0
                WHERE id = ?
            ");
            $stmt->execute([$notes, $taskId]);
            
            if ($stmt->rowCount() === 0) {
                throw new RuntimeException("No task found with ID: " . $taskId);
            }
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to complete task: " . $e->getMessage());
        }
    }

    public function approveTask(int $taskId): void
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE tasks 
                SET is_approved = 1 
                WHERE id = ? AND status = 'completed'
            ");
            $stmt->execute([$taskId]);
            
            if ($stmt->rowCount() === 0) {
                throw new RuntimeException("No completed task found with ID: " . $taskId);
            }
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to approve task: " . $e->getMessage());
        }
    }

    private function hydrate(array $data): Task
    {
        $task = new Task(
            $data['title'],
            (int)$data['created_by'],
            $data['created_by_name'] ?? null,
            (int)$data['assigned_to'],
            $data['assigned_to_name'] ?? null,
            (int)$data['family_id'],
            $data['category'],
            (int)$data['room_id'],
            $data['description'] ?? null,
            $data['status'] ?? Task::STATUS_PENDING,
            $data['priority'] ?? Task::PRIORITY_MEDIUM,
            new DateTime($data['date_created']),
            $data['start_time'] ? new DateTime($data['start_time']) : null,
            $data['due_time'] ? new DateTime($data['due_time']) : null,
            $data['estimated_duration'] ?? null,
            $data['recurring_pattern'] ?? null,
            $data['completion_notes'] ?? null,
            (int)($data['points_value'] ?? 1),
            (bool)$data['is_approved']
        );
        $task->setId((int)$data['id']);
        return $task;
    }

    private function extractTaskData(Task $task): array
    {
        return [
            $task->getTitle(),
            $task->getDescription(),
            $task->getStatus(),
            $task->getCategory(),
            $task->getPriority(),
            $task->getDateCreated()->format('Y-m-d H:i:s'),
            $task->getStartTime() ? $task->getStartTime()->format('Y-m-d H:i:s') : null,
            $task->getDueTime() ? $task->getDueTime()->format('Y-m-d H:i:s') : null,
            $task->getEstimatedDuration(),
            $task->getCreatedBy(),
            $task->getAssignedTo(),
            $task->getFamilyId(),
            $task->getRecurringPattern(),
            $task->getCompletionNotes(),
            $task->getPointsValue(),
            (int)$task->isApproved()
        ];
    }
}