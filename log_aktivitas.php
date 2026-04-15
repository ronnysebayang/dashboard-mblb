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
            $stmtCount = $pdo->query("SELECT COUNT(*) FROM activity_logs");
            $total = $stmtCount->fetchColumn();

            $pdo->exec("DELETE FROM activity_logs");

            tulisLogAktivitas(
                $pdo,
                'HAPUS_LOG_SEMUA',
                "Menghapus semua log ({$total} data)",
                'log_aktivitas.php'
            );

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

function badgeActivityClass(string $activity): string {
    $activity = strtoupper($activity);

    return match ($activity) {
        'LOGIN' => 'act-login',
        'LOGOUT' => 'act-logout',
        'LIHAT_SK' => 'act-lihat-sk',
        'UBAH_PASSWORD' => 'act-ubah-password',
        'UPLOAD_WIUP' => 'act-upload-wiup',
        'HAPUS_LOG' => 'act-hapus-log',
        'HAPUS_LOG_SEMUA' => 'act-hapus-semua',
        default => 'act-default',
    };
}
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
            --sidebar-bg: #111111;
            --primary-esdm: #f2d500;
            --primary-esdm-dark: #c9b100;
            --text-dark: #1f2937;
            --card-radius: 18px;
            --soft-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            --soft-shadow-strong: 0 12px 28px rgba(15, 23, 42, 0.08);
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background: #f3f4f6;
            font-family: "Segoe UI", Tahoma, sans-serif;
            color: #1f2937;
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #111111 0%, #1a1a1a 100%);
            color: #fff;
            padding: 1.25rem 1rem;
            position: sticky;
            top: 0;
            border-right: 4px solid var(--primary-esdm);
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
        }

        .brand-logo {
            width: 38px;
            height: 48px;
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
            background: rgba(242, 213, 0, 0.16);
            color: #fff;
            border-left: 3px solid var(--primary-esdm);
        }

        .content-wrap {
            padding: 1.5rem;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: stretch;
            gap: 24px;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #111111 0%, #1f1f1f 100%);
            border-radius: 20px;
            padding: 1.25rem 1.5rem;
            color: #ffffff;
            border-left: 6px solid var(--primary-esdm);
        }

        .page-header-left {
            flex: 1;
            min-width: 0;
        }

        .page-header-right {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
            padding-top: 4px;
        }

        .login-info {
            text-align: right;
            line-height: 1.25;
        }

        .login-info-label {
            font-size: 13px;
            color: rgba(255,255,255,.75);
        }

        .login-info-email {
            font-size: 14px;
            font-weight: 700;
            color: var(--primary-esdm);
        }

        .page-title {
            font-weight: 800;
            margin-bottom: .2rem;
            color: #ffffff;
            letter-spacing: .2px;
        }

        .page-subtitle {
            color: rgba(255,255,255,.82);
            margin-bottom: .2rem;
        }

        .card-soft {
            border: 1px solid #e5e7eb;
            border-radius: var(--card-radius);
            box-shadow: var(--soft-shadow);
            background: #fff;
        }

        .toolbar-card {
            padding: 1.1rem 1.15rem;
            border: 1px solid #e5e7eb;
            background: #fffef7;
        }

        .toolbar-title {
            font-size: .92rem;
            font-weight: 800;
            color: #111111;
            margin-bottom: .85rem;
        }

        .form-label {
            font-size: .88rem;
            font-weight: 700;
            color: #374151;
            margin-bottom: .35rem;
        }

        .form-control,
        .form-select,
        .input-group-text {
            border-radius: 12px !important;
            border-color: #d1d5db;
            min-height: 44px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-esdm);
            box-shadow: 0 0 0 0.15rem rgba(242, 213, 0, 0.18);
        }

        .table thead th {
            background: #111111;
            color: #ffffff;
            font-size: .86rem;
            font-weight: 700;
            border-bottom: 0;
            white-space: nowrap;
            vertical-align: middle;
            padding-top: 13px;
            padding-bottom: 13px;
        }

        .table tbody td {
            vertical-align: middle;
            border-color: #e5e7eb;
            font-size: .92rem;
            line-height: 1.35;
            background: #ffffff;
            padding-top: 13px;
            padding-bottom: 13px;
            transition: background-color .15s ease;
        }

        .table tbody tr:hover td {
            background-color: #ffe8a3 !important;
        }

        .activity-badge {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
            letter-spacing: .2px;
        }

        .act-login {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .act-logout {
            background: #e5e7eb;
            color: #374151;
        }

        .act-lihat-sk {
            background: #fee2e2;
            color: #dc2626;
        }

        .act-ubah-password {
            background: #fef3c7;
            color: #a16207;
        }

        .act-upload-wiup {
            background: #dcfce7;
            color: #15803d;
        }

        .act-hapus-log {
            background: #fee2e2;
            color: #b91c1c;
        }

        .act-hapus-semua {
            background: #fecaca;
            color: #991b1b;
        }

        .act-default {
            background: #f3f4f6;
            color: #374151;
        }

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

        .btn-template {
            border: 1px solid #111111;
            color: #111111;
            background: #ffffff;
            font-weight: 600;
            transition: all .2s ease;
        }

        .btn-template:hover,
        .btn-template:focus {
            background: #111111;
            color: #ffffff;
            border-color: #111111;
        }

        .btn-danger-soft {
            border: 1px solid #991b1b;
            color: #991b1b;
            background: #ffffff;
            font-weight: 600;
            transition: all .2s ease;
        }

        .btn-danger-soft:hover,
        .btn-danger-soft:focus {
            background: #991b1b;
            color: #ffffff;
            border-color: #991b1b;
        }

        .btn-warning-soft {
            background: var(--primary-esdm);
            color: #111111;
            border: 1px solid var(--primary-esdm);
            font-weight: 700;
            transition: all .2s ease;
        }

        .btn-warning-soft:hover,
        .btn-warning-soft:focus {
            background: var(--primary-esdm-dark);
            color: #111111;
            border-color: var(--primary-esdm-dark);
        }

        .alert {
            border-radius: 14px;
        }

        .mobile-header {
            background: linear-gradient(135deg, #111111 0%, #1f1f1f 100%);
            border-radius: 0 0 16px 16px;
            overflow: hidden;
            border-bottom: 3px solid var(--primary-esdm);
        }

        .mobile-header-top {
            padding: 12px 14px 10px;
            border-bottom: 1px solid rgba(255,255,255,0.18);
        }

        .mobile-brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .mobile-brand-logo {
            width: 30px;
            height: 38px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .mobile-brand-title {
            color: #fff;
            font-weight: 800;
            font-size: 1rem;
            line-height: 1.05;
        }

        .mobile-brand-subtitle {
            color: rgba(242, 213, 0, 0.9);
            font-size: 0.78rem;
            line-height: 1.2;
        }

        .mobile-header-menu {
            padding: 10px 12px 12px;
        }

        .mobile-nav {
            position: sticky;
            top: 0;
            z-index: 1040;
        }

        .mobile-nav .menu-scroll {
            scrollbar-width: none;
        }

        .mobile-nav .menu-scroll::-webkit-scrollbar {
            display: none;
        }

        .mobile-menu-btn {
            min-width: 76px;
            height: 46px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background: #ffffff;
            color: #1f2937;
            text-decoration: none;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2px;
            padding: 6px 10px;
            flex: 0 0 auto;
            box-shadow: none !important;
            transition: all .15s ease;
        }

        .mobile-menu-btn i {
            font-size: 0.95rem;
            line-height: 1;
        }

        .mobile-menu-btn small {
            font-size: 11px;
            line-height: 1.1;
            margin: 0;
        }

        .mobile-menu-btn:hover,
        .mobile-menu-btn:focus,
        .mobile-menu-btn:active {
            background: #f8fafc;
            color: #1f2937;
            border-color: #e5e7eb;
            box-shadow: none !important;
        }

        .mobile-menu-btn.active {
            background: #fff8cc;
            color: #111111;
            border-color: var(--primary-esdm);
            font-weight: 700;
        }

        .mobile-menu-btn.active:hover,
        .mobile-menu-btn.active:focus,
        .mobile-menu-btn.active:active {
            background: #fff8cc;
            color: #111111;
            border-color: var(--primary-esdm);
            box-shadow: none !important;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                min-height: auto;
                position: relative;
            }

            .content-wrap {
                padding: 1rem;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .page-header-right {
                width: 100%;
                justify-content: space-between;
                padding-top: 0;
            }

            .login-info {
                text-align: left;
            }
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row g-0">
        <div class="col-lg-2">
            <aside class="sidebar d-none d-md-flex flex-column">
                <div class="brand">
                    <div class="brand-icon">
                        <img src="assets/logo-sumut.png" alt="Logo Sumatera Utara" class="brand-logo">
                    </div>
                    <div>
                        <div class="brand-title">MINERBA</div>
                        <div class="brand-subtitle">Monitoring Perizinan MBLB</div>
                    </div>
                </div>

                <nav class="nav flex-column">
                    <a class="nav-link" href="index.php">
                        <i class="bi bi-grid"></i> Dashboard
                    </a>

                    <a class="nav-link" href="izin_masa_berlaku.php">
                        <i class="bi bi-calendar-event"></i> Masa Berlaku Izin
                    </a>

                    <a class="nav-link active" href="log_aktivitas.php">
                        <i class="bi bi-clock-history"></i> Log Aktivitas
                    </a>
                </nav>
            </aside>
        </div>

        <div class="col-lg-10">
            <main class="content-wrap">

                <div class="d-md-none mobile-nav mobile-header shadow-sm mb-3">
                    <div class="mobile-header-top">
                        <div class="mobile-brand">
                            <img src="assets/logo-sumut.png" alt="Logo Sumatera Utara" class="mobile-brand-logo">
                            <div>
                                <div class="mobile-brand-title">MINERBA</div>
                                <div class="mobile-brand-subtitle">Monitoring Perizinan MBLB</div>
                            </div>
                        </div>
                    </div>

                    <div class="mobile-header-menu">
                        <div class="d-flex gap-2 flex-nowrap text-center menu-scroll overflow-auto">
                            <a href="index.php" class="mobile-menu-btn">
                                <i class="bi bi-grid"></i>
                                <small>Dashboard</small>
                            </a>

                            <a href="izin_masa_berlaku.php" class="mobile-menu-btn">
                                <i class="bi bi-calendar-event"></i>
                                <small>Izin</small>
                            </a>

                            <a href="log_aktivitas.php" class="mobile-menu-btn active">
                                <i class="bi bi-clock-history"></i>
                                <small>Log</small>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="page-header">
                    <div class="page-header-left">
                        <div style="display:inline-flex; align-items:center; gap:8px; background:rgba(242,213,0,.14); color:var(--primary-esdm); border:1px solid rgba(242,213,0,.35); padding:6px 12px; border-radius:999px; font-size:.8rem; font-weight:700; margin-bottom:10px;">
                            <i class="bi bi-shield-check"></i> Dashboard Internal
                        </div>

                        <h1 class="page-title">Log Aktivitas</h1>
                        <div class="page-subtitle">Riwayat aktivitas pengguna pada dashboard internal.</div>
                        <div class="page-subtitle small">Halaman ini menampilkan jejak penggunaan sistem untuk kebutuhan pemantauan internal dan pengelolaan administrasi.</div>
                    </div>

                    <div class="page-header-right">
                        <div class="login-info">
                            <div class="login-info-label">Login:</div>
                            <div class="login-info-email"><?php echo htmlspecialchars($_SESSION['user_email'] ?? '-'); ?></div>
                        </div>

                        <a href="logout.php" class="btn btn-sm rounded-4 px-3" style="background:var(--primary-esdm); color:#111111; border:1px solid var(--primary-esdm); font-weight:600;">
                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                        </a>
                    </div>
                </div>

                <div class="card card-soft toolbar-card mb-3">
                    <div class="toolbar-title">
                        <i class="bi bi-funnel me-1"></i>Filter Log Aktivitas
                    </div>

                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Aktivitas</label>
                            <select name="activity" class="form-select">
                                <option value="">Semua Aktivitas</option>
                                <option value="LOGIN" <?php echo $activityFilter === 'LOGIN' ? 'selected' : ''; ?>>LOGIN</option>
                                <option value="LOGOUT" <?php echo $activityFilter === 'LOGOUT' ? 'selected' : ''; ?>>LOGOUT</option>
                                <option value="LIHAT_SK" <?php echo $activityFilter === 'LIHAT_SK' ? 'selected' : ''; ?>>LIHAT_SK</option>
                                <option value="UBAH_PASSWORD" <?php echo $activityFilter === 'UBAH_PASSWORD' ? 'selected' : ''; ?>>UBAH_PASSWORD</option>
                                <option value="UPLOAD_WIUP" <?php echo $activityFilter === 'UPLOAD_WIUP' ? 'selected' : ''; ?>>UPLOAD_WIUP</option>
                                <option value="HAPUS_LOG" <?php echo $activityFilter === 'HAPUS_LOG' ? 'selected' : ''; ?>>HAPUS_LOG</option>
                                <option value="HAPUS_LOG_SEMUA" <?php echo $activityFilter === 'HAPUS_LOG_SEMUA' ? 'selected' : ''; ?>>HAPUS_LOG_SEMUA</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">User / Email</label>
                            <input
                                type="text"
                                name="user_email"
                                class="form-control"
                                value="<?php echo htmlspecialchars($userFilter); ?>"
                                placeholder="Cari email user"
                            >
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Tanggal</label>
                            <input
                                type="date"
                                name="date"
                                class="form-control"
                                value="<?php echo htmlspecialchars($dateFilter); ?>"
                            >
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Urutkan</label>
                            <select name="sort" class="form-select">
                                <option value="desc" <?php echo $sortFilter === 'desc' ? 'selected' : ''; ?>>Terbaru</option>
                                <option value="asc" <?php echo $sortFilter === 'asc' ? 'selected' : ''; ?>>Terlama</option>
                            </select>
                        </div>

                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-warning-soft w-100">
                                <i class="bi bi-funnel me-1"></i>Filter
                            </button>
                            <a href="log_aktivitas.php" class="btn btn-template w-100">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>

                <?php if ($pesanSukses): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($pesanSukses); ?></div>
                <?php endif; ?>

                <?php if ($pesanError): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($pesanError); ?></div>
                <?php endif; ?>

                <?php if ($isAdmin): ?>
                    <div class="card card-soft toolbar-card mb-3">
                        <div class="toolbar-title">
                            <i class="bi bi-trash me-1"></i>Manajemen Log
                        </div>

                        <form method="POST" class="d-flex flex-wrap gap-2 align-items-end">
                            <div>
                                <label class="form-label">Hapus log lebih lama dari</label>
                                <select name="retensi_hari" class="form-select">
                                    <option value="30">30 hari</option>
                                    <option value="90" selected>90 hari</option>
                                    <option value="180">180 hari</option>
                                    <option value="365">365 hari</option>
                                </select>
                            </div>

                            <div class="d-flex gap-2 flex-wrap">
                                <button
                                    type="submit"
                                    name="hapus_log"
                                    value="1"
                                    class="btn btn-danger-soft"
                                    onclick="return confirm('Yakin ingin menghapus log lama?')"
                                >
                                    <i class="bi bi-trash me-1"></i>Hapus Log Lama
                                </button>

                                <button
                                    type="submit"
                                    name="hapus_semua_log"
                                    value="1"
                                    class="btn btn-danger btn-sm px-3"
                                    onclick="return confirm('PERINGATAN! Semua log akan dihapus permanen. Yakin?')"
                                >
                                    <i class="bi bi-trash-fill me-1"></i>Hapus Semua Log
                                </button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>

                <div class="card card-soft">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
                            <div>
                                <div style="font-weight:800; color:#111111;">Daftar Log Aktivitas</div>
                                <div class="text-muted-small">Menampilkan <?php echo count($logs); ?> log aktivitas.</div>
                            </div>
                        </div>

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
                                                $badgeClass = badgeActivityClass($activity);
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="log-time"><?php echo date('d/m/Y H:i', strtotime($log['created_at'])); ?></div>
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

                                                <td>
                                                    <span class="text-muted-small"><?php echo htmlspecialchars($log['page'] ?? '-'); ?></span>
                                                </td>

                                                <td>
                                                    <span class="text-muted-small"><?php echo htmlspecialchars($log['ip_address'] ?? '-'); ?></span>
                                                </td>

                                                <td>
                                                    <div class="ua-box" title="<?php echo htmlspecialchars($log['user_agent'] ?? '-'); ?>">
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