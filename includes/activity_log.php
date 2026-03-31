<?php

function tulisLogAktivitas(PDO $pdo, string $activity, string $detail = '', string $page = ''): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $userId = $_SESSION['user_id'] ?? null;
    $userEmail = $_SESSION['user_email'] ?? null;
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

    $stmt = $pdo->prepare("
        INSERT INTO activity_logs (
            user_id,
            user_email,
            activity,
            detail,
            page,
            ip_address,
            user_agent
        ) VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $userId,
        $userEmail,
        $activity,
        $detail,
        $page,
        $ipAddress,
        $userAgent
    ]);
}