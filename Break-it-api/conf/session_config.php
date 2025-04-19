<?php
function configureSession() {
    ini_set('session.cookie_samesite', 'None');
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    
    session_set_cookie_params([
        'lifetime' => 86400,
        'path' => '/break-it-api/public/',
        'domain' => 'localhost',
        'secure' => false, // Set true in production
        'httponly' => true,
        'samesite' => 'None'
    ]);
}
?>