<?php
$databaseDir = __DIR__ . '/../data';
if (!is_dir($databaseDir)) {
    mkdir($databaseDir, 0777, true);
}

$dbPath = $databaseDir . '/minerba.sqlite';
$pdo = new PDO('sqlite:' . $dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$pdo->exec("CREATE TABLE IF NOT EXISTS izin (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    perusahaan TEXT NOT NULL,
    kabupaten TEXT,
    kecamatan TEXT,
    desa TEXT,
    komoditas TEXT,
    status_izin TEXT,
    jenis_izin TEXT,
    nomor_sk TEXT,
    nib TEXT,
    luas_ha REAL DEFAULT 0,
    latitude REAL,
    longitude REAL,
    tanggal_terbit TEXT,
    tanggal_berakhir TEXT,
    keterangan TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    nama TEXT DEFAULT 'Admin Cabdis II',
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
)");

function jsonResponse(array $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function sanitizeText(?string $value): string
{
    return trim((string) $value);
}

function parseFloatValue($value): float
{
    $value = trim((string) $value);
    if ($value === '') {
        return 0.0;
    }

    // dukung format Indonesia dan format internasional
    $value = str_replace(' ', '', $value);
    if (substr_count($value, ',') === 1 && substr_count($value, '.') >= 1) {
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);
    } elseif (substr_count($value, ',') === 1 && substr_count($value, '.') === 0) {
        $value = str_replace(',', '.', $value);
    } else {
        $value = str_replace(',', '', $value);
    }

    return is_numeric($value) ? (float) $value : 0.0;
}

function normalizeStatus(string $status): string
{
    $status = strtolower(trim($status));
    $map = [
        'aktif' => 'Aktif',
        'berakhir' => 'Berakhir',
        'proses' => 'Proses',
        'dicabut' => 'Dicabut',
        'nonaktif' => 'Nonaktif',
    ];

    return $map[$status] ?? ucwords($status ?: 'Tidak Diketahui');
}
