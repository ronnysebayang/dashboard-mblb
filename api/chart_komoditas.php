<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$kabupaten = sanitizeText($_GET['kabupaten'] ?? '');
$status = sanitizeText($_GET['status'] ?? '');
$search = sanitizeText($_GET['search'] ?? '');

$where = [];
$params = [];

if ($kabupaten !== '') {
    $where[] = 'kabupaten = :kabupaten';
    $params[':kabupaten'] = $kabupaten;
}
if ($status !== '') {
    $where[] = 'status_izin = :status';
    $params[':status'] = $status;
}
if ($search !== '') {
    $where[] = '(perusahaan LIKE :search OR nomor_sk LIKE :search OR nib LIKE :search)';
    $params[':search'] = '%' . $search . '%';
}

$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
$sql = "SELECT komoditas AS label, COUNT(*) AS total
        FROM izin
        $whereSql
        GROUP BY komoditas
        ORDER BY total DESC
        LIMIT 8";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
jsonResponse(['items' => $stmt->fetchAll()]);
