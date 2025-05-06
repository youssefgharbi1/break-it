<?php
header("Access-Control-Allow-Origin: http://localhost:5173"); // Vite dev server
header("Access-Control-Allow-Credentials: true"); // Required for cookies/session
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight requests (OPTIONS method)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__.'/../bootstrap.php';


// Handle different HTTP methods
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST': // Start a new session or update session data
        $input = json_decode(file_get_contents('php://input'), true);
        if (is_array($input)) {
            foreach ($input as $key => $value) {
                $_SESSION[$key] = $value;
            }
            echo json_encode(['status' => 'success', 'message' => 'Session data updated']);
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
        }
        break;

    case 'GET': // Retrieve session data
        echo json_encode(['status' => 'success', 'session' => $_SESSION["user"]]);
        break;

    case 'DELETE': // Destroy the session
        session_destroy();
        echo json_encode(['status' => 'success', 'message' => 'Session destroyed']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
        break;
}
?>