<?php
require_once __DIR__.'/../bootstrap.php';

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true"); // Required if using sessions/cookies

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

header("Content-Type: application/json");

$input = json_decode(file_get_contents('php://input'), true);

try {
    if (empty($input['email']) || empty($input['password'])) {
        throw new Exception("Email and password required");
    }

    $user = $userService->verifyCredentials($input['email'], $input['password']);

    if ($user) {
        $user = $user->toArray();
        $_SESSION['user'] = [
            'id' => $user['id'],
            'email' => $user['email'],
            'name' => $user['firstName'],
            'role' => $user['role']

        ];
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Invalid credentials");
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>