<?php
require_once __DIR__.'/../../bootstrap.php';
use App\Model\Task;
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}


try {
    // Get request method and input
    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents('php://input'), true);
    $taskId = isset($_GET['id']) ? (int)$_GET['id'] : null;

    // Route requests
    switch ($method) {
        case 'GET':
            if (!$taskId){
                $tasks = $taskService->getAllTasks();
                echo json_encode(['data' => $tasks]);
            }
            else if ($taskId) {
                // GET /api/tasks?id=123
                $task = $taskService->getTaskById($taskId);
                echo json_encode(['data' => $task->toArray()]);
            } else {
                // GET /api/tasks
                $familyId = $_GET['family_id'] ?? null;
                $userId = $_GET['user_id'] ?? null;
                
                if ($familyId) {
                    $tasks = $taskService->getTasksByFamilyId((int)$familyId);
                } elseif ($userId) {
                    $tasks = $taskService->getTasksByUserId((int)$userId);
                } else {
                    throw new InvalidArgumentException("Missing filter parameter", 400);
                }
                
                echo json_encode(['data' => array_map(fn($t) => $t->toArray(), $tasks)]);
            }
            break;

        case 'POST':
            // POST /api/tasks
            $required = ['title', 'created_by', 'assigned_to', 'family_id', 'category'];
            foreach ($required as $field) {
                if (empty($input[$field])) {
                    throw new InvalidArgumentException("Missing required field: $field", 400);
                }
            }

            $task = $taskService->createTask([
                'title' => $input['title'],
                'description' => $input['description'] ?? null,
                'created_by' => (int)$input['created_by'],
                'assigned_to' => (int)$input['assigned_to'],
                'family_id' => (int)$input['family_id'],
                'category' => $input['category'],
                'status' => $input['status'] ?? Task::STATUS_PENDING,
                'priority' => $input['priority'] ?? Task::PRIORITY_MEDIUM,
                'start_time' => isset($input['start_time']) ? $input['start_time'] : null,
                'due_time' => isset($input['due_time']) ? $input['due_time'] : null,
                'estimated_duration' => $input['estimated_duration'] ?? null,
                'recurring_pattern' => $input['recurring_pattern'] ?? null,
                'points_value' => $input['points_value'] ?? 1
            ]);

            http_response_code(201);
            echo json_encode(['data' => $task->toArray()]);
            break;

        case 'PUT':
            // PUT /api/tasks?id=123
            if (!$taskId) throw new InvalidArgumentException("Task ID required", 400);
            
            $task = $taskService->updateTask(
                $taskId,[
                'title'=> $input['title'] ?? null,
                'description' => $input['description'] ?? null,
                'status' => $input['status'] ?? null,
                'priority' => $input['priority'] ?? null,
                'start_time' => isset($input['start_time']) ? $input['start_time'] : null,
                'due_time' => isset($input['due_time']) ? $input['due_time'] : null,
                'estimated_duration' =>$input['estimated_duration'] ?? null,
                'assigned_to' => $input['assigned_to'] ?? null
                ]
            );
            
            echo json_encode(['data' => $task->toArray()]);
            break;

        case 'DELETE':
            // DELETE /api/tasks?id=123
            if (!$taskId) throw new InvalidArgumentException("Task ID required", 400);
            $taskService->deleteTask($taskId);
            http_response_code(204);
            break;

        case 'PATCH':
            // PATCH /api/tasks?id=123&action=complete
            if (!$taskId) throw new InvalidArgumentException("Task ID required", 400);
            
            $action = $_GET['action'] ?? '';
            switch ($action) {
                case 'complete':
                    $task = $taskService->completeTask($taskId, $input['notes'] ?? null);
                    break;
                case 'approve':
                    $task = $taskService->approveTask($taskId);
                    break;
                case 'reject':
                    if (empty($input['reason'])) throw new InvalidArgumentException("Reason required", 400);
                    $task = $taskService->rejectTask($taskId, $input['reason']);
                    break;
                default:
                    throw new InvalidArgumentException("Invalid action", 400);
            }
            
            echo json_encode(['data' => $task->toArray()]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }

} catch (InvalidArgumentException $e) {
    http_response_code($e->getCode() ?: 400);
    echo json_encode(['error' => $e->getMessage()]);
} catch (LogicException $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    error_log("API Error: " . $e->getMessage());
    echo json_encode(['error' => 'Internal server error']);
}