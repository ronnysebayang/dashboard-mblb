<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$kabupaten = sanitizeText($_GET['kabupaten'] ?? '');
$komoditas = sanitizeText($_GET['komoditas'] ?? '');
$search = sanitizeText($_GET['search'] ?? '');

$where = [];
$params = [];

if ($kabupaten !== '') {
    $where[] = 'kabupaten = :kabupaten';
    $params[':kabupaten'] = $kabupaten;
}
if ($komoditas !== '') {
    $where[] = 'komoditas = :komoditas';
    $params[':komoditas'] = $komoditas;
}
if ($search !== '') {
    $where[] = '(perusahaan LIKE :search OR nomor_sk LIKE :search OR nib LIKE :search)';
    $params[':search'] = '%' . $search . '%';
}

$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
$sql = "SELECT jenis_izin AS label, COUNT(*) AS total
        FROM izin
        $whereSql
        GROUP BY jenis_izin
        ORDER BY total DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
jsonResponse(['items' => $stmt->fetchAll()]);
