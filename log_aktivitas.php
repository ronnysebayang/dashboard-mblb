<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/activity_log.php';

$pesanSukses = '';
$pesanError = '';

// batasi hanya admin/email tertentu
$isAdmin = (($_SESSION['user_email'] ?? '') === 'cabdis2ppesdm@gmail.com');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_log'])) {
    if (!$isAdmin) {
        $pesanError = 'Akses ditolak.';
    } else {
        $retensiHari = (int)($_POST['retensi_hari'] ?? 90);

        if (!in_array($retensiHari, [30, 90, 180, 365], true)) {
            $retensiHari = 90;
        }

        try {
            $stmt = $pdo->prepare("
                DELETE FROM activity_logs
                WHERE created_at < NOW() - INTERVAL ? DAY
            ");
            $stmt->execute([$retensiHari]);

            $jumlahTerhapus = $stmt->rowCount();

            tulisLogAktivitas(
                $pdo,
                'HAPUS_LOG',
                "Menghapus {$jumlahTerhapus} log yang lebih lama dari {$retensiHari} hari",
                'log_aktivitas.php'
            );

            $pesanSukses = "Berhasil menghapus {$jumlahTerhapus} log yang lebih lama dari {$retensiHari} hari.";
        } catch (Exception $e) {
            $pesanError = 'Terjadi kesalahan saat menghapus log.';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_semua_log'])) {
    if (!$isAdmin) {
        $pesanError = 'Akses ditolak.';
    } else {
        try {
            // hitung jumlah log dulu
            $stmtCount = $pdo->query("SELECT COUNT(*) FROM activity_logs");
            $total = $stmtCount->fetchColumn();

            // hapus semua
            $pdo->exec("DELETE FROM activity_logs");

            $pesanSukses = "Berhasil menghapus semua log ({$total} data).";
        } catch (Exception $e) {
            $pesanError = 'Gagal menghapus semua log.';
        }
    }
}

$activityFilter = trim($_GET['activity'] ?? '');
$userFilter = trim($_GET['user_email'] ?? '');
$dateFilter = trim($_GET['date'] ?? '');
$sortFilter = trim($_GET['sort'] ?? 'desc');

$where = [];
$params = [];

if ($activityFilter !== '') {
    $where[] = "activity = ?";
    $params[] = $activityFilter;
}

if ($userFilter !== '') {
    $where[] = "user_email LIKE ?";
    $params[] = '%' . $userFilter . '%';
}

if ($dateFilter !== '') {
    $where[] = "DATE(created_at) = ?";
    $params[] = $dateFilter;
}

$orderBy = strtoupper($sortFilter) === 'ASC' ? 'ASC' : 'DESC';

$sql = "
    SELECT id, user_email, activity, detail, page, ip_address, user_agent, created_at
    FROM activity_logs
";

if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY created_at $orderBy LIMIT 300";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Aktivitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --sidebar-bg: #1f3c8c;
            --card-radius: 18px;
            --soft-shadow: 0 8px 24px rgba(30, 41, 59, 0.08);
        }

        body {
            background: #f5f7fb;
            font-family: "Segoe UI", Tahoma, sans-serif;
            color: #1f2937;
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #1E4E8C 0%, #163A6B 100%);
            color: #fff;
            padding: 1.25rem 1rem;
            position: sticky;
            top: 0;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 2rem;
        }

        .brand-icon {
            width: 52px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .brand-icon img {
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
        }

        .brand-title {
            font-weight: 700;
            line-height: 1.1;
        }

        .brand-subtitle {
            font-size: .85rem;
            color: rgba(255,255,255,.75);
        }

        .nav-link {
            color: rgba(255,255,255,.9);
            border-radius: 14px;
            padding: .85rem 1rem;
            margin-bottom: .35rem;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: .2s ease;
        }

        .nav-link.active,
        .nav-link:hover {
            background: rgba(255,255,255,.12);
            color: #fff;
        }

        .content-wrap {
            padding: 1.5rem;
        }

        .page-title {
            font-weight: 700;
            margin-bottom: .2rem;
        }

        .page-subtitle {
            color: #6b7280;
            margin-bottom: 1rem;
        }

        .card-soft {
            border: 0;
            border-radius: var(--card-radius);
            box-shadow: var(--soft-shadow);
            background: #fff;
        }

        .table thead th {
            background: #f8fafc;
            color: #475569;
            font-size: .88rem;
            font-weight: 700;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        .table tbody td {
            vertical-align: middle;
            border-color: #eef2f7;
            font-size: .92rem;
            line-height: 1.35;
        }

        .activity-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .act-login { background: #dbeafe; color: #1d4ed8; }
        .act-logout { background: #e5e7eb; color: #374151; }
        .act-lihat-sk { background: #fee2e2; color: #dc2626; }
        .act-ubah-password { background: #fef3c7; color: #a16207; }
        .act-upload-wiup { background: #dcfce7; color: #15803d; }

        .text-muted-small {
            font-size: .84rem;
            color: #6b7280;
        }

        .detail-link {
            max-width: 260px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #6b7280;
            font-size: .84rem;
            display: block;
        }

        .ua-box {
            max-width: 220px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #6b7280;
            font-size: .84rem;
            display: block;
        }

        .log-time {
            white-space: nowrap;
            font-size: .88rem;
        }

        .form-label {
            font-size: .88rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: .35rem;
        }

        .form-label {
            font-size: .88rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: .35rem;
        }

        .act-hapus-log { background: #fee2e2; color: #b91c1c; }
        .act-hapus-semua { background: #fecaca; color: #991b1b; }


        @media (max-width: 991.98px) {
            .sidebar {
                min-height: auto;
                position: relative;
            }

            .content-wrap {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row g-0">
        <div class="col-lg-2">
            <aside class="sidebar">
                <div class="brand">
                    <div class="brand-icon">
                        <img src="assets/logo-sumut.png" alt="Logo Sumut">
                    </div>
                    <div>
                        <div class="brand-title">MINERBA</div>
                        <div class="brand-subtitle">Monitoring Perizinan MBLB</div>
                    </div>
                </div>

                <nav class="nav flex-column">
                    <a class="nav-link" href="index.php"><i class="bi bi-grid"></i> Dashboard</a>
                    <a class="nav-link" href="izin_masa_berlaku.php"><i class="bi bi-calendar-event"></i> Masa Berlaku Izin</a>
                    <a class="nav-link active" href="log_aktivitas.php"><i class="bi bi-clock-history"></i> Log Aktivitas</a>
                </nav>
            </aside>
        </div>

        <div class="col-lg-10">
            <main class="content-wrap">
                <h1 class="page-title">Log Aktivitas</h1>
                <div class="page-subtitle">Riwayat aktivitas pengguna pada dashboard internal.</div>

                <div class="card card-soft">
                    <div class="card-body">
                        <form method="GET" class="row g-3 align-items-end mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Aktivitas</label>
                                <select name="activity" class="form-select">
                                    <option value="">Semua Aktivitas</option>
                                    <option value="LOGIN" <?php echo $activityFilter === 'LOGIN' ? 'selected' : ''; ?>>LOGIN</option>
                                    <option value="LOGOUT" <?php echo $activityFilter === 'LOGOUT' ? 'selected' : ''; ?>>LOGOUT</option>
                                    <option value="LIHAT_SK" <?php echo $activityFilter === 'LIHAT_SK' ? 'selected' : ''; ?>>LIHAT_SK</option>
                                    <option value="UBAH_PASSWORD" <?php echo $activityFilter === 'UBAH_PASSWORD' ? 'selected' : ''; ?>>UBAH_PASSWORD</option>
                                    <option value="UPLOAD_WIUP" <?php echo $activityFilter === 'UPLOAD_WIUP' ? 'selected' : ''; ?>>UPLOAD_WIUP</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">User / Email</label>
                                <input type="text" name="user_email" class="form-control"
                                    value="<?php echo htmlspecialchars($userFilter); ?>"
                                    placeholder="Cari email user">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" class="form-control"
                                    value="<?php echo htmlspecialchars($dateFilter); ?>">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Urutkan</label>
                                <select name="sort" class="form-select">
                                    <option value="desc" <?php echo $sortFilter === 'desc' ? 'selected' : ''; ?>>Terbaru</option>
                                    <option value="asc" <?php echo $sortFilter === 'asc' ? 'selected' : ''; ?>>Terlama</option>
                                </select>
                            </div>

                            <div class="col-md-2 d-flex gap-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-funnel me-1"></i>Filter
                                </button>
                                <a href="log_aktivitas.php" class="btn btn-outline-secondary w-100">
                                    Reset
                                </a>
                            </div>
                        </form>

                        <div class="text-muted small mb-3">
                            Menampilkan <?php echo count($logs); ?> log aktivitas.
                        </div>

                        <?php if ($pesanSukses): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($pesanSukses); ?></div>
                        <?php endif; ?>

                        <?php if ($pesanError): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($pesanError); ?></div>
                        <?php endif; ?>

                        <?php if ($isAdmin): ?>
                            <form method="POST" class="d-flex flex-wrap gap-2 align-items-end mb-3">
                                <div>
                                    <label class="form-label">Hapus log lebih lama dari</label>
                                    <select name="retensi_hari" class="form-select">
                                        <option value="30">30 hari</option>
                                        <option value="90" selected>90 hari</option>
                                        <option value="180">180 hari</option>
                                        <option value="365">365 hari</option>
                                    </select>
                                </div>

                                <div>
                                    <button type="submit" name="hapus_log" value="1"
                                            class="btn btn-outline-danger"
                                            onclick="return confirm('Yakin ingin menghapus log lama?')">
                                        <i class="bi bi-trash me-1"></i>Hapus Log Lama
                                    </button>
                                    <button type="submit" name="hapus_semua_log" value="1"
                                            class="btn btn-danger"
                                            onclick="return confirm('PERINGATAN! Semua log akan dihapus permanen. Yakin?')">
                                        <i class="bi bi-trash-fill me-1"></i>Hapus Semua Log
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>

                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Waktu</th>
                                        <th>User</th>
                                        <th>Aktivitas</th>
                                        <th>Detail</th>
                                        <th>Halaman</th>
                                        <th>IP</th>
                                        <th>User Agent</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!$logs): ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">Belum ada log aktivitas.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($logs as $log): ?>
                                            <?php
                                                $activity = strtoupper((string)($log['activity'] ?? ''));
                                                $badgeClass = 'act-logout';

                                                if ($activity === 'LOGIN') $badgeClass = 'act-login';
                                                elseif ($activity === 'LOGOUT') $badgeClass = 'act-logout';
                                                elseif ($activity === 'LIHAT_SK') $badgeClass = 'act-lihat-sk';
                                                elseif ($activity === 'UBAH_PASSWORD') $badgeClass = 'act-ubah-password';
                                                elseif ($activity === 'UPLOAD_WIUP') $badgeClass = 'act-upload-wiup';
                                                elseif ($activity === 'HAPUS_LOG') $badgeClass = 'act-hapus-log';
                                                elseif ($activity === 'HAPUS_LOG_SEMUA') $badgeClass = 'act-hapus-semua';
                                            ?>
                                            <tr>
                                                <td>
                                                    <div><?php echo date('d/m/Y H:i', strtotime($log['created_at'])); ?></div>
                                                </td>
                                                <td>
                                                    <div><?php echo htmlspecialchars($log['user_email'] ?? '-'); ?></div>
                                                </td>
                                                <td>
                                                    <span class="activity-badge <?php echo $badgeClass; ?>">
                                                        <?php echo htmlspecialchars($activity); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="detail-link" title="<?php echo htmlspecialchars($log['detail'] ?? '-'); ?>">
                                                        <?php echo htmlspecialchars($log['detail'] ?? '-'); ?>
                                                    </div>
                                                </td>
                                                <td><?php echo htmlspecialchars($log['page'] ?? '-'); ?></td>
                                                <td><?php echo htmlspecialchars($log['ip_address'] ?? '-'); ?></td>
                                                <td>
                                                    <div class="ua-box">
                                                        <?php echo htmlspecialchars($log['user_agent'] ?? '-'); ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>
</div>
</body>
</html>