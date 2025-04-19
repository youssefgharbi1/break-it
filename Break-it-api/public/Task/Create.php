<?php
require_once __DIR__.'/../../../app/bootstrap.php';

header('Content-Type: application/json');

try {
    // Verify authentication
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Authentication required', 401);
    }

    // Get input data
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    if (empty($input['title']) || empty($input['family_id'])) {
        throw new Exception('Title and family ID are required', 400);
    }

    // Set default values
    $input['created_by'] = $_SESSION['user_id'];
    $input['assigned_to'] = $input['assigned_to'] ?? $_SESSION['user_id'];
    
    // Create the task
    $task = $taskService->createTask($input);

    // Return success response
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'data' => [
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'status' => $task->getStatus(),
            'due_time' => $task->getDueTime() ? $task->getDueTime()->format('Y-m-d H:i:s') : null
        ]
    ]);

} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} catch (RuntimeException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}