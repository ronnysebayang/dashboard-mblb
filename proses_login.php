<?php
$https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => $https,
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/activity_log.php';

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    header("Location: login.php?error=1");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_nama'] = $user['nama'];
        tulisLogAktivitas($pdo, 'LOGIN', 'Login berhasil', 'proses_login.php');

        header("Location: index.php");
        exit;
    } else {
        header("Location: login.php?error=1");
        exit;
    }

} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    header("Location: login.php?error=1");
    exit;
}