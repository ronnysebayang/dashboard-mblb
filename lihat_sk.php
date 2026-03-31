<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/activity_log.php';

$link = $_GET['file'] ?? '';

if (!$link || !filter_var($link, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    exit('Link tidak valid');
}

// hanya izinkan Google Drive
$host = parse_url($link, PHP_URL_HOST);

if (!in_array($host, ['drive.google.com', 'docs.google.com'])) {
    http_response_code(403);
    exit('Akses ditolak.');
}

tulisLogAktivitas($pdo, 'LIHAT_SK', $link, 'lihat_sk.php');

header("Location: " . $link);
exit;