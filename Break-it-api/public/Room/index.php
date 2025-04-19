<?php
require_once __DIR__.'/../../bootstrap.php';

header('Content-Type: application/json');

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $roomId = isset($_GET['id']) ? (int)$_GET['id'] : null;

    switch ($method) {
        case 'GET':
            if ($roomId) {
                // GET /api/rooms?id=123
                $room = $roomService->getRoomById($roomId);
                echo json_encode(['data' => $room]);
            } else {
                // GET /api/rooms
                $rooms = $roomService->getAllRooms();
                echo json_encode(['data' => $rooms]);
            }
            break;

        case 'POST':
            // POST /api/rooms
            $input = json_decode(file_get_contents('php://input'), true);
            $room = $roomService->createRoom(
                $input['name'],
                $input['description'],
                $input['family_id']
            );
            http_response_code(201);
            echo json_encode(['data' => $room]);
            break;

        case 'PUT':
            // PUT /api/rooms?id=123
            if (!$roomId) throw new Exception("Room ID required");
            $input = json_decode(file_get_contents('php://input'), true);
            $room = $roomService->updateRoom(
                $roomId,
                $input['name'],
                $input['description'],
                $input['familyId']
            );
            echo json_encode(['data' => $room]);
            break;

        case 'DELETE':
            // DELETE /api/rooms?id=123
            if (!$roomId) throw new Exception("Room ID required");
            $roomService->deleteRoom($roomId);
            http_response_code(204);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 400);
    echo json_encode(['error' => $e->getMessage()]);
}