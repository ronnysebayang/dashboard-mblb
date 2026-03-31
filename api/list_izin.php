<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$kabupaten = sanitizeText($_GET['kabupaten'] ?? '');
$komoditas = sanitizeText($_GET['komoditas'] ?? '');
$status = sanitizeText($_GET['status'] ?? '');
$search = sanitizeText($_GET['search'] ?? '');
$limit = (int) ($_GET['limit'] ?? 25);
$limit = max(1, min($limit, 200));

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
if ($status !== '') {
    $where[] = 'status_izin = :status';
    $params[':status'] = $status;
}
if ($search !== '') {
    $where[] = '(perusahaan LIKE :search OR nomor_sk LIKE :search OR nib LIKE :search)';
    $params[':search'] = '%' . $search . '%';
}

$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
$sql = "SELECT perusahaan, kabupaten, komoditas, status_izin, jenis_izin, luas_ha, nomor_sk, nib
        FROM izin
        $whereSql
        ORDER BY perusahaan
        LIMIT $limit";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
jsonResponse(['items' => $stmt->fetchAll()]);
