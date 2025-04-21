<?php

// Bootstrap
require_once __DIR__.'/../bootstrap.php';



ob_start();
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
    // Basic validation
    $required = ['first_name', 'last_name', 'email', 'password', 'phone', 'age', 'gender', 'role'];
    foreach ($required as $field) {
        if (empty($input[$field])) {
            throw new Exception("$field is required");
        }
    }
    
    // Check if email exists
    if ($userService->getUserByEmail($input['email'])) {
        throw new Exception("Email already registered");
    }

    // Create user
    if ($userService->createUser($input)) {
        $_SESSION['user_email'] = $input['email']; // Simple session tracking
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Registration failed");
    }

} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
ob_end_flush();
?>