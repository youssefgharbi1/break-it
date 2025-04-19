<?php
// test_bootstrap.php

// 1. Test bootstrap loading
require_once __DIR__.'/bootstrap.php';

echo "✅ Bootstrap loaded successfully\n";

// 2. Test database connection
try {
    $pdo->query("SELECT 1");
    echo "✅ Database connection working\n";
} catch (PDOException $e) {
    die("❌ Database connection failed: ".$e->getMessage());
}

// 3. Test service initialization
if ($taskService instanceof App\model\Service\TaskService) {
    echo "✅ TaskService initialized\n";
} else {
    die("❌ TaskService not initialized properly");
}

// 4. Test autoloading
$testClass = new App\Model\Task("task", 1,2,1,"hose chores");
if ($testClass instanceof App\Model\Task) {
    echo "✅ Autoloading working\n";
} else {
    die("❌ Autoloading failed");
}

// 5. Test session
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "✅ Session started\n";
} else {
    die("❌ Session not started");
}

echo "All bootstrap tests passed successfully!\n";