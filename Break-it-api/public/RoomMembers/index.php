<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header("Access-Control-Allow-Credentials: true");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}
require_once __DIR__ . '/../../bootstrap.php';


$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);
$roomId = $_GET['room_id'] ?? null;
$userId = $_SESSION['user']['id']; // You'll need to implement this based on your auth system


try {
    switch ($method) {
        case 'GET':
            // POST /api/room-members - check is member
            if ($roomId) {
                http_response_code(201);
                echo json_encode(["success"=> true, "data" => $roomMembersService->isMember((int)$roomId, $userId)]);
            } else {
                // Show all members
                $allMembers = $roomMembersService->getAllMembers();
                echo json_encode(["success"=> true, 'data' => $allMembers]);
            }
            break;

        case 'POST':
            // POST /api/room-members - Request to join a room
            if (!isset($input['room_id'])) {
                http_response_code(400);
                echo json_encode(["success"=> false, 'error' => 'Room ID required']);
                break;
            }

            $roomMembersService->requestToJoin((int)$input['room_id'], (int)$input['member_id']);
            http_response_code(201);
            echo json_encode(["success"=> true, 'message' => 'Join request submitted']);
            break;

        case 'PUT':
            // PUT /api/room-members?id=123 - Approve/reject member
            if (!$roomId || !isset($input['member_id']) || !isset($input['action'])) {
                http_response_code(400);
                echo json_encode(["success"=> false, 'error' => 'Room ID, member ID and action required']);
                break;
            }

            $memberId = (int)$input['member_id'];
            $action = $input['action'];

            if ($action === 'approve') {
                $roomMembersService->approveMember((int)$roomId, $userId, $memberId);
                echo json_encode(["success"=> true, 'message' => 'Member approved']);
            } elseif ($action === 'reject') {
                $roomMembersService->rejectMember((int)$roomId, $userId, $memberId);
                echo json_encode(["success"=> true, 'message' => 'Member rejected']);
            } else {
                http_response_code(400);
                echo json_encode(["success"=> false, 'error' => 'Invalid action']);
            }
            break;

        case 'DELETE':
            // DELETE /api/room-members?id=123&member_id=456&action=kick|reject - Remove member or reject request
            // id is room id
            if (!$roomId || !isset($_GET['member_id']) || !isset($_GET['action'])) {
            http_response_code(400);
            echo json_encode(["success"=> false, 'error' => 'Room ID, member ID, and action required']);
            break;
            }

            $memberId = (int)$_GET['member_id'];
            $action = $_GET['action'];

            if ($action === 'kick') {
            $roomMembersService->kickMember((int)$roomId, $userId, $memberId);
            echo json_encode(["success"=> true, 'message' => 'Member kicked from the room']);
            } elseif ($action === 'reject') {
            $roomMembersService->rejectMember((int)$roomId, $userId, $memberId);
            echo json_encode(["success"=> true, 'message' => 'Member request rejected']);
            } else {
            http_response_code(400);
            echo json_encode(["success"=> false, 'error' => 'Invalid action']);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(["success"=> false, 'error' => 'Method not allowed']);
    }
} catch (RuntimeException $e) {
    http_response_code(400);
    echo json_encode(["success"=> false, 'error' => $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success"=> false, 'error' => 'Server error']);
    error_log($e->getMessage());
}