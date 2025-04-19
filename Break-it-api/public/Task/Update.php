<?php
require_once __DIR__.'/../../../app/bootstrap.php';

header('Content-Type: application/json');

try {
    // Verify authentication
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Authentication required', 401);
    }

    // Get task ID from URL
    $taskId = isset($_GET['id']) ? (int)$_GET['id'] : null;
    if (!$taskId) {
        throw new Exception('Task ID is required', 400);
    }

    // Get the task
    $task = $taskRepository->findById($taskId);
    if (!$task) {
        throw new Exception('Task not found', 404);
    }

    // Verify the user has access to this task
    if ($task->getFamilyId() != $_SESSION['family_id']) {
        throw new Exception('Unauthorized access to task', 403);
    }

    // Return task data
    echo json_encode([
        'success' => true,
        'data' => [
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'status' => $task->getStatus(),
            'priority' => $task->getPriority(),
            'created_by' => $task->getCreatedBy(),
            'assigned_to' => $task->getAssignedTo(),
            'start_time' => $task->getStartTime() ? $task->getStartTime()->format('Y-m-d H:i:s') : null,
            'due_time' => $task->getDueTime() ? $task->getDueTime()->format('Y-m-d H:i:s') : null,
            'estimated_duration' => $task->getEstimatedDuration(),
            'points_value' => $task->getPointsValue(),
            'is_approved' => $task->isApproved(),
            'completion_notes' => $task->getCompletionNotes()
        ]
    ]);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}