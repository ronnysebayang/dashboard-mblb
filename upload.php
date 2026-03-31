<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Metode tidak diizinkan.'], 405);
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    jsonResponse(['success' => false, 'message' => 'File CSV belum dipilih atau gagal diunggah.'], 400);
}

$uploadedFile = $_FILES['file'];
$extension = strtolower(pathinfo($uploadedFile['name'], PATHINFO_EXTENSION));
if ($extension !== 'csv') {
    jsonResponse(['success' => false, 'message' => 'Saat ini sistem mendukung file CSV. Simpan Excel sebagai CSV UTF-8 terlebih dahulu.'], 400);
}

$destination = __DIR__ . '/uploads/' . date('Ymd_His') . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $uploadedFile['name']);
move_uploaded_file($uploadedFile['tmp_name'], $destination);

$handle = fopen($destination, 'r');
if ($handle === false) {
    jsonResponse(['success' => false, 'message' => 'File tidak dapat dibaca.'], 500);
}

$header = fgetcsv($handle);
if (!$header) {
    fclose($handle);
    jsonResponse(['success' => false, 'message' => 'Header CSV tidak ditemukan.'], 400);
}

$header = array_map(fn($v) => strtolower(trim((string) $v)), $header);
$expected = [
    'perusahaan', 'kabupaten', 'kecamatan', 'desa', 'komoditas', 'status_izin', 'jenis_izin',
    'nomor_sk', 'nib', 'luas_ha', 'latitude', 'longitude', 'tanggal_terbit', 'tanggal_berakhir', 'keterangan'
];

$indexes = [];
foreach ($expected as $field) {
    $indexes[$field] = array_search($field, $header, true);
}

$pdo->beginTransaction();
$pdo->exec('DELETE FROM izin');
$insert = $pdo->prepare("INSERT INTO izin (
    perusahaan, kabupaten, kecamatan, desa, komoditas, status_izin, jenis_izin,
    nomor_sk, nib, luas_ha, latitude, longitude, tanggal_terbit, tanggal_berakhir, keterangan
) VALUES (
    :perusahaan, :kabupaten, :kecamatan, :desa, :komoditas, :status_izin, :jenis_izin,
    :nomor_sk, :nib, :luas_ha, :latitude, :longitude, :tanggal_terbit, :tanggal_berakhir, :keterangan
)");

$rowCount = 0;
while (($row = fgetcsv($handle)) !== false) {
    if (count(array_filter($row, fn($v) => trim((string) $v) !== '')) === 0) {
        continue;
    }

    $data = [];
    foreach ($expected as $field) {
        $idx = $indexes[$field];
        $value = ($idx !== false && isset($row[$idx])) ? $row[$idx] : '';
        $data[$field] = trim((string) $value);
    }

    $insert->execute([
        ':perusahaan' => $data['perusahaan'] ?: 'Tanpa Nama',
        ':kabupaten' => $data['kabupaten'],
        ':kecamatan' => $data['kecamatan'],
        ':desa' => $data['desa'],
        ':komoditas' => $data['komoditas'],
        ':status_izin' => normalizeStatus($data['status_izin']),
        ':jenis_izin' => $data['jenis_izin'],
        ':nomor_sk' => $data['nomor_sk'],
        ':nib' => $data['nib'],
        ':luas_ha' => parseFloatValue($data['luas_ha']),
        ':latitude' => $data['latitude'] === '' ? null : parseFloatValue($data['latitude']),
        ':longitude' => $data['longitude'] === '' ? null : parseFloatValue($data['longitude']),
        ':tanggal_terbit' => $data['tanggal_terbit'],
        ':tanggal_berakhir' => $data['tanggal_berakhir'],
        ':keterangan' => $data['keterangan'],
    ]);
    $rowCount++;
}

fclose($handle);
$pdo->commit();

jsonResponse([
    'success' => true,
    'message' => "Upload berhasil. $rowCount data izin dimasukkan ke dashboard.",
    'rows' => $rowCount,
]);
