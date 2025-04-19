<?php
namespace App\model\Service;

use App\Model\Task;
use App\model\Repository\TaskRepository; 
use DateTime;

class TaskService
{
    private $repository;

    public function __construct(TaskRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Creates a new task with validation
     */
    public function createTask(array $taskData): Task
    {
        // Validate required fields
        if (empty($taskData['title'])) {
            throw new \InvalidArgumentException("Task title is required");
        }

        $task = new Task(
            $taskData['title'],
            $taskData['created_by'],
            $taskData['assigned_to'],
            $taskData['family_id'],
            $taskData['category'],
            $taskData['description'] ?? null,
            $taskData['status'] ?? Task::STATUS_PENDING,
            $taskData['priority'] ?? Task::PRIORITY_MEDIUM,
            new DateTime(),
            isset($taskData['start_time']) ? new DateTime($taskData['start_time']) : null,
            isset($taskData['due_time']) ? new DateTime($taskData['due_time']) : null,
            $taskData['estimated_duration'] ?? null,
            $taskData['recurring_pattern'] ?? null,
            $taskData['completion_notes'] ?? null,
            $taskData['points_value'] ?? 1,
            $taskData['is_approved'] ?? false
        );

        return $this->repository->create($task);
    }

    /**
     * Marks a task as complete
     */
    public function completeTask(int $taskId, string $notes = null): Task
    {
        $task = $this->repository->findById($taskId);
        if (!$task) {
            throw new \RuntimeException("Task not found");
        }

        $task->markComplete($notes);
        $this->repository->update($task);
        
        return $task;
    }

    /**
     * Approves a completed task
     */
    public function approveTask(int $taskId): Task
    {
        $task = $this->repository->findById($taskId);
        if (!$task) {
            throw new \RuntimeException("Task not found");
        }

        $task->approve();
        $this->repository->update($task);
        
        return $task;
    }

    /**
     * Reassigns a task to another family member
     */
    public function reassignTask(int $taskId, int $newAssigneeId): Task
    {
        $task = $this->repository->findById($taskId);
        if (!$task) {
            throw new \RuntimeException("Task not found");
        }

        $task->setAssignedTo($newAssigneeId);
        $this->repository->update($task);
        
        return $task;
    }

    /**
     * Gets overdue tasks for a family
     */
    public function getOverdueTasks(int $familyId): array
    {
        $tasks = $this->repository->findByFamilyId($familyId);
        return array_filter($tasks, function(Task $task) {
            return $task->isOverdue();
        });
    }

    /**
     * Gets today's tasks for a user
     */
    public function getTodaysTasks(int $userId): array
    {
        $todayStart = new DateTime('today');
        $todayEnd = new DateTime('tomorrow');

        $tasks = $this->repository->findByUser($userId);
        
        return array_filter($tasks, function(Task $task) use ($todayStart, $todayEnd) {
            $dueTime = $task->getDueTime();
            return $dueTime && $dueTime >= $todayStart && $dueTime < $todayEnd;
        });
    }

    /**
     * Creates recurring tasks from a template
     */
    public function generateRecurringTasks(Task $template): array
    {
        if (!$template->isRecurring()) {
            throw new \RuntimeException("Template task must be recurring");
        }

        $generatedTasks = [];
        $nextDate = $this->calculateNextOccurrence($template);

        for ($i = 0; $i < 5; $i++) { // Generate next 5 instances
            $task = clone $template;
            $task->setStartTime($nextDate);
            $task->setDueTime((clone $nextDate)->modify('+1 hour'));
            $task->setStatus(Task::STATUS_PENDING);
            
            $this->repository->create($task);
            $generatedTasks[] = $task;
            
            $nextDate = $this->calculateNextOccurrence($template, $nextDate);
        }

        return $generatedTasks;
    }

    private function calculateNextOccurrence(Task $task, DateTime $after = null): DateTime
    {
        $after = $after ?: new DateTime();
        $nextDate = clone ($task->getStartTime() ?: new DateTime());

        while ($nextDate <= $after) {
            switch ($task->getRecurringPattern()) {
                case 'daily':
                    $nextDate->modify('+1 day');
                    break;
                case 'weekly':
                    $nextDate->modify('+1 week');
                    break;
                case 'monthly':
                    $nextDate->modify('+1 month');
                    break;
                default:
                    throw new \RuntimeException("Invalid recurring pattern");
            }
        }

        return $nextDate;
    }
}
?>