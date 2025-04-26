<?php
require_once __DIR__.'/../../bootstrap.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    $userId = $_SESSION['user']['id'];
    $roomId = $_GET['room_id'] ?? null;

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if (!$roomId) {
                http_response_code(400);
                echo json_encode(['error' => 'room_id required']);
                break;
            }
            $messages = $messageService->getMessages((int)$roomId, $userId);
            echo json_encode(['data' => array_map(fn($m) => $m, $messages)]);
            break;

        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            if (!isset($input['room_id'], $input['content'])) {
                http_response_code(400);
                echo json_encode(['error' => 'room_id and content required']);
                break;
            }
            $message = $messageService->sendMessage(
                (int)$input['room_id'],
                $userId,
                trim($input['content'])
            );
            http_response_code(201);
            echo json_encode(['data' => $message->toArray()]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}