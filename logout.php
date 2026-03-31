<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/activity_log.php';

$https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => $https,
    'httponly' => true,
    'samesite' => 'Lax'
]);


session_start();

tulisLogAktivitas($pdo, 'LOGOUT', 'Logout user', 'logout.php');

// kosongkan semua data session
$_SESSION = [];

// hapus cookie session di browser
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'] ?? '',
        $params['secure'],
        $params['httponly']
    );
}

// hancurkan session di server
session_destroy();

// arahkan ke login
header("Location: login.php");
exit;