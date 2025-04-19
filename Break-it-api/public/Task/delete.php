<?php
require_once __DIR__.'/../../app/bootstrap.php';

header('Content-Type: application/json');

try {
    // Authentication check
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Unauthorized', 401);
    }

    // Get task ID from query parameters
    $taskId = isset($_GET['id']) ? (int)$_GET['id'] : null;
    
    // Validate input
    if (!$taskId) {
        throw new Exception('Task ID is required', 400);
    }

    // Get the task
    $task = $taskRepository->findById($taskId);
    
    if (!$task) {
        throw new Exception('Task not found', 404);
    }

    // Authorization - verify user has access to this task
    if ($task->getFamilyId() !== $_SESSION['family_id']) {
        throw new Exception('Forbidden - You do not have access to this task', 403);
    }

    // Format response data
    $response = [
        'id' => $task->getId(),
        'title' => $task->getTitle(),
        'description' => $task->getDescription(),
        'status' => $task->getStatus(),
        'priority' => $task->getPriority(),
        'category' => $task->getCategory(),
        'created_by' => $task->getCreatedBy(),
        'assigned_to' => $task->getAssignedTo(),
        'family_id' => $task->getFamilyId(),
        'created_at' => $task->getCreatedAt()->format('Y-m-d H:i:s'),
        'start_time' => $task->getStartTime() ? $task->getStartTime()->format('Y-m-d H:i:s') : null,
        'due_time' => $task->getDueTime() ? $task->getDueTime()->format('Y-m-d H:i:s') : null,
        'estimated_duration' => $task->getEstimatedDuration(),
        'points_value' => $task->getPointsValue(),
        'is_approved' => $task->isApproved(),
        'completion_notes' => $task->getCompletionNotes(),
        'recurring_pattern' => $task->getRecurringPattern()
    ];

    // Return successful response
    echo json_encode([
        'success' => true,
        'data' => $response
    ]);

} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} catch (RuntimeException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} catch (Exception $e) {
    $statusCode = $e->getCode() ?: 400;
    http_response_code($statusCode);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'code' => $statusCode
    ]);
}