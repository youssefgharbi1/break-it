<?php
require_once __DIR__.'/../../conf/conf.php';
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}
require_once __DIR__.'/../../bootstrap.php';
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
            echo json_encode([
                'success' => true,
                'messages' => $messages
            ], JSON_NUMERIC_CHECK);
            break;

        case 'POST':
            $ai_reply = null;
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
            if (str_contains($input['content'], "@chat ")){
                 // Extract clean message (remove @chat if present)
                $botmessage = str_replace("@chat ", "", $input['content']);
                
                // Prepare API request
                $api_key = "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"; // Replace with your actual API key
                $url = "https://openrouter.ai/api/v1/chat/completions";
                $data = [
                    "model" => "deepseek/deepseek-r1:free",
                    "messages" => [
                        [
                            "role" => "user",
                            "content" => $botmessage
                        ]
                    ]
                ];

                // Initialize and execute cURL request
                $ch = curl_init($url);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => json_encode($data),
                    CURLOPT_HTTPHEADER => [
                        "Authorization: Bearer " . $api_key,
                        "Content-Type: application/json",
                        "HTTP-Referer: http://localhost:5173",
                        "X-Title: Break-it"
                    ]
                ]);

                $response = curl_exec($ch);
                
                if (curl_errno($ch)) {
                    http_response_code(500);
                    echo json_encode(['error' => 'Chatbot service unavailable']);
                } else {
                    $decoded = json_decode($response, true);
                    $ai_reply = $decoded['choices'][0]['message']['content'] ?? "failed";
                    $ai_reply = $messageService->sendMessageAI(
                        (int)$input['room_id'],
                        trim($ai_reply)
                    );

                }
                
                curl_close($ch);
            }
            
            $responseData = ['success' => true, 'message' => $message->toArray()];
            if ($ai_reply !== null) {
                $responseData['ai_reply'] = $ai_reply;
            }
            
            http_response_code(201);
            echo json_encode(['success' => true, $message->toArray()]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}