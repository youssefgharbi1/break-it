<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header("Access-Control-Allow-Credentials: true");

require_once __DIR__.'/../../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $roomId = isset($_GET['id']) ? (int)$_GET['id'] : null;
    $userId = $_SESSION['user']['id']; 

    switch ($method) {
        case 'GET':
            if (isset($roomId)) {
                // GET /api/rooms?id=123
                $room = $roomService->getRoomById($roomId)->toArray();
                $room['roomMembers'] = $roomMembersService->getRoomMembers($roomId);
                echo json_encode(["success"=> true, 'data' => $room]);
            } elseif (isset($_GET['member_id'])) {
                // GET /api/rooms?member_id=456
                $memberId = (int)$_GET['member_id'];
                $rooms = $roomMembersService->getRoomsByMemberId($memberId);
                echo json_encode(["success"=> true, 'data' => $rooms]);
            } elseif (isset($userId)) {
                // GET /api/rooms?user_id=789
                $rooms = $roomMembersService->getRoomsByMemberId($userId);
                echo json_encode(["success"=> true, 'data' => $rooms]);
            }
             else {
                // GET /api/rooms
                $rooms = $roomService->getAllRooms();
                echo json_encode(["success"=> true, 'data' => $rooms]);
            }
            break;

        case 'POST':
            // POST /api/rooms
            $input = json_decode(file_get_contents('php://input'), true);
            $room = $roomService->createRoom(
                $input['name'],
                $input['description'],
                $userId
            );
            http_response_code(201);
            echo json_encode(["success"=> true, 'data' => $room]);
            break;

        case 'PUT':
            // PUT /api/rooms?id=123
            if (!$roomId) throw new Exception("Room ID required");
            $input = json_decode(file_get_contents('php://input'), true);

            $imagePath = null;

            if (!empty($input['image'])) {
                $imagePath = saveBase64Image($input['image'], 'rooms');
            }

            $room = $roomService->updateRoom(
                $roomId,
                $input['name'] ?? null,
                $input['description'] ?? null,
                null,
                $imagePath ?? null

            );
            echo json_encode(["success"=> true, 'data' => $room]);
            break;

        case 'DELETE':
            // DELETE /api/rooms?id=123
            if (!$roomId) throw new Exception("Room ID required");
            $roomService->deleteRoom($roomId);
            http_response_code(204);
            break;

        default:
            http_response_code(405);
            echo json_encode(["success"=> false, 'message' => 'Method not allowed']);
    }

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 400);
    echo json_encode(["success"=> false, 'message' => $e->getMessage()]);
}

function saveBase64Image(string $base64Image, string $folder): string
{
    // Extract image data from base64 string
    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
    if (!$imageData) {
        throw new Exception("Invalid base64 image data");
    }

    // Define storage directory
    $relativeDir = "/uploads/$folder/";
    $storageDir = __DIR__ . '/../../public' . $relativeDir;

    if (!file_exists($storageDir)) {
        mkdir($storageDir, 0777, true);
    }

    // Generate unique filename (optionally, extract extension from MIME)
    $filename = uniqid($folder . '_', true) . '.jpg'; // Always saving as JPG
    $fullPath = $storageDir . $filename;

    file_put_contents($fullPath, $imageData);

    return $relativeDir . $filename;
}
