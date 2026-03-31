<?php
require_once __DIR__ . '/includes/auth.php';

$path = __DIR__ . '/storage_private/iup.geojson';

if (!file_exists($path)) {
    http_response_code(404);
    exit;
}

header('Content-Type: application/json; charset=utf-8');
readfile($path);
exit;