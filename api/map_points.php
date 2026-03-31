<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$kabupaten = sanitizeText($_GET['kabupaten'] ?? '');
$komoditas = sanitizeText($_GET['komoditas'] ?? '');
$status = sanitizeText($_GET['status'] ?? '');
$search = sanitizeText($_GET['search'] ?? '');

$where = ['latitude IS NOT NULL', 'longitude IS NOT NULL'];
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

$whereSql = 'WHERE ' . implode(' AND ', $where);
$sql = "SELECT id, perusahaan, kabupaten, komoditas, status_izin, luas_ha, latitude, longitude, nomor_sk, jenis_izin
        FROM izin
        $whereSql
        ORDER BY perusahaan";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();
jsonResponse(['points' => $rows]);
