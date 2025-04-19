<?php


header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
ini_set('session.cookie_samesite', 'None');

require_once __DIR__ . '/../models/User.php'; // For potential future use
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}
$response = ['success' => false, 'message' => ''];
session_start();
try {
    // Check if user was actually logged in
    if (empty($_SESSION['user'])) {
        $response['message'] = 'No active session';
        echo json_encode($response);
        exit;
    }

    // Capture user data before destroying session (optional)
    $loggedOutUser = $_SESSION['user'];

    // Clear all session data
    $_SESSION = [];

    // If using session cookies, destroy the cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    // Destroy the session
    session_destroy();

    // Successful response
    $response = [
        'success' => true,
        'message' => 'Logged out successfully',
        'user' => $loggedOutUser // Optional: Identify who logged out
    ];

} catch (Exception $e) {
    $response['message'] = 'Logout failed: ' . $e->getMessage();
}

echo json_encode($response);
?>