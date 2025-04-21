<?php
// App/bootstrap.php

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define base path
define('BASE_PATH', realpath(__DIR__.'/'));

// Improved autoloader
spl_autoload_register(function ($class) {

    $classWithoutApp = str_replace('App\\', '/', $class);
    // Convert namespace to file path
    $file = BASE_PATH . $classWithoutApp . '.php';

    if (file_exists($file)) {
        require $file;
    } else {
        error_log("Autoloader failed to find: $file");
    }
});
$taskRepoPath = __DIR__.'/Model/Repository/TaskRepository.php';
$taskModelPath = __DIR__.'/Model/Task.php';

    

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load configuration - FIXED PATH (single slash)
try {
    $configPath = __DIR__.'/conf/dbConfig.php';
    
    if (!file_exists($configPath)) {
        throw new RuntimeException("Database config not found at: ".realpath($configPath));
    }

    $dbConfig = require $configPath;

    // Validate config
    $required = ['host', 'dbname', 'username', 'password'];
    foreach ($required as $key) {
        if (!isset($dbConfig[$key])) {
            throw new RuntimeException("Missing database config key: $key");
        }
    }
} catch (Exception $e) {
    die("Config Error: ".$e->getMessage());
}

// Initialize database connection
try {
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset=utf8mb4",
        $dbConfig['username'],
        $dbConfig['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}


// Initialize repositories

$database = new App\Conf\Database($dbConfig);

$taskRepository = new App\model\Repository\TaskRepository($database);
$userRepository = new App\model\Repository\UserRepository($database);
$roomRepository = new App\model\Repository\RoomRepository($database);
$roomMembersRepository = new App\Model\Repository\RoomMembersRepository($database);
$messageRepository = new App\Model\Repository\MessageRepository($database);


// Initialize services
$taskService = new App\model\Service\TaskService($taskRepository);
$userService = new App\model\Service\UserService($userRepository);
$roomService = new App\model\Service\RoomService($roomRepository, $roomMembersRepository);
$roomMembersService = new App\model\Service\RoomMembersService($roomMembersRepository, $userRepository);
$messageService = new App\Model\Service\MessageService($messageRepository, $roomMembersService);

// Helper function to send JSON responses
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_PRETTY_PRINT);
    exit;
}