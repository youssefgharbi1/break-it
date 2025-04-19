<?php
namespace App\Model;
use DateTime;
use InvalidArgumentException;
use LogicException;

class Task
{
    private $id;
    private $title;
    private $description;
    private $status;
    private $category;
    private $priority;
    private $dateCreated;
    private $startTime;
    private $dueTime;
    private $estimatedDuration;
    private $createdBy;
    private $assignedTo;
    private $familyId;
    private $recurringPattern;
    private $completionNotes;
    private $pointsValue;
    private $isApproved;

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_ARCHIVED = 'archived';

    // Priority constants
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    public function __construct(
        string $title,
        int $createdBy,
        int $assignedTo,
        int $familyId,
        string $category,
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
        $this->dateCreated = $dateCreated ?: new DateTime();
        $this->startTime = $startTime;
        $this->dueTime = $dueTime;
        $this->estimatedDuration = $estimatedDuration;
        $this->createdBy = $createdBy;
        $this->assignedTo = $assignedTo;
        $this->familyId = $familyId;
        $this->recurringPattern = $recurringPattern;
        $this->completionNotes = $completionNotes;
        $this->pointsValue = $pointsValue;
        $this->isApproved = $isApproved;
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

    public function IsApproved(): bool
    {
        return $this->isApproved;
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
    public function setCreatedBy(int $createdBy): void
    {
        $this->createdBy = $createdBy;
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
}
?>