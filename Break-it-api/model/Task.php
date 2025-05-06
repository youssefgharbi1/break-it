<?php

namespace App\Model;

use DateTime;
use InvalidArgumentException;
use LogicException;

class Task
{
    // Status constants
    public const STATUS_PENDING = 'pending';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_ARCHIVED = 'archived';

    // Priority constants
    public const PRIORITY_LOW = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_URGENT = 'urgent';

    private ?int $id = null;
    private string $title;
    private ?string $description;
    private string $status;
    private string $category;
    private string $priority;
    private DateTime $dateCreated;
    private ?DateTime $startTime;
    private ?DateTime $dueTime;
    private ?int $estimatedDuration;
    private int $createdBy;
    private string $createdByName;
    private int $assignedTo;
    private string $assignedToName;
    private int $familyId;
    private ?string $recurringPattern;
    private ?string $completionNotes;
    private int $pointsValue;
    private bool $isApproved;
    private int $roomId;


    public function __construct(
        string $title,
        int $createdBy,
        string $createdByName,
        int $assignedTo,
        string $assignedToName,
        int $familyId,
        string $category,
        int $roomId,
        ?string $description = null,
        string $status = self::STATUS_PENDING,
        string $priority = self::PRIORITY_MEDIUM,
        ?DateTime $dateCreated = null,
        ?DateTime $startTime = null,
        ?DateTime $dueTime = null,
        ?int $estimatedDuration = null,
        ?string $recurringPattern = null,
        ?string $completionNotes = null,
        int $pointsValue = 1,
        bool $isApproved = false
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->status = $status;
        $this->category = $category;
        $this->priority = $priority;
        $this->dateCreated = $dateCreated;
        $this->startTime = $startTime;
        $this->dueTime = $dueTime;
        $this->estimatedDuration = $estimatedDuration;
        $this->createdBy = $createdBy;
        $this->assignedTo = $assignedTo;
        $this->assignedToName = $assignedToName;
        $this->familyId = $familyId;
        $this->recurringPattern = $recurringPattern;
        $this->completionNotes = $completionNotes;
        $this->pointsValue = $pointsValue;
        $this->isApproved = $isApproved;
        $this->roomId = $roomId;

        $this->validate();
    }

    private function validate(): void
    {
        if (empty($this->title)) {
            throw new InvalidArgumentException('Task title cannot be empty');
        }

        if (!in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_IN_PROGRESS,
            self::STATUS_COMPLETED,
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
            self::STATUS_ARCHIVED
        ])) {
            throw new InvalidArgumentException('Invalid task status');
        }

        if (!in_array($this->priority, [
            self::PRIORITY_LOW,
            self::PRIORITY_MEDIUM,
            self::PRIORITY_HIGH,
            self::PRIORITY_URGENT
        ])) {
            throw new InvalidArgumentException('Invalid task priority');
        }
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function getDateCreated(): DateTime
    {
        return $this->dateCreated;
    }

    public function getStartTime(): ?DateTime
    {
        return $this->startTime;
    }

    public function getDueTime(): ?DateTime
    {
        return $this->dueTime;
    }

    public function getEstimatedDuration(): ?int
    {
        return $this->estimatedDuration;
    }

    public function getCreatedBy(): int
    {
        return $this->createdBy;
    }

    public function getAssignedTo(): int
    {
        return $this->assignedTo;
    }

    public function getFamilyId(): int
    {
        return $this->familyId;
    }

    public function getRecurringPattern(): ?string
    {
        return $this->recurringPattern;
    }

    public function getCompletionNotes(): ?string
    {
        return $this->completionNotes;
    }

    public function getPointsValue(): int
    {
        return $this->pointsValue;
    }
    public function getRoomId(): ?int
    {
        return $this->roomId;
    }

    public function IsApproved(): bool
    {
        return $this->isApproved;
    }
    public function getAssignedToName(): string
    {
        return $this->assignedToName;
    }
    public function getCreatedByName(): string
    {
        return $this->createdByName;
    }

    
    

    // Setters
    public function setId(int $id): void
    {
        $this->id = $id;
    }
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function setStatus(string $status): void
    {
        if (!in_array($status, [
            self::STATUS_PENDING,
            self::STATUS_IN_PROGRESS,
            self::STATUS_COMPLETED,
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
            self::STATUS_ARCHIVED
        ])) {
            throw new InvalidArgumentException("Invalid status");
        }
        $this->status = $status;
    }

    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    public function setPriority(string $priority): void
    {
        if (!in_array($priority, [
            self::PRIORITY_LOW,
            self::PRIORITY_MEDIUM,
            self::PRIORITY_HIGH,
            self::PRIORITY_URGENT
        ])) {
            throw new InvalidArgumentException("Invalid priority");
        }
        $this->priority = $priority;
    }

    public function setStartTime(?DateTime $startTime): void
    {
        $this->startTime = $startTime;
    }

    public function setDueTime(?DateTime $dueTime): void
    {
        $this->dueTime = $dueTime;
    }

    public function setEstimatedDuration(?int $estimatedDuration): void
    {
        $this->estimatedDuration = $estimatedDuration;
    }

    public function setRecurringPattern(?string $recurringPattern): void
    {
        $this->recurringPattern = $recurringPattern;
    }
    public function setAssignedTo(int $userId): void
    {
        $this->assignedTo = $userId;
    }
    public function setAssignedToName(string $assignedToName): void
    {
        $this->assignedToName = $assignedToName;
    }
    public function setCreatedBy(int $createdBy): void
    {
        $this->createdBy = $createdBy;
    }
    public function setCreatedByName(string $createdByName): void
    {
        $this->createdByName = $createdByName;
    }
    public function setFamilyId(int $familyId): void
    {
        $this->familyId = $familyId;
    }

    public function setCompletionNotes(?string $completionNotes): void
    {
        $this->completionNotes = $completionNotes;
    }

    public function setPointsValue(int $pointsValue): void
    {
        $this->pointsValue = $pointsValue;
    }

    public function setIsApproved(bool $isApproved): void
    {
        $this->isApproved = $isApproved;
    }
    public function setRoomId(?int $roomId): void
    {
        $this->roomId = $roomId;
    }

    // Business logic methods
    public function isOverdue(): bool
    {
        if (!$this->dueTime) {
            return false;
        }
        return $this->dueTime < new DateTime() && $this->status !== self::STATUS_COMPLETED;
    }

    public function isRecurring(): bool
    {
        return $this->recurringPattern !== null;
    }

    public function markComplete(string $notes = null): void
    {
        $this->status = self::STATUS_COMPLETED;
        $this->completionNotes = $notes;
    }

    public function approve(): void
    {
        if ($this->status !== self::STATUS_COMPLETED) {
            throw new LogicException("Only completed tasks can be approved");
        }
        $this->status = self::STATUS_APPROVED;
        $this->isApproved = true;
    }

    public function reject(string $reason): void
    {
        if ($this->status !== self::STATUS_COMPLETED) {
            throw new LogicException("Only completed tasks can be rejected");
        }
        $this->status = self::STATUS_REJECTED;
        $this->completionNotes = $reason;
    }
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'category' => $this->category,
            'priority' => $this->priority,
            'dateCreated' => $this->dateCreated->format('Y-m-d H:i:s'),
            'startTime' => $this->startTime ? $this->startTime->format('Y-m-d H:i:s') : null,
            'dueTime' => $this->dueTime ? $this->dueTime->format('Y-m-d H:i:s') : null,
            'estimatedDuration' => $this->estimatedDuration,
            'createdBy' => $this->createdBy,
            'createdByName' => $this->createdByName ?? null,
            'assignedTo' => $this->assignedTo,
            'assignedToName' => $this->assignedToName ?? null,
            'familyId' => $this->familyId,
            'recurringPattern' => $this->recurringPattern,
            'completionNotes' => $this->completionNotes,
            'pointsValue' => $this->pointsValue,
            'isApproved' => $this->isApproved,
            'roomId' => $this->roomId, // Added roomId here
        ];
    }

}
?>