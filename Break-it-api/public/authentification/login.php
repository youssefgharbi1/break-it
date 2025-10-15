<?php
require_once __DIR__.'/../../conf/conf.php';
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}
require_once __DIR__.'/../../bootstrap.php';

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
            'username' => $user['firstName'] . " " . $user['lastName'],
            'role' => $user['role']
        ];
        echo json_encode(['success' => true, "user" => $_SESSION['user']]);
    } else {
        throw new Exception("Invalid credentials");
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>