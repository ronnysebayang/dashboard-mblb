<?php
require_once __DIR__ . '/includes/db.php';

$sql = "
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    user_email VARCHAR(150) NULL,
    activity VARCHAR(100) NOT NULL,
    detail TEXT NULL,
    page VARCHAR(100) NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
";

try {
    $pdo->exec($sql);
    echo "Tabel activity_logs berhasil dibuat.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}