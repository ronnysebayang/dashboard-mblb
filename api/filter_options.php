<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$options = [
    'kabupaten' => $pdo->query("SELECT DISTINCT kabupaten FROM izin WHERE kabupaten IS NOT NULL AND trim(kabupaten) <> '' ORDER BY kabupaten")->fetchAll(PDO::FETCH_COLUMN),
    'komoditas' => $pdo->query("SELECT DISTINCT komoditas FROM izin WHERE komoditas IS NOT NULL AND trim(komoditas) <> '' ORDER BY komoditas")->fetchAll(PDO::FETCH_COLUMN),
    'status' => $pdo->query("SELECT DISTINCT status_izin FROM izin WHERE status_izin IS NOT NULL AND trim(status_izin) <> '' ORDER BY status_izin")->fetchAll(PDO::FETCH_COLUMN),
];
jsonResponse($options);
