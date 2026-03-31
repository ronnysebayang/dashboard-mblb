<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$kabupaten = sanitizeText($_GET['kabupaten'] ?? '');
$komoditas = sanitizeText($_GET['komoditas'] ?? '');
$status = sanitizeText($_GET['status'] ?? '');
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
if ($status !== '') {
    $where[] = 'status_izin = :status';
    $params[':status'] = $status;
}
if ($search !== '') {
    $where[] = '(perusahaan LIKE :search OR nomor_sk LIKE :search OR nib LIKE :search)';
    $params[':search'] = '%' . $search . '%';
}

$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
$sql = "SELECT 
    COUNT(*) AS total_izin,
    COALESCE(SUM(luas_ha), 0) AS total_luas,
    SUM(CASE WHEN lower(jenis_izin) = 'iup operasi produksi' THEN 1 ELSE 0 END) AS iup_op,
    SUM(CASE WHEN lower(jenis_izin) = 'iup eksplorasi' THEN 1 ELSE 0 END) AS iup_eks,
    SUM(CASE WHEN lower(jenis_izin) = 'sipb' THEN 1 ELSE 0 END) AS sipb
FROM izin $whereSql";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetch();
jsonResponse($data ?: [
    'total_izin' => 0,
    'total_luas' => 0,
    'iup_op' => 0,
    'iup_eks' => 0,
    'sipb' => 0,
]);
