<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/activity_log.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Monitoring Izin Pertambangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <style>
        :root {
            --sidebar-bg: #1f3c8c;
            --card-radius: 18px;
            --soft-shadow: 0 8px 24px rgba(30, 41, 59, 0.08);
            --soft-shadow-strong: 0 12px 28px rgba(30, 41, 59, 0.10);
        }

        body {
            background: #f5f7fb;
            font-family: "Segoe UI", Tahoma, sans-serif;
            color: #1f2937;
        }

        html {
            scroll-behavior: smooth;
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
            background: rgba(255,255,255,.12);
            color: #fff;
        }

        .content-wrap {
            padding: 1.5rem;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 24px;
            margin-bottom: 1.5rem;
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
            color: #6b7280;
        }

        .login-info-email {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
        }

        .page-title {
            font-weight: 700;
            margin-bottom: .2rem;
        }

        .page-subtitle {
            color: #6b7280;
        }

        .card-soft {
            border: 0;
            border-radius: var(--card-radius);
            box-shadow: var(--soft-shadow);
            background: #fff;
        }

        .stat-card {
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--soft-shadow-strong);
        }

        .stat-card .card-body {
            padding: 1.1rem 1.15rem;
        }

        .stat-card .icon-box {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
        }

        .stat-label {
            color: #6b7280;
            font-weight: 600;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1.1;
        }

        .toolbar-card {
            padding: 1rem;
        }

        .toolbar-title {
            font-size: .92rem;
            font-weight: 700;
            color: #374151;
            margin-bottom: .85rem;
        }

        .panel-title {
            font-weight: 700;
            color: #111827;
            margin-bottom: 1rem;
        }

        .chart-box {
            min-height: 280px;
        }

        .chart-box .panel-title {
            margin-bottom: .85rem;
        }

        .small-note {
            font-size: .88rem;
            color: #6b7280;
        }

        .section-subtitle {
            font-size: .88rem;
            color: #6b7280;
        }

        .table-card-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 1rem;
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
            padding-top: 12px;
            padding-bottom: 12px;
        }

        .table tbody tr {
            transition: background-color .15s ease;
        }

        .table tbody tr:hover td {
            background-color: #f8fbff;
        }

        .row-terpilih td {
            background-color: #e0f2fe !important;
        }

        .sortable-active {
            color: #1d4ed8;
            font-weight: 700;
        }

        .sortable-active span {
            margin-left: 4px;
            font-size: 12px;
        }

        .badge-status {
            font-size: .76rem;
            font-weight: 600;
            padding: 6px 10px;
            border-radius: 10px;
            white-space: normal;
            line-height: 1.15;
            display: inline-block;
            text-align: center;
        }

        .kolom-perusahaan {
            min-width: 300px;
            white-space: normal;
            word-break: break-word;
            line-height: 1.4;
        }

        .kolom-jenis-izin,
        .kolom-dokumen-sk {
            text-align: center;
            vertical-align: middle;
        }

        .kolom-jenis-izin .badge-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            min-width: 80px;
        }

        .kolom-dokumen-sk .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        #map {
            height: 470px;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
        }

        .map-toolbar {
            padding: .2rem 0 0 0;
        }

        .map-toolbar .btn {
            box-shadow: none;
        }

        .map-legend {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
            font-size: 13px;
            background: rgba(255,255,255,.75);
            padding: 6px 10px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            display: inline-block;
            margin-right: 6px;
        }

        #fullscreenMapBtn,
        #toggleMeasureBtn,
        #clearMeasureBtn {
            height: 38px;
        }

        #komoditasChart {
            height: 240px !important;
        }

        .north-arrow-control {
            background: transparent;
            padding: 2px;
            box-shadow: none;
        }

        .label-ukur-jarak {
            background: transparent !important;
            border: none !important;
        }

        .label-ukur-jarak div {
            display: inline-block;
            background: #dc2626;
            color: #fff;
            padding: 6px 10px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
            line-height: 1.2;
            border: 2px solid #dc2626;
            box-shadow: 0 0 0 4px rgba(245, 247, 251, 1);
        }

        .map-fullscreen {
            position: fixed !important;
            inset: 0 !important;
            z-index: 99999 !important;
            background: #f5f7fb !important;
            margin: 0 !important;
            padding: 8px !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            overflow: hidden !important;
            width: 100vw !important;
            height: 100vh !important;
        }

        .map-fullscreen .card-body {
            height: 100% !important;
            display: flex !important;
            flex-direction: column !important;
            padding: 8px !important;
        }

        .map-fullscreen .map-header-text {
            display: none !important;
        }

        .map-fullscreen .map-toolbar {
            gap: 8px !important;
            margin: 0 0 6px 0 !important;
            padding: 0 !important;
            flex-shrink: 0;
        }

        .map-fullscreen #map {
            flex: 1 !important;
            width: 100% !important;
            height: auto !important;
            min-height: 0 !important;
            margin: 0 !important;
            border-radius: 10px !important;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .map-fullscreen .overlap-panel {
            display: none !important;
        }

        body.fullscreen-active {
            overflow: hidden;
        }

        .leaflet-popup-content-wrapper {
            border-radius: 14px;
            box-shadow: 0 10px 24px rgba(22, 58, 107, 0.18);
            border: 1px solid #dbe5f1;
        }

        .leaflet-popup-content {
            margin: 14px 16px;
            font-size: 13px;
            line-height: 1.5;
            color: #374151;
        }

        .leaflet-popup-content b:first-child {
            color: #1E4E8C;
            font-size: 14px;
        }

        .leaflet-popup-tip {
            background: #fff;
        }

        .leaflet-container a.leaflet-popup-close-button {
            color: #1E4E8C;
            padding: 6px 10px 0 0;
            font-size: 18px;
            font-weight: 700;
        }

        .leaflet-container a.leaflet-popup-close-button:hover {
            color: #163A6B;
        }

        @media (min-width: 1200px) {
            .col-xl-20 {
                flex: 0 0 20%;
                max-width: 20%;
            }
        }

        @media (max-width: 991.98px) {
            .sidebar {
                min-height: auto;
                position: relative;
            }

            .content-wrap {
                padding: 1rem;
            }

            #map {
                height: 360px;
            }
        }

        @media (max-width: 768px) {
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

        .nav-sublink {
            padding-left: 2.3rem;
            font-size: 0.94rem;
            color: rgba(255,255,255,.78);
        }

        .nav-sublink:hover {
            background: rgba(255,255,255,.08);
            color: #fff;
        }

        .map-toolbar-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 38px;
            padding: 0 14px;
            line-height: 1;
            white-space: nowrap;
        }

        .map-toolbar-btn i {
            font-size: 0.95rem;
            line-height: 1;
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
                        <img src="assets/logo-sumut.png" alt="Logo Sumatera Utara" class="brand-logo">
                    </div>
                    <div>
                        <div class="brand-title">MINERBA</div>
                        <div class="brand-subtitle">Monitoring Perizinan MBLB</div>
                    </div>
                </div>

                <nav class="nav flex-column">
                    <a class="nav-link active" href="#top">
                        <i class="bi bi-grid"></i> Dashboard
                    </a>

                    <a class="nav-link nav-sublink" href="#panelPeta">
                        <i class="bi bi-map"></i> Peta Perizinan
                    </a>

                    <a class="nav-link nav-sublink" href="#panelData">
                        <i class="bi bi-table"></i> Data Perizinan
                    </a>

                    <a class="nav-link nav-sublink" href="#panelStatistik">
                        <i class="bi bi-bar-chart"></i> Statistik
                    </a>

                    <a class="nav-link nav-sublink" href="#uploadSection">
                        <i class="bi bi-upload"></i> Upload WIUP
                    </a>

                    <a class="nav-link" href="izin_masa_berlaku.php">
                        <i class="bi bi-calendar-event"></i> Masa Berlaku Izin
                    </a>
                    
                    <a class="nav-link" href="log_aktivitas.php">
                    <i class="bi bi-clock-history"></i> Log Aktivitas
                </a>
                </nav>
            </aside>
        </div>

        <div class="col-lg-10">
            <main class="content-wrap">
                <div class="page-header mb-4">

                    <div class="page-header-left">
                        <h1 class="page-title">Dashboard Monitoring Perizinan MBLB</h1>
                        <div class="page-subtitle">Cabang Dinas ESDM Wilayah II — DPPESDM Provinsi Sumatera Utara</div>
                        <div class="page-subtitle small">Proyek Aktualisasi Latsar CPNS Tahun 2026 — Optimalisasi Pengelolaan Data Perizinan Berbasis Peta</div>
                    </div>

                    <div class="page-header-right">
                        <div class="login-info">
                            <div class="login-info-label">Login:</div>
                            <div class="login-info-email"><?php echo htmlspecialchars($_SESSION['user_email']); ?></div>
                        </div>

                        <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-4">
                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                        </a>
                    </div>

                </div>
                 
                <div class="card card-soft toolbar-card mb-3">
                    <div class="toolbar-title">
                        <i class="bi bi-funnel me-1"></i>Filter Dashboard
                    </div>

                    <div class="row g-3 align-items-center">
                        <div class="col-lg-4">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Cari perusahaan / nomor SK / NIB...">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <select class="form-select" id="kabupatenFilter">
                                <option value="">Semua Kabupaten</option>
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <select class="form-select" id="komoditasFilter">
                                <option value="">Semua Komoditas</option>
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <select class="form-select" id="statusFilter">
                                <option value="">Semua Status</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6 col-xl-20">
                        <div class="card card-soft stat-card h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="icon-box" style="background:#dbeafe;color:#1E4E8C;"><i class="bi bi-file-earmark-text"></i></div>
                                <div>
                                    <div class="stat-label">Total Izin</div>
                                    <div class="stat-value" id="totalIzin">0</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-20">
                        <div class="card card-soft stat-card h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="icon-box" style="background:#fef3c7;color:#F3B000;"><i class="bi bi-bounding-box"></i></div>
                                <div>
                                    <div class="stat-label">Total Luas (Ha)</div>
                                    <div class="stat-value" id="totalLuas">0</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-20">
                        <div class="card card-soft stat-card h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="icon-box" style="background:#dcfce7;color:#198754;"><i class="bi bi-gear"></i></div>
                                <div>
                                    <div class="stat-label">IUP Operasi Produksi</div>
                                    <div class="stat-value" id="iupOp">0</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-20">
                        <div class="card card-soft stat-card h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="icon-box" style="background:#fff7ed;color:#d97706;"><i class="bi bi-search"></i></div>
                                <div>
                                    <div class="stat-label">IUP Eksplorasi</div>
                                    <div class="stat-value" id="iupEks">0</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-20">
                        <div class="card card-soft stat-card h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="icon-box" style="background:#eff6ff;color:#2563eb;"><i class="bi bi-shield-check"></i></div>
                                <div>
                                    <div class="stat-label">SIPB</div>
                                    <div class="stat-value" id="sipb">0</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-xl-8" id="panelPeta">
                        <div class="card card-soft h-100" id="cardPeta">
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="map-header-text mb-2">
                                        <div class="panel-title mb-1">Peta Sebaran Izin</div>
                                        <div class="small-note">
                                            Data izin existing ditampilkan dari peta perizinan, sedangkan usulan WIUP dibaca dari file Excel untuk analisis overlap.
                                        </div>
                                    </div>

                                    <div class="map-toolbar d-flex flex-wrap align-items-center gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-dark rounded-3 map-toolbar-btn" id="toggleMeasureBtn">
                                            <i class="bi bi-rulers me-1"></i>Ukur Jarak
                                        </button>

                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-3" id="clearMeasureBtn" style="display:none;">
                                            <i class="bi bi-x-circle me-1"></i>Hapus Ukur
                                        </button>

                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-3 map-toolbar-btn" id="fullscreenMapBtn">
                                            <i class="bi bi-arrows-fullscreen me-1"></i>Fullscreen
                                        </button>

                                        <button type="button" class="btn btn-sm btn-outline-success rounded-3 map-toolbar-btn" id="btnLokasiSaya">
                                            <i class="bi bi-crosshair me-1"></i>Lokasi Saya
                                        </button>

                                        <button type="button" class="btn btn-sm btn-outline-success rounded-3 map-toolbar-btn" id="btnTrackingLokasi">
                                            <i class="bi bi-broadcast me-1"></i>Tracking Lokasi
                                        </button>

                                        <div class="map-legend ms-1">
                                            <div class="legend-item">
                                                <span class="legend-dot" style="background:#198754"></span>
                                                <span>IUP Operasi Produksi</span>
                                            </div>

                                            <div class="legend-item">
                                                <span class="legend-dot" style="background:#facc15"></span>
                                                <span>IUP Eksplorasi</span>
                                            </div>

                                            <div class="legend-item">
                                                <span class="legend-dot" style="background:#2563eb"></span>
                                                <span>SIPB</span>
                                            </div>

                                            <div class="legend-item" id="legendUsulan" style="display:none;">
                                                <span class="legend-dot" style="background:#7c3aed"></span>
                                                <span>Usulan WIUP</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="map"></div>

                                <div id="ringkasanOverlap" class="mt-3 overlap-panel" style="display:none;">
                                    <div class="card border-0 rounded-4" style="background:#f8fafc;">
                                        <div class="card-body">
                                            <div class="fw-semibold mb-2">Ringkasan Overlap Usulan WIUP</div>
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <div class="small text-muted">Jumlah Izin Overlap</div>
                                                    <div class="fs-5 fw-bold" id="overlapJumlah">0</div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="small text-muted">Total Luas Overlap</div>
                                                    <div class="fs-5 fw-bold" id="overlapLuas">0 Ha</div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="small text-muted">Overlap Terbesar</div>
                                                    <div class="fs-6 fw-bold" id="overlapTerbesar">-</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="detailOverlapWrapper" class="card border-0 rounded-4 mt-3 overlap-panel" style="background:#ffffff; display:none;">
                                    <div class="card-body">
                                        <div class="fw-semibold mb-2">Detail Overlap Existing</div>
                                        <div class="table-responsive">
                                            <table class="table table-sm align-middle mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Perusahaan</th>
                                                        <th>No. SK</th>
                                                        <th class="text-end">Luas Overlap (Ha)</th>
                                                        <th class="text-end">Persentase</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="detailOverlapBody">
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted">Belum ada data overlap</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4" id="panelStatistik">
                        <div class="card card-soft mb-3">
                            <div class="card-body chart-box">
                                <div class="panel-title">Distribusi Jenis Izin</div>
                                <canvas id="statusChart"></canvas>
                            </div>
                        </div>
                        <div class="card card-soft">
                            <div class="card-body chart-box">
                                <div class="panel-title">Komoditas Teratas</div>
                                <canvas id="komoditasChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-soft mt-3" id="panelData">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                            <div>
                                <div class="panel-title mb-1" id="judulTabel">Data Izin Pertambangan</div>
				                <div class="small-note" id="subjudulTabel">Menampilkan data sesuai filter dashboard.</div>
                            </div>
                            <button class="btn btn-sm btn-outline-primary rounded-4" id="refreshButton">
                                <i class="bi bi-arrow-clockwise me-1"></i>Refresh Data
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
    				    <tr>
        				<th id="sortNama" style="cursor:pointer">Nama Perusahaan <span id="iconSortNama"></span></th>
        				<th id="sortKabupaten" style="cursor:pointer">Kabupaten <span id="iconSortKabupaten"></span></th>
        				<th id="sortKomoditas" style="cursor:pointer">Komoditas <span id="iconSortKomoditas"></span></th>
        				<th id="sortLuas" class="text-end" style="cursor:pointer">Luas (Ha) <span id="iconSortLuas"></span></th>
        				<th id="sortStatus" class="text-center" style="cursor:pointer">Jenis Izin <span id="iconSortStatus"></span></th>
                        <th class="text-center">Dokumen SK</th>
    				    </tr>
				</thead>
                                <tbody id="tableBody">
                                    <tr><td colspan="6" class="text-center text-muted py-4">Memuat data...</td></tr>
                                </tbody>
                            </table>
                        </div>
			<div class="d-flex justify-content-between align-items-center mt-3">
    			    <div class="small text-muted" id="infoHalaman">Menampilkan data</div>
                            <div id="pagination" class="d-flex gap-2"></div>
                        </div>
                    </div>
                </div>

                <div class="card card-soft mt-3" id="uploadSection">
                    <div class="card-body">
                        <div class="d-flex gap-2 flex-wrap mb-3">
                            <button class="btn btn-primary rounded-4 px-4" data-bs-toggle="modal" data-bs-target="#uploadModal">
                                <i class="bi bi-upload me-2"></i>Upload Excel WIUP
                            </button>

                            <a class="btn btn-outline-secondary rounded-4 px-4" href="data/template_wiup_usulan.xlsx" download>
                                <i class="bi bi-download me-2"></i>Template Excel
                            </a>

                            <button class="btn btn-outline-danger rounded-4 px-4" id="refreshUsulanWiup">
                                <i class="bi bi-x-circle me-2"></i>Hapus Usulan WIUP
                            </button>
                        </div>

                        <div class="panel-title">Petunjuk Upload Usulan WIUP</div>
                        <ol class="mb-2">
                            <li>Siapkan file <strong>Excel (.xlsx)</strong> sesuai template usulan WIUP.</li>
                            <li>Pastikan sheet koordinat berisi urutan titik polygon.</li>
                            <li>Kolom minimal yang wajib tersedia adalah <strong>urut</strong>, <strong>longitude</strong>, dan <strong>latitude</strong>.</li>
                            <li>Klik tombol <strong>Upload Excel WIUP</strong>.</li>
                            <li>Sistem akan membentuk polygon usulan dan mengecek indikasi tumpang tindih dengan izin existing.</li>
                        </ol>
                        <div class="small-note">
                            File upload ini digunakan untuk analisis usulan WIUP sementara, bukan untuk menambah data izin existing ke dashboard.
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title">Upload Excel Usulan WIUP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="uploadForm">
                    <div class="mb-3">
                        <label class="form-label">Pilih file Excel (.xlsx)</label>
			<input type="file" class="form-control" id="uploadExcel" accept=".xlsx" required>
                    </div>
                    <div class="alert alert-light border rounded-4 small mb-0">
                        Unggah file Excel berisi titik koordinat calon WIUP. File ini akan digunakan untuk membentuk polygon usulan dan ditampilkan terpisah dari data existing.
                    </div>
                </form>
                <div id="uploadMessage" class="mt-3"></div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-4" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary rounded-4" id="submitUpload">Upload Sekarang</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="pdfModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header">
                <h5 class="modal-title">Dokumen SK</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <iframe id="pdfFrame" style="width:100%; height:80vh; border:none;"></iframe>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>
<script>

let map, statusChart, komoditasChart, activeHighlightLayer, activeFeatureLayer, polygonLayer;
let nomorSkTerpilih = '';

let dataTabel = [];
let kolomSort = '';
let arahSort = 'asc';
let halamanAktif = 1;
const jumlahPerHalaman = 25;

let arahkanKePetaSetelahModalTertutup = false;
let pemicuModalUpload = null;
let coordsUsulan = null;
let infoUsulan = null;
let overlapNomorSk = [];
let hasilOverlapTerakhir = [];
let modeUkurAktif = false;
let titikUkur = [];
let garisUkur = null;
let markerUkur = [];
let labelUkur = null;
let markerLokasiSaya = null;
let circleLokasiSaya = null;
let watchLokasiSayaId = null;

function simpanDataUploadWiup() {
    const data = {
        coordsUsulan: coordsUsulan,
        infoUsulan: infoUsulan
    };
    sessionStorage.setItem('uploadWiupData', JSON.stringify(data));
}

function pulihkanDataUploadWiup() {
    const raw = sessionStorage.getItem('uploadWiupData');
    if (!raw) return false;

    try {
        const data = JSON.parse(raw);
        coordsUsulan = data.coordsUsulan || null;
        infoUsulan = data.infoUsulan || null;
        return true;
    } catch (error) {
        console.error('Gagal memulihkan data upload WIUP:', error);
        return false;
    }
}

function hapusDataUploadWiup() {
    sessionStorage.removeItem('uploadWiupData');
}

function simpanDataOverlap() {
    const data = {
        hasilOverlapTerakhir: hasilOverlapTerakhir,
        overlapNomorSk: overlapNomorSk
    };
    sessionStorage.setItem('overlapDataState', JSON.stringify(data));
}

function pulihkanDataOverlap() {
    const raw = sessionStorage.getItem('overlapDataState');
    if (!raw) return false;

    try {
        const data = JSON.parse(raw);
        hasilOverlapTerakhir = data.hasilOverlapTerakhir || [];
        overlapNomorSk = data.overlapNomorSk || [];
        return true;
    } catch (error) {
        console.error('Gagal memulihkan data overlap:', error);
        return false;
    }
}

function hapusDataOverlap() {
    sessionStorage.removeItem('overlapDataState');
}

async function getGeoJsonData() {
    const response = await fetch('get_iup_geojson.php');
    return await response.json();
}

function getFilterValues() {
    return {
        kabupaten: document.getElementById('kabupatenFilter').value,
        komoditas: document.getElementById('komoditasFilter').value,
        status: document.getElementById('statusFilter').value,
        keyword: document.getElementById('searchInput').value.toLowerCase().trim()
    };
}

function getFilteredFeatures(features) {
    const filter = getFilterValues();

    return (features || []).filter(feature => {
        const p = feature.properties || {};
        const nama = String(p.NAME || '').toLowerCase();
        const nomorSk = String(p.NOMOR_SK || '').toLowerCase();
        const nib = String(p.NIB || '').toLowerCase();

        return (!filter.kabupaten || p.KABUPATEN === filter.kabupaten)
            && (!filter.komoditas || p.KOMODITAS === filter.komoditas)
            && (!filter.status || p.STATUS === filter.status)
            && (!filter.keyword || nama.includes(filter.keyword) || nomorSk.includes(filter.keyword) || nib.includes(filter.keyword));
    });
}

async function refreshDashboard() {
    initMap();
    await loadStats();
    await loadStatusChart();
    await loadKomoditasChart();
    await loadTable();
}

async function loadFilterOptions() {
    const geojson = await getGeoJsonData();
    const kabupatenSet = new Set();
    const komoditasSet = new Set();
    const statusSet = new Set();

    (geojson.features || []).forEach(feature => {
        const p = feature.properties || {};
        if (p.KABUPATEN) kabupatenSet.add(p.KABUPATEN);
        if (p.KOMODITAS) komoditasSet.add(p.KOMODITAS);
        if (p.STATUS) statusSet.add(p.STATUS);
    });

    isiSelect('kabupatenFilter', kabupatenSet);
    isiSelect('komoditasFilter', komoditasSet);
    isiSelect('statusFilter', statusSet);
}


function isiSelect(id, dataSet) {
    const select = document.getElementById(id);
    const firstOption = select.options[0] ? select.options[0].outerHTML : '';
    select.innerHTML = firstOption;

    [...dataSet].sort().forEach(value => {
        const option = document.createElement("option");
        option.value = value;
        option.textContent = value;
        select.appendChild(option);
    });
}

function formatNumber(value, digits = 0) {
    return new Intl.NumberFormat('id-ID', { minimumFractionDigits: digits, maximumFractionDigits: digits }).format(Number(value || 0));
}

function formatLuasHa(meterPersegi) {
    const ha = (Number(meterPersegi || 0) / 10000);
    return formatNumber(ha, 2);
}

function formatJarakMeter(meter) {
    if (meter >= 1000) {
        return `${formatNumber(meter / 1000, 2)} km`;
    }
    return `${formatNumber(meter, 2)} m`;
}

function tampilkanLokasiSaya() {
    if (!navigator.geolocation) {
        alert('Browser ini tidak mendukung lokasi.');
        return;
    }

    navigator.geolocation.getCurrentPosition(
        function (position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            const accuracy = position.coords.accuracy;

            if (markerLokasiSaya && map) {
                map.removeLayer(markerLokasiSaya);
            }

            if (circleLokasiSaya && map) {
                map.removeLayer(circleLokasiSaya);
            }

            markerLokasiSaya = L.marker([lat, lng]).addTo(map)
                .bindPopup(`
                    <b>Posisi Anda</b><br>
                    Lat: ${lat.toFixed(6)}<br>
                    Lng: ${lng.toFixed(6)}<br>
                    Akurasi: ± ${Math.round(accuracy)} meter
                `);

            circleLokasiSaya = L.circle([lat, lng], {
                radius: accuracy,
                weight: 1,
                color: '#16a34a',
                fillColor: '#16a34a',
                fillOpacity: 0.12
            }).addTo(map);

            map.setView([lat, lng], 17);
            markerLokasiSaya.openPopup();
        },
        function (error) {
            let pesan = 'Gagal mengambil lokasi.';
            if (error.code === 1) pesan = 'Izin lokasi ditolak.';
            if (error.code === 2) pesan = 'Lokasi tidak tersedia.';
            if (error.code === 3) pesan = 'Permintaan lokasi timeout.';
            alert(pesan);
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    );
}

function toggleTrackingLokasi() {
    if (!navigator.geolocation) {
        alert('Browser ini tidak mendukung lokasi.');
        return;
    }

    const btn = document.getElementById('btnTrackingLokasi');

    if (watchLokasiSayaId !== null) {
        navigator.geolocation.clearWatch(watchLokasiSayaId);
        watchLokasiSayaId = null;

        if (btn) {
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-success');
            btn.innerHTML = '<i class="bi bi-broadcast me-1"></i>Tracking Lokasi';
        }
        return;
    }

    watchLokasiSayaId = navigator.geolocation.watchPosition(
        function (position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            const accuracy = position.coords.accuracy;

            if (markerLokasiSaya) {
                markerLokasiSaya.setLatLng([lat, lng]);
            } else {
                markerLokasiSaya = L.marker([lat, lng]).addTo(map);
            }

            if (circleLokasiSaya) {
                circleLokasiSaya.setLatLng([lat, lng]);
                circleLokasiSaya.setRadius(accuracy);
            } else {
                circleLokasiSaya = L.circle([lat, lng], {
                    radius: accuracy,
                    weight: 1,
                    color: '#16a34a',
                    fillColor: '#16a34a',
                    fillOpacity: 0.12
                }).addTo(map);
            }

            markerLokasiSaya.bindPopup(`
                <b>Posisi Anda</b><br>
                Lat: ${lat.toFixed(6)}<br>
                Lng: ${lng.toFixed(6)}<br>
                Akurasi: ± ${Math.round(accuracy)} meter
            `);
        },
        function (error) {
            console.error('Gagal tracking lokasi:', error);
            alert('Tracking lokasi gagal dijalankan.');

            if (watchLokasiSayaId !== null) {
                navigator.geolocation.clearWatch(watchLokasiSayaId);
                watchLokasiSayaId = null;
            }
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    );

    if (btn) {
        btn.classList.remove('btn-outline-success');
        btn.classList.add('btn-success');
        btn.innerHTML = '<i class="bi bi-broadcast me-1"></i>Stop Tracking';
    }
}

function bukaPDF(link) {
    document.getElementById('pdfFrame').src = link;

    const modal = new bootstrap.Modal(document.getElementById('pdfModal'));
    modal.show();
}

function resetUkur() {
    titikUkur = [];

    if (garisUkur && map) {
        map.removeLayer(garisUkur);
        garisUkur = null;
    }

    markerUkur.forEach(marker => {
        if (map) map.removeLayer(marker);
    });
    markerUkur = [];

    if (labelUkur && map) {
        map.removeLayer(labelUkur);
        labelUkur = null;
    }

    const clearBtn = document.getElementById('clearMeasureBtn');
    if (clearBtn) clearBtn.style.display = 'none';
}

function aturInteraksiPolygonSaatUkur(nonaktifkan) {
    const pointerValue = nonaktifkan ? 'none' : 'auto';

    if (polygonLayer) {
        polygonLayer.eachLayer(layer => {
            if (layer._path) {
                layer._path.style.pointerEvents = pointerValue;
            }
            if (nonaktifkan && layer.closePopup) {
                layer.closePopup();
            }
        });
    }

    if (activeHighlightLayer) {
        activeHighlightLayer.eachLayer(layer => {
            if (layer._path) {
                layer._path.style.pointerEvents = pointerValue;
            }
            if (nonaktifkan && layer.closePopup) {
                layer.closePopup();
            }
        });
    }

    if (window.usulanLayer && window.usulanLayer._path) {
        window.usulanLayer._path.style.pointerEvents = pointerValue;
        if (nonaktifkan && window.usulanLayer.closePopup) {
            window.usulanLayer.closePopup();
        }
    }
}

function toggleModeUkur() {
    modeUkurAktif = !modeUkurAktif;

    const btn = document.getElementById('toggleMeasureBtn');
    const clearBtn = document.getElementById('clearMeasureBtn');

    resetUkur();

    if (modeUkurAktif) {
        btn.classList.remove('btn-outline-dark');
        btn.classList.add('btn-dark');
        btn.innerHTML = '<i class="bi bi-rulers me-1"></i>Mode Ukur Aktif';
        if (map) map.getContainer().style.cursor = 'crosshair';
        aturInteraksiPolygonSaatUkur(true);
    } else {
        btn.classList.remove('btn-dark');
        btn.classList.add('btn-outline-dark');
        btn.innerHTML = '<i class="bi bi-rulers me-1"></i>Ukur Jarak';
        if (map) map.getContainer().style.cursor = '';
        if (clearBtn) clearBtn.style.display = 'none';
        aturInteraksiPolygonSaatUkur(false);
    }
}

function prosesKlikUkur(e) {
    if (!modeUkurAktif || !map) return;

    if (titikUkur.length >= 2) {
        resetUkur();
    }

    titikUkur.push([e.latlng.lat, e.latlng.lng]);

    const marker = L.circleMarker([e.latlng.lat, e.latlng.lng], {
        radius: 5,
        color: '#dc2626',
        fillColor: '#dc2626',
        fillOpacity: 1,
        weight: 2
    }).addTo(map);

    markerUkur.push(marker);

    if (titikUkur.length === 2) {
        garisUkur = L.polyline(titikUkur, {
            color: '#dc2626',
            weight: 3,
            dashArray: '6,4'
        }).addTo(map);

        const jarak = map.distance(titikUkur[0], titikUkur[1]);

        const titikTengah = [
            (titikUkur[0][0] + titikUkur[1][0]) / 2,
            (titikUkur[0][1] + titikUkur[1][1]) / 2
        ];

        labelUkur = L.marker(titikTengah, {
            interactive: false,
            icon: L.divIcon({
                className: 'label-ukur-jarak',
                html: `<div>${formatJarakMeter(jarak)}</div>`
            })
        }).addTo(map);

        const clearBtn = document.getElementById('clearMeasureBtn');
        if (clearBtn) clearBtn.style.display = 'inline-flex';

        modeUkurAktif = false;
        aturInteraksiPolygonSaatUkur(false);

        const btn = document.getElementById('toggleMeasureBtn');
        if (btn) {
            btn.classList.remove('btn-dark');
            btn.classList.add('btn-outline-dark');
            btn.innerHTML = '<i class="bi bi-rulers me-1"></i>Ukur Jarak';
        }

        if (map) {
            map.getContainer().style.cursor = '';
        }
    }
}

function getPolygonColor(status) {
    if (!status) return "#6b7280";

    const s = String(status).toLowerCase().trim();

    // SIPB
    if (s.includes("sipb")) {
        return "#2563eb";
    }

    // IUP Operasi Produksi
    if (
        s.includes("operasi produksi") ||
        s.includes("iup op") ||
        s.includes("op ") ||
        s === "op" ||
        s.includes("iup operasi")
    ) {
        return "#198754";
    }

    // IUP Eksplorasi
    if (
        s.includes("eksplorasi") ||
        s.includes("iup eks") ||
        s.includes("eks ")
    ) {
        return "#facc15";
    }

    return "#6b7280";
}

function resetPolygonStyle(layer) {
    if (!layer || !layer.feature) return;

    const p = layer.feature.properties || {};
    layer.setStyle({
        color: getPolygonColor(p.STATUS),
        weight: 2,
        fillColor: getPolygonColor(p.STATUS),
        fillOpacity: 0.35
    });
}

function gambarUsulanWiup(zoomKeUsulan = false) {
    if (!coordsUsulan || !coordsUsulan.length || !map) return;

    if (window.usulanLayer) {
        map.removeLayer(window.usulanLayer);
    }

    window.usulanLayer = L.polygon(coordsUsulan, {
        color: '#7c3aed',
        weight: 3,
        dashArray: '6,4',
        fillColor: '#a78bfa',
        fillOpacity: 0.45
    }).addTo(map);

    const legendUsulan = document.getElementById('legendUsulan');
    if (legendUsulan) {
        legendUsulan.style.display = 'flex';
    }

    if (zoomKeUsulan && window.usulanLayer.getBounds().isValid()) {
        map.fitBounds(window.usulanLayer.getBounds(), { padding: [30, 30] });
    }
}

async function cekOverlapUsulan() {
    if (!coordsUsulan || !coordsUsulan.length) return [];

    try {
        const response = await fetch('get_iup_geojson.php');
        const geojsonExisting = await response.json();

        const ring = coordsUsulan.map(c => [c[1], c[0]]);

        const titikAwal = ring[0];
        const titikAkhir = ring[ring.length - 1];

        if (
            titikAwal[0] !== titikAkhir[0] ||
            titikAwal[1] !== titikAkhir[1]
        ) {
            ring.push(titikAwal);
        }

        const usulanPolygon = turf.polygon([ring]);

        const hasilOverlap = [];

        const luasUsulan = turf.area(usulanPolygon);

        (geojsonExisting.features || []).forEach(feature => {
            try {
                const overlap = turf.booleanIntersects(usulanPolygon, feature);

                if (overlap) {
                    const p = feature.properties || {};

                    let luasOverlap = 0;
                    let persenOverlap = 0;

                    try {
                        const irisan = turf.intersect(usulanPolygon, feature);

                        if (irisan) {
                            luasOverlap = turf.area(irisan);
                            persenOverlap = luasUsulan > 0 ? (luasOverlap / luasUsulan) * 100 : 0;
                        }
                    } catch (errIrisan) {
                        console.warn('Gagal hitung irisan:', errIrisan);
                    }

                    hasilOverlap.push({
                        nama: p.NAME || '-',
                        nomorSk: p.NOMOR_SK || '-',
                        status: p.STATUS || '-',
                        kabupaten: p.KABUPATEN || '-',
                        komoditas: p.KOMODITAS || '-',
                        luasOverlap: luasOverlap,
                        persenOverlap: persenOverlap
                    });
                }
            } catch (e) {
                console.warn('Feature gagal dicek:', e);
            }
        });

        overlapNomorSk = hasilOverlap.map(item => String(item.nomorSk || '').trim());
        hasilOverlapTerakhir = hasilOverlap;

        simpanDataOverlap();

        return hasilOverlap;
    } catch (error) {
        console.error('Gagal cek overlap:', error);
        return [];
    }
}

function tampilkanRingkasanOverlap() {
    const box = document.getElementById('ringkasanOverlap');
    const elJumlah = document.getElementById('overlapJumlah');
    const elLuas = document.getElementById('overlapLuas');
    const elTerbesar = document.getElementById('overlapTerbesar');

    if (!hasilOverlapTerakhir || hasilOverlapTerakhir.length === 0) {
        box.style.display = 'none';
        return;
    }

    const jumlah = hasilOverlapTerakhir.length;

    const totalLuas = hasilOverlapTerakhir.reduce((total, item) => {
        return total + Number(item.luasOverlap || 0);
    }, 0);

    const terbesar = [...hasilOverlapTerakhir].sort((a, b) => {
        return Number(b.luasOverlap || 0) - Number(a.luasOverlap || 0);
    })[0];

    elJumlah.textContent = jumlah;
    elLuas.textContent = `${formatLuasHa(totalLuas)} Ha`;
    elTerbesar.textContent = terbesar
        ? `${terbesar.nama} (${formatLuasHa(terbesar.luasOverlap)} Ha)`
        : '-';

    box.style.display = 'block';
}

function tampilkanDetailOverlap() {
    const wrapper = document.getElementById('detailOverlapWrapper');
    const body = document.getElementById('detailOverlapBody');

    if (!hasilOverlapTerakhir || hasilOverlapTerakhir.length === 0) {
        wrapper.style.display = 'none';
        body.innerHTML = `
            <tr>
                <td colspan="4" class="text-center text-muted">Belum ada data overlap</td>
            </tr>
        `;
        return;
    }

    wrapper.style.display = 'block';

    body.innerHTML = hasilOverlapTerakhir.map(item => `
        <tr>
            <td>${item.nama || '-'}</td>
            <td>${item.nomorSk || '-'}</td>
            <td class="text-end">${formatLuasHa(item.luasOverlap)} Ha</td>
            <td class="text-end">${formatNumber(item.persenOverlap, 2)}%</td>
        </tr>
    `).join('');
}

const northArrow = L.control({ position: 'topright' });

northArrow.onAdd = function (map) {
    const div = L.DomUtil.create('div', 'north-arrow-control');
    div.innerHTML = `
        <img src="assets/north-arrow.png" 
            style="width:42px; height:auto; opacity:0.9;">
    `;
    return div;
};

function simpanStatePeta() {
    if (!map) return;

    const pusat = map.getCenter();
    const zoom = map.getZoom();

    const statePeta = {
        lat: pusat.lat,
        lng: pusat.lng,
        zoom: zoom
    };

    sessionStorage.setItem('dashboardMapState', JSON.stringify(statePeta));
}

function pulihkanStatePeta() {
    if (!map) return false;

    const raw = sessionStorage.getItem('dashboardMapState');
    if (!raw) return false;

    try {
        const state = JSON.parse(raw);
        map.setView([state.lat, state.lng], state.zoom);
        return true;
    } catch (e) {
        console.error('Gagal memulihkan state peta:', e);
        return false;
    }
}

function ambilStatePeta() {
    const raw = sessionStorage.getItem('dashboardMapState');
    if (!raw) return null;

    try {
        return JSON.parse(raw);
    } catch (e) {
        return null;
    }
}

function simpanBasemapAktif(namaBasemap) {
    sessionStorage.setItem('dashboardBasemap', namaBasemap);
}

function ambilBasemapAktif() {
    return sessionStorage.getItem('dashboardBasemap') || 'osm';
}

function initMap() {
    if (map) {
        map.remove();
    }

    map = L.map('map').setView([2.5, 99], 7);

    const statePeta = ambilStatePeta();
    if (statePeta) {
        map.setView([statePeta.lat, statePeta.lng], statePeta.zoom);
    }

    map.on('click', prosesKlikUkur);

    map.on('moveend', function () {
        if (!map) return;

        const center = map.getCenter();
        const zoom = map.getZoom();

        sessionStorage.setItem('dashboardMapState', JSON.stringify({
            lat: center.lat,
            lng: center.lng,
            zoom: zoom
        }));
    });

    northArrow.addTo(map);

    L.control.scale({
        position: 'bottomleft',
        imperial: false,
        metric: true
    }).addTo(map);

    // OSM
    const osm = L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }
    );

    // Google Satellite
    const googleSat = L.tileLayer(
        'https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}',
        {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: '© Google'
        }
    );

    // Google Hybrid
    const googleHybrid = L.tileLayer(
        'https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}',
        {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: '© Google'
        }
    );

    // ESRI
    const esriSat = L.tileLayer(
        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
        {
            maxZoom: 19,
            attribution: 'Tiles © Esri — Source: Esri, Maxar, Earthstar Geographics'
        }
    );

    const openTopo = L.tileLayer(
        'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png',
        {
            maxZoom: 17,
            attribution: 'Map data: © OpenStreetMap contributors, SRTM | Map style: © OpenTopoMap',
            subdomains: ['a', 'b', 'c']
        }
    );

    const daftarBasemap = {
        osm: osm,
        googleSat: googleSat,
        googleHybrid: googleHybrid,
        esriSat: esriSat,
        openTopo: openTopo
    };

    const basemapAktif = ambilBasemapAktif();
    (daftarBasemap[basemapAktif] || osm).addTo(map);

    // layer control
    L.control.layers({
        'OpenStreetMap': osm,
        'Google Satellite': googleSat,
        'Google Hybrid': googleHybrid,
        'ESRI Satellite': esriSat,
        'OpenTopoMap': openTopo
    }, null, {
        position: 'topright'
    }).addTo(map);

    map.on('baselayerchange', function (e) {
        if (e.name === 'OpenStreetMap') {
            simpanBasemapAktif('osm');
        } else if (e.name === 'Google Satellite') {
            simpanBasemapAktif('googleSat');
        } else if (e.name === 'Google Hybrid') {
            simpanBasemapAktif('googleHybrid');
        } else if (e.name === 'ESRI Satellite') {
            simpanBasemapAktif('esriSat');
        } else if (e.name === 'OpenTopoMap') {
            simpanBasemapAktif('openTopo');
        }
    });

    getGeoJsonData()
        .then(geojson => {
            const geojsonFilter = {
                type: 'FeatureCollection',
                features: getFilteredFeatures(geojson.features)
            };

            polygonLayer = L.geoJSON(geojsonFilter, {
                style: feature => {
                    const p = feature.properties || {};
                    const warna = getPolygonColor(p.STATUS);
                    const nomorSk = String(p.NOMOR_SK || '').trim();
                    const isOverlap = overlapNomorSk.includes(nomorSk);

                    return {
                        color: isOverlap ? '#dc2626' : warna,
                        weight: isOverlap ? 4 : 2,
                        fillColor: warna,
                        fillOpacity: isOverlap ? 0.55 : 0.35
                    };
                },
                onEachFeature: (feature, layer) => {
                    const p = feature.properties || {};

                    layer.bindPopup(`
                        <div style="min-width:220px">
                            <b>${p.NAME || '-'}</b><br><br>
                            <b>Status Izin:</b> ${p.STATUS || '-'}<br>
                            <b>Kabupaten:</b> ${p.KABUPATEN || '-'}<br>
                            <b>Komoditas:</b> ${p.KOMODITAS || '-'}<br>
                            <b>Luas:</b> ${p.LUAS_HA || '-'} Ha<br>
                            <b>No. SK:</b> ${p.NOMOR_SK || '-'}<br>
                            <b>Awal SK:</b> ${p.AWAL_SK || '-'}<br>
                            <b>Akhir SK:</b> ${p.AKHIR_SK || '-'}
                        </div>
                    `);

                    layer.on('popupopen', () => {
                        if (modeUkurAktif) layer.closePopup();
                    });

                    layer.on('click', e => {
                        if (modeUkurAktif) {
                            e.originalEvent?.preventDefault();
                            e.originalEvent?.stopPropagation();
                            layer.closePopup?.();
                            return false;
                        }

                        if (activeFeatureLayer && activeFeatureLayer !== layer) {
                            resetPolygonStyle(activeFeatureLayer);
                        }

                        activeFeatureLayer = layer;
                        nomorSkTerpilih = String(p.NOMOR_SK || '').trim();

                        layer.setStyle({
                            color: '#dc2626',
                            weight: 4,
                            fillColor: getPolygonColor(p.STATUS),
                            fillOpacity: 0.55
                        });

                        if (layer.bringToFront) layer.bringToFront();

                        const indexData = dataTabel.findIndex(item => {
                            const props = item.properties || {};
                            return String(props.NOMOR_SK || '').trim() === nomorSkTerpilih;
                        });

                        if (indexData !== -1) {
                            halamanAktif = Math.floor(indexData / jumlahPerHalaman) + 1;
                        }

                        tampilkanTabel(dataTabel);
                        updateSortIcons();
                    });
                }
            }).addTo(map);

            if (!statePeta && polygonLayer.getBounds().isValid()) {
                map.fitBounds(polygonLayer.getBounds());
            }
        })
        .catch(error => console.error('Gagal memuat GeoJSON:', error));

    setTimeout(() => gambarUsulanWiup(), 300);
    setTimeout(() => map.invalidateSize(), 500);
}


async function loadStats() {
    try {
        const geojson = await getGeoJsonData();
        const features = getFilteredFeatures(geojson.features);

        const ringkasan = features.reduce((acc, feature) => {
            const p = feature.properties || {};
            const status = String(p.STATUS || '').toUpperCase().trim();
            const luas = parseFloat(String(p.LUAS_HA || 0).replace(',', '.')) || 0;

            acc.totalIzin += 1;
            acc.totalLuas += luas;
            if (status === 'IUP OP') acc.iupOp += 1;
            else if (status === 'IUP EKS') acc.iupEks += 1;
            else if (status === 'SIPB') acc.sipb += 1;

            return acc;
        }, { totalIzin: 0, totalLuas: 0, iupOp: 0, iupEks: 0, sipb: 0 });

        document.getElementById('totalIzin').textContent = ringkasan.totalIzin.toLocaleString('id-ID');
        document.getElementById('totalLuas').textContent = ringkasan.totalLuas.toLocaleString('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        document.getElementById('iupOp').textContent = ringkasan.iupOp.toLocaleString('id-ID');
        document.getElementById('iupEks').textContent = ringkasan.iupEks.toLocaleString('id-ID');
        document.getElementById('sipb').textContent = ringkasan.sipb.toLocaleString('id-ID');
    } catch (error) {
        console.error('Gagal memuat statistik:', error);
    }
}


async function loadStatusChart() {
    try {
        const geojson = await getGeoJsonData();
        const features = getFilteredFeatures(geojson.features);

        let jumlahIupOp = 0;
        let jumlahIupEks = 0;
        let jumlahSipb = 0;

        features.forEach(feature => {
            const status = String(feature.properties?.STATUS || '').toUpperCase().trim();
            if (status === 'IUP OP') jumlahIupOp++;
            else if (status === 'IUP EKS') jumlahIupEks++;
            else if (status === 'SIPB') jumlahSipb++;
        });

        if (statusChart) statusChart.destroy();

        statusChart = new Chart(document.getElementById('statusChart'), {
            type: 'pie',
            data: {
                labels: ['IUP OP', 'IUP EKS', 'SIPB'],
                datasets: [{
                    data: [jumlahIupOp, jumlahIupEks, jumlahSipb],
                    backgroundColor: ['#198754', '#facc15', '#2563eb']
                }]
            },
            plugins: [ChartDataLabels],
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    datalabels: {
                        color: context => context.chart.data.labels[context.dataIndex] === 'IUP EKS' ? '#000000' : '#ffffff',
                        font: { weight: 'bold', size: 14 },
                        formatter: (value, context) => {
                            const data = context.chart.data.datasets[0].data;
                            const total = data.reduce((a, b) => a + b, 0);
                            if (!total || value === 0) return '';
                            return ((value / total) * 100).toFixed(1) + '%';
                        }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Gagal memuat grafik status:', error);
    }
}


async function loadKomoditasChart() {
    try {
        const geojson = await getGeoJsonData();
        const features = getFilteredFeatures(geojson.features);
        const hitungKomoditas = {};

        features.forEach(feature => {
            const komoditas = String(feature.properties?.KOMODITAS || '').trim();
            if (!komoditas) return;
            hitungKomoditas[komoditas] = (hitungKomoditas[komoditas] || 0) + 1;
        });

        const urut = Object.entries(hitungKomoditas)
            .sort((a, b) => b[1] - a[1])
            .slice(0, 10);

        if (komoditasChart) komoditasChart.destroy();

        komoditasChart = new Chart(document.getElementById('komoditasChart'), {
            type: 'bar',
            data: {
                labels: urut.map(item => item[0]),
                datasets: [{
                    label: 'Jumlah Izin',
                    data: urut.map(item => item[1]),
                    backgroundColor: '#F3B000'
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: { left: 10, right: 10, top: 10, bottom: 10 }
                },
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, ticks: { precision: 0 } },
                    y: { ticks: { autoSkip: false, font: { size: 11 } } }
                }
            }
        });
    } catch (error) {
        console.error('Gagal memuat grafik komoditas:', error);
    }
}


function statusBadge(status) {
    const s = String(status || '').toUpperCase().trim();

    if (s === 'IUP OP') {
        return `
            <span class="badge badge-status" style="background:#198754;color:#fff;">
                Operasi<br>Produksi
            </span>
        `;
    }

    if (s === 'IUP EKS') {
        return `
            <span class="badge badge-status" style="background:#facc15;color:#000;">
                Eksplorasi
            </span>
        `;
    }

    if (s === 'SIPB') {
        return `
            <span class="badge badge-status" style="background:#2563eb;color:#fff;">
                SIPB
            </span>
        `;
    }

    return `
        <span class="badge badge-status bg-secondary">
            ${status || '-'}
        </span>
    `;
}

function jenisIzinBadge(status, link) {

    const label = status || '-';

    if (link && link !== '-') {
        return `
            <button onclick="bukaPDF('lihat_sk.php?file=${encodeURIComponent(link)}')"
                class="btn btn-sm btn-outline-primary rounded-3">
                <i class="bi bi-file-earmark-pdf"></i> Lihat SK
            </button>
        `;
    }

    return `<span class="badge text-bg-secondary badge-status">${label}</span>`;
}

async function loadTable() {
    try {
        const tableBody = document.getElementById('tableBody');
        const geojson = await getGeoJsonData();
        const features = getFilteredFeatures(geojson.features);

        document.getElementById('judulTabel').textContent = `Data Izin Pertambangan (${features.length} izin)`;
        document.getElementById('subjudulTabel').textContent = 'Menampilkan data izin sesuai hasil filter dashboard.';

        dataTabel = features;
        halamanAktif = 1;

        if (!features.length) {
            tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Belum ada data yang sesuai filter.</td></tr>';
            renderPagination(0, 0, 0);
            return;
        }

        if (kolomSort) {
            sortTable(kolomSort, false);
        } else {
            tampilkanTabel(dataTabel);
        }
    } catch (error) {
        console.error('Gagal memuat tabel:', error);
    }
}


function tampilkanTabel(data) {
    const tableBody = document.getElementById('tableBody');
    const mulai = (halamanAktif - 1) * jumlahPerHalaman;
    const akhir = mulai + jumlahPerHalaman;
    const dataHalaman = data.slice(mulai, akhir);

    tableBody.innerHTML = dataHalaman.map(feature => {
        const p = feature.properties || {};

	const rowClass = String(p.NOMOR_SK || '').trim() === String(nomorSkTerpilih || '').trim()
    	    ? 'row-terpilih'
    	    : '';

        return `
            <tr class="${rowClass}" data-nomorsk="${String(p.NOMOR_SK || '').replace(/"/g, '&quot;')}">
                <td class="kolom-perusahaan">
                    <div class="fw-semibold">
                        <a href="#" onclick="fokusKePerusahaan('${String(p.NOMOR_SK || '').replace(/'/g, "\\'")}'); return false;" class="text-decoration-none">
                            ${p.NAME || '-'}
                        </a>
                    </div>
                    <div class="small text-muted">${p.NOMOR_SK || '-'}</div>
                </td>
                <td>${p.KABUPATEN || '-'}</td>
                <td>${p.KOMODITAS || '-'}</td>
                <td class="text-end">${formatNumber(parseFloat(String(p.LUAS_HA || 0).replace(',', '.')) || 0, 2)}</td>
                <td class="kolom-jenis-izin">${statusBadge(p.STATUS)}</td>
                <td class="kolom-dokumen-sk">${jenisIzinBadge(p.STATUS, p.LINK_SK)}</td>
            </tr>
        `;
    }).join('');

    renderPagination(data.length, mulai, akhir);
}

function fokusKePerusahaan(nomorSk) {
    if (!map || !dataTabel || !dataTabel.length) return;

    nomorSkTerpilih = String(nomorSk || '').trim();

    const indexData = dataTabel.findIndex(feature => {
        const p = feature.properties || {};
        return String(p.NOMOR_SK || '').trim() === nomorSkTerpilih;
    });

    if (indexData !== -1) {
        halamanAktif = Math.floor(indexData / jumlahPerHalaman) + 1;
    }

    tampilkanTabel(dataTabel);
    updateSortIcons();

    const target = dataTabel.find(feature => {
        const p = feature.properties || {};
        return String(p.NOMOR_SK || '').trim() === String(nomorSk || '').trim();
    });

    if (!target) return;

    if (activeHighlightLayer) {
        map.removeLayer(activeHighlightLayer);
    }

    activeHighlightLayer = L.geoJSON(target, {
        style: function(feature) {
            const p = feature.properties || {};
            return {
                color: '#dc2626',
                weight: 4,
                fillColor: getPolygonColor(p.STATUS),
                fillOpacity: 0.45
            };
        },
        onEachFeature: function(feature, layer) {
    	    const p = feature.properties || {};

            layer.bindPopup(`
        	<div style="min-width:220px">
            	    <b>${p.NAME || '-'}</b><br><br>
            	    <b>Status Izin:</b> ${p.STATUS || '-'}<br>
            	    <b>Kabupaten:</b> ${p.KABUPATEN || '-'}<br>
            	    <b>Komoditas:</b> ${p.KOMODITAS || '-'}<br>
            	    <b>Luas:</b> ${p.LUAS_HA || '-'} Ha<br>
            	    <b>No. SK:</b> ${p.NOMOR_SK || '-'}<br>
            	    <b>Awal SK:</b> ${p.AWAL_SK || '-'}<br>
            	    <b>Akhir SK:</b> ${p.AKHIR_SK || '-'}
             	</div>
    	    `);

            layer.on('popupopen', function() {
                if (modeUkurAktif) {
                    layer.closePopup();
                }
            });

    	    layer.on('click', function() {
        	nomorSkTerpilih = String(p.NOMOR_SK || '').trim();
        	tampilkanTabel(dataTabel);
        	updateSortIcons();

        	const row = document.querySelector(`tr[data-nomorsk="${CSS.escape(nomorSkTerpilih)}"]`);
        	if (row) {
            	    row.scrollIntoView({ behavior: 'smooth', block: 'center' });
        	}
    	    });
	}
    }).addTo(map);

    const bounds = activeHighlightLayer.getBounds();
    if (bounds.isValid()) {
        map.fitBounds(bounds, { padding: [30, 30] });
    }

    setTimeout(() => {
        activeHighlightLayer.eachLayer(layer => {
            if (layer.openPopup) {
                layer.openPopup();
            }
        });
    }, 300);
}

function renderPagination(totalData, mulai, akhir) {
    const totalHalaman = Math.ceil(totalData / jumlahPerHalaman);
    const pagination = document.getElementById('pagination');
    const infoHalaman = document.getElementById('infoHalaman');

    pagination.innerHTML = '';

    const dataAwal = totalData === 0 ? 0 : mulai + 1;
    const dataAkhir = Math.min(akhir, totalData);

    infoHalaman.textContent = `Menampilkan ${dataAwal}-${dataAkhir} dari ${totalData} data`;

    if (totalHalaman <= 1) return;

    const tombolSebelum = document.createElement('button');
    tombolSebelum.className = 'btn btn-sm btn-outline-secondary rounded-3';
    tombolSebelum.textContent = 'Sebelumnya';
    tombolSebelum.disabled = halamanAktif === 1;
    tombolSebelum.onclick = function () {
        if (halamanAktif > 1) {
            halamanAktif--;
            tampilkanTabel(dataTabel);
            updateSortIcons();
        }
    };
    pagination.appendChild(tombolSebelum);

    for (let i = 1; i <= totalHalaman; i++) {
        const btn = document.createElement('button');
        btn.className = i === halamanAktif
            ? 'btn btn-sm btn-primary rounded-3'
            : 'btn btn-sm btn-outline-primary rounded-3';
        btn.textContent = i;
        btn.onclick = function () {
            halamanAktif = i;
            tampilkanTabel(dataTabel);
            updateSortIcons();
        };
        pagination.appendChild(btn);
    }

    const tombolBerikut = document.createElement('button');
    tombolBerikut.className = 'btn btn-sm btn-outline-secondary rounded-3';
    tombolBerikut.textContent = 'Berikutnya';
    tombolBerikut.disabled = halamanAktif === totalHalaman;
    tombolBerikut.onclick = function () {
        if (halamanAktif < totalHalaman) {
            halamanAktif++;
            tampilkanTabel(dataTabel);
            updateSortIcons();
        }
    };
    pagination.appendChild(tombolBerikut);
}

function sortTable(kolom, ubahArah = true) {

    if (ubahArah) {
        if (kolomSort === kolom) {
            arahSort = arahSort === 'asc' ? 'desc' : 'asc';
        } else {
            kolomSort = kolom;
            arahSort = 'asc';
        }
        simpanSortDashboard();
    } else {
        kolomSort = kolom;
    }

    dataTabel.sort((a, b) => {
        const pa = a.properties || {};
        const pb = b.properties || {};

        let va = '';
        let vb = '';

        if (kolom === 'nama') {
            va = String(pa.NAME || '').toLowerCase();
            vb = String(pb.NAME || '').toLowerCase();
        }

        if (kolom === 'kabupaten') {
            va = String(pa.KABUPATEN || '').toLowerCase();
            vb = String(pb.KABUPATEN || '').toLowerCase();
        }

        if (kolom === 'komoditas') {
            va = String(pa.KOMODITAS || '').toLowerCase();
            vb = String(pb.KOMODITAS || '').toLowerCase();
        }

        if (kolom === 'status') {
            va = String(pa.STATUS || '').toLowerCase();
            vb = String(pb.STATUS || '').toLowerCase();
        }

        if (kolom === 'jenis') {
            va = String(pa.STATUS || '').toLowerCase();
            vb = String(pb.STATUS || '').toLowerCase();
        }

        if (kolom === 'luas') {
            va = parseFloat(String(pa.LUAS_HA || 0).replace(',', '.')) || 0;
            vb = parseFloat(String(pb.LUAS_HA || 0).replace(',', '.')) || 0;
        }

        if (va < vb) return arahSort === 'asc' ? -1 : 1;
        if (va > vb) return arahSort === 'asc' ? 1 : -1;
        return 0;
    });

    tampilkanTabel(dataTabel);
    updateSortIcons();
}

function updateSortIcons() {
    document.getElementById("iconSortNama").textContent = "";
    document.getElementById("iconSortKabupaten").textContent = "";
    document.getElementById("iconSortKomoditas").textContent = "";
    document.getElementById("iconSortLuas").textContent = "";
    document.getElementById("iconSortStatus").textContent = "";
    
    document.getElementById("sortNama").classList.remove("sortable-active");
    document.getElementById("sortKabupaten").classList.remove("sortable-active");
    document.getElementById("sortKomoditas").classList.remove("sortable-active");
    document.getElementById("sortLuas").classList.remove("sortable-active");
    document.getElementById("sortStatus").classList.remove("sortable-active");

    let icon = arahSort === 'asc' ? '▲' : '▼';

    if (kolomSort === 'nama') {
        document.getElementById("iconSortNama").textContent = icon;
        document.getElementById("sortNama").classList.add("sortable-active");
    }

    if (kolomSort === 'kabupaten') {
        document.getElementById("iconSortKabupaten").textContent = icon;
        document.getElementById("sortKabupaten").classList.add("sortable-active");
    }

    if (kolomSort === 'komoditas') {
        document.getElementById("iconSortKomoditas").textContent = icon;
        document.getElementById("sortKomoditas").classList.add("sortable-active");
    }

    if (kolomSort === 'luas') {
        document.getElementById("iconSortLuas").textContent = icon;
        document.getElementById("sortLuas").classList.add("sortable-active");
    }

    if (kolomSort === 'status') {
        document.getElementById("iconSortStatus").textContent = icon;
        document.getElementById("sortStatus").classList.add("sortable-active");
    }

}

function simpanFilterDashboard() {
    const dataFilter = {
        keyword: document.getElementById('searchInput').value,
        kabupaten: document.getElementById('kabupatenFilter').value,
        komoditas: document.getElementById('komoditasFilter').value,
        status: document.getElementById('statusFilter').value
    };

    sessionStorage.setItem('dashboardFilterState', JSON.stringify(dataFilter));
}

function pulihkanFilterDashboard() {
    const dataTersimpan = sessionStorage.getItem('dashboardFilterState');
    if (!dataTersimpan) return;

    const dataFilter = JSON.parse(dataTersimpan);

    document.getElementById('searchInput').value = dataFilter.keyword || '';
    document.getElementById('kabupatenFilter').value = dataFilter.kabupaten || '';
    document.getElementById('komoditasFilter').value = dataFilter.komoditas || '';
    document.getElementById('statusFilter').value = dataFilter.status || '';
}

function simpanSortDashboard() {
    const dataSort = {
        kolomSort: kolomSort,
        arahSort: arahSort
    };

    sessionStorage.setItem('dashboardSortState', JSON.stringify(dataSort));
}

function pulihkanSortDashboard() {
    const raw = sessionStorage.getItem('dashboardSortState');
    if (!raw) return;

    try {
        const dataSort = JSON.parse(raw);
        kolomSort = dataSort.kolomSort || '';
        arahSort = dataSort.arahSort || 'asc';
    } catch (error) {
        console.error('Gagal memulihkan sort dashboard:', error);
    }
}

function pasangEventFilter() {
    ['kabupatenFilter', 'komoditasFilter', 'statusFilter'].forEach(id => {
        document.getElementById(id).addEventListener('change', function () {
            simpanFilterDashboard();
            sessionStorage.removeItem('dashboardMapState');
            refreshDashboard();
        });
    });

    document.getElementById('searchInput').addEventListener('input', function () {
        simpanFilterDashboard();
        sessionStorage.removeItem('dashboardMapState');
        refreshDashboard();
    });
}



document.getElementById('refreshButton').addEventListener('click', function() {
    sessionStorage.removeItem('dashboardFilterState');
    sessionStorage.removeItem('dashboardMapState');
    sessionStorage.removeItem('dashboardBasemap');
    sessionStorage.removeItem('dashboardSortState');
    document.getElementById("searchInput").value = "";
    document.getElementById("kabupatenFilter").value = "";
    document.getElementById("komoditasFilter").value = "";
    document.getElementById("statusFilter").value = "";

    kolomSort = '';
    arahSort = 'asc';
    halamanAktif = 1;

    nomorSkTerpilih = '';

    if (watchLokasiSayaId !== null) {
        navigator.geolocation.clearWatch(watchLokasiSayaId);
        watchLokasiSayaId = null;
    }

    if (activeFeatureLayer) {
        resetPolygonStyle(activeFeatureLayer);
    }
    activeFeatureLayer = null;

    if (activeHighlightLayer) {
        map.removeLayer(activeHighlightLayer);
        activeHighlightLayer = null;
    }

    updateSortIcons();

    initMap();
    loadStats();
    loadStatusChart();
    loadKomoditasChart();
    loadTable();
});

async function catatLogUploadWiup(detail) {
    try {
        await fetch('log_upload_wiup.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                detail: detail || 'Upload WIUP berhasil'
            })
        });
    } catch (error) {
        console.error('Gagal mencatat log upload WIUP:', error);
    }
}

document.getElementById('submitUpload').addEventListener('click', async () => {
    const fileInput = document.getElementById('uploadExcel');
    const file = fileInput.files[0];
    const uploadMessage = document.getElementById('uploadMessage');

    if (!file) {
        uploadMessage.innerHTML = '<div class="alert alert-warning">Silakan pilih file Excel terlebih dahulu.</div>';
        return;
    }

    uploadMessage.innerHTML = '<div class="alert alert-info">Membaca file Excel...</div>';

    try {
        const reader = new FileReader();

        reader.onload = async function(event) {
            try {
                const data = new Uint8Array(event.target.result);
                const workbook = XLSX.read(data, { type: 'array' });

                const namaSheetKoordinat = workbook.SheetNames.includes('Koordinat_WIUP')
    		    ? 'Koordinat_WIUP'
    		    : workbook.SheetNames[0];

		const sheet = workbook.Sheets[namaSheetKoordinat];
		const jsonData = XLSX.utils.sheet_to_json(sheet);

                if (!jsonData.length) {
                    uploadMessage.innerHTML = '<div class="alert alert-warning">File Excel kosong atau tidak terbaca.</div>';
                    return;
                }

                const kolomWajib = ['urut', 'longitude', 'latitude'];
                const kolomFile = Object.keys(jsonData[0]).map(k =>
    		    String(k).trim().toLowerCase().replace(/\s+/g, '')
		);

                const kolomTidakAda = kolomWajib.filter(k => !kolomFile.includes(k));

                if (kolomTidakAda.length > 0) {
                    uploadMessage.innerHTML = `
                        <div class="alert alert-danger">
                            Format template tidak sesuai.<br>
                            Kolom yang belum ada: <strong>${kolomTidakAda.join(', ')}</strong>
                        </div>
                    `;
                    return;
                }

                const jumlahTitik = jsonData.length;

                uploadMessage.innerHTML = `
                    <div class="alert alert-success">
                        File Excel berhasil dibaca.<br>
                        Sheet: <strong>${namaSheetKoordinat}</strong><br>
                        Jumlah titik: <strong>${jumlahTitik}</strong>
                    </div>
                `;

                console.log('Data Excel valid:', jsonData);

		// urutkan titik berdasarkan kolom urut
		jsonData.sort((a, b) => Number(a.urut) - Number(b.urut));

		// ubah menjadi format koordinat leaflet
		const coords = jsonData.map(p => [
    		    Number(p.latitude),
    		    Number(p.longitude)
		]);

		coordsUsulan = coords;
		infoUsulan = jsonData[0] || null;

        simpanDataUploadWiup();

		console.log('Koordinat polygon:', coords);

		// hapus polygon usulan lama jika ada
		if (window.usulanLayer) {
    		    map.removeLayer(window.usulanLayer);
		}

		gambarUsulanWiup();

		if (window.usulanLayer && window.usulanLayer.getBounds().isValid()) {
		    map.fitBounds(window.usulanLayer.getBounds());
		}
		
        await catatLogUploadWiup('Upload WIUP berhasil');

        const hasilOverlap = await cekOverlapUsulan();
        initMap();

        setTimeout(() => {
            gambarUsulanWiup(true);
            tampilkanRingkasanOverlap();
            tampilkanDetailOverlap();
            arahkanKePetaSetelahModalTertutup = true;
        }, 500);

        if (hasilOverlap.length === 0) {
            uploadMessage.innerHTML = `
                <div class="alert alert-success">
                    File Excel berhasil dibaca.<br>
                    Jumlah titik: <strong>${jsonData.length}</strong><br>
                    Hasil analisis: <strong>Tidak terindikasi overlap</strong>
                </div>
            `;
        } else {
            const daftar = hasilOverlap.map(item => `
                <li>
                    <strong>${item.nama}</strong><br>
                    No. SK: ${item.nomorSk}<br>
                    Luas overlap: <strong>${formatLuasHa(item.luasOverlap)} Ha</strong><br>
                    Persentase overlap: <strong>${formatNumber(item.persenOverlap, 2)}%</strong>
                </li>
            `).join('');

            uploadMessage.innerHTML = `
                <div class="alert alert-warning">
                    File Excel berhasil dibaca.<br>
                    Jumlah titik: <strong>${jsonData.length}</strong><br>
                    Hasil analisis: <strong>Terdeteksi overlap dengan ${hasilOverlap.length} izin existing</strong>
                    <ul class="mb-0 mt-2">${daftar}</ul>
                </div>
            `;
        }

            } catch (err) {
                console.error(err);
                uploadMessage.innerHTML = '<div class="alert alert-danger">Gagal membaca isi file Excel.</div>';
            }
        };

        reader.readAsArrayBuffer(file);

    } catch (error) {
        console.error(error);
        uploadMessage.innerHTML = '<div class="alert alert-danger">Terjadi kesalahan saat membaca file.</div>';
    }
});

document.getElementById("sortNama").addEventListener("click", function() {
    sortTable("nama");
});

document.getElementById("sortKabupaten").addEventListener("click", function() {
    sortTable("kabupaten");
});

document.getElementById("sortKomoditas").addEventListener("click", function() {
    sortTable("komoditas");
});

document.getElementById("sortLuas").addEventListener("click", function() {
    sortTable("luas");
});

document.getElementById("sortStatus").addEventListener("click", function() {
    sortTable("status");
});

document.getElementById('refreshUsulanWiup').addEventListener('click', function() {
    if (window.usulanLayer) {
        map.removeLayer(window.usulanLayer);
        window.usulanLayer = null;
    }

    coordsUsulan = null;
    infoUsulan = null;
    overlapNomorSk = [];
    hasilOverlapTerakhir = [];

    hapusDataUploadWiup();
    hapusDataOverlap();

    const inputExcel = document.getElementById('uploadExcel');
    if (inputExcel) {
        inputExcel.value = '';
    }

    const pesanUpload = document.getElementById('uploadMessage');
    if (pesanUpload) {
        pesanUpload.innerHTML = '';
    }

    const legendUsulan = document.getElementById('legendUsulan');
    if (legendUsulan) {
        legendUsulan.style.display = 'none';
    }

    document.getElementById('ringkasanOverlap').style.display = 'none';
    document.getElementById('detailOverlapWrapper').style.display = 'none';
    document.getElementById('detailOverlapBody').innerHTML = `
    <tr>
        <td colspan="4" class="text-center text-muted">Belum ada data overlap</td>
    </tr>
`;
    nomorSkTerpilih = '';
    if (activeHighlightLayer) {
        map.removeLayer(activeHighlightLayer);
        activeHighlightLayer = null;
    }
    if (activeFeatureLayer) {
        resetPolygonStyle(activeFeatureLayer);
        activeFeatureLayer = null;
    }
    loadTable();
    initMap();
});

document.getElementById('toggleMeasureBtn').addEventListener('click', function() {
    toggleModeUkur();
});

document.getElementById('clearMeasureBtn').addEventListener('click', function() {
    resetUkur();
});

const fullscreenBtn = document.getElementById('fullscreenMapBtn');

fullscreenBtn.addEventListener('click', function () {
    const cardPeta = document.getElementById('cardPeta');
    document.body.classList.toggle('fullscreen-active');
    cardPeta.classList.toggle('map-fullscreen');

    if (cardPeta.classList.contains('map-fullscreen')) {
        fullscreenBtn.innerHTML = '<i class="bi bi-fullscreen-exit me-1"></i>Keluar Fullscreen';
    } else {
        fullscreenBtn.innerHTML = '<i class="bi bi-arrows-fullscreen me-1"></i>Fullscreen';
    }

    setTimeout(function () {
        map.invalidateSize();
    }, 300);
});

document.getElementById('btnLokasiSaya').addEventListener('click', tampilkanLokasiSaya);
document.getElementById('btnTrackingLokasi').addEventListener('click', toggleTrackingLokasi);

const uploadModalElement = document.getElementById('uploadModal');

uploadModalElement.addEventListener('show.bs.modal', function (event) {
    pemicuModalUpload = event.relatedTarget || null;
});

uploadModalElement.addEventListener('hidden.bs.modal', function () {
    if (pemicuModalUpload && typeof pemicuModalUpload.blur === 'function') {
        pemicuModalUpload.blur();
    }

    if (document.activeElement && typeof document.activeElement.blur === 'function') {
        document.activeElement.blur();
    }

    if (arahkanKePetaSetelahModalTertutup) {
        setTimeout(() => {
            const panelPeta = document.getElementById('panelPeta');
            if (panelPeta) {
                panelPeta.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }

            arahkanKePetaSetelahModalTertutup = false;
        }, 150);
    }
});

(async function init() {
    await loadFilterOptions();
    pulihkanFilterDashboard();
    pulihkanSortDashboard();
    pasangEventFilter();
    await refreshDashboard();

    const adaDataUpload = pulihkanDataUploadWiup();
    if (adaDataUpload) {
        gambarUsulanWiup();
    }

    const adaDataOverlap = pulihkanDataOverlap();
    if (adaDataOverlap) {
        tampilkanRingkasanOverlap();
        tampilkanDetailOverlap();
    }

    updateSortIcons();
})();
</script>
</body>
</html>
