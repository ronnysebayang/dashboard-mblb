<?php
require_once __DIR__ . '/includes/db.php';

$stmt = $pdo->query("SELECT COUNT(*) as total FROM activity_logs");
$data = $stmt->fetch();

echo "Jumlah log: " . ($data['total'] ?? 0);