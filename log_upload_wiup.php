<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/activity_log.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method tidak diizinkan');
}

$detail = trim($_POST['detail'] ?? 'Upload WIUP berhasil');

tulisLogAktivitas($pdo, 'UPLOAD_WIUP', $detail, 'index.php');

header('Content-Type: application/json; charset=utf-8');
echo json_encode(['success' => true]);
exit;