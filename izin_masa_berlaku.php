<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Masa Berlaku Izin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
            letter-spacing: 0;
        }

        .page-subtitle {
            color: #6b7280;
            margin-bottom: .2rem;
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
            padding: 1.15rem 1.2rem;
        }

        .stat-card .icon-box {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
        }

        .stat-label {
            color: #6b7280;
            font-weight: 600;
            font-size: .92rem;
            margin-bottom: .2rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1.05;
            color: #111827;
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

        .status-chip {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,.08);
        }

        .status-kritis {
            background: #dc2626;
            color: #fff;
        }

        .status-waspada {
            background: #f59e0b;
            color: #fff;
        }

        .status-aman {
            background: #16a34a;
            color: #fff;
        }

        .status-berakhir {
            background: #6b7280;
            color: #fff;
        }

        .row-kritis td {
            background-color: #fee2e2 !important;
        }

        .row-waspada td {
            background-color: #fef3c7 !important;
        }

        .row-berakhir td {
            background-color: #e5e7eb !important;
        }

        .small-note {
            font-size: .9rem;
            color: #6b7280;
        }

        .insight-box {
            border-radius: 16px;
            background: linear-gradient(135deg, #eff6ff 0%, #f8fafc 100%);
            border: 1px solid #dbeafe;
            padding: 1rem 1.1rem;
            margin-bottom: 1rem;
        }

        .insight-title {
            font-size: .9rem;
            font-weight: 700;
            color: #1E4E8C;
            margin-bottom: .35rem;
        }

        .insight-text {
            font-size: .95rem;
            color: #374151;
            margin: 0;
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
        }

        .table tbody tr {
            transition: background-color .15s ease;
        }

        .table tbody tr:hover td {
            background-color: #eff6ff !important;
        }

        .fw-company {
            font-weight: 600;
            color: #1f2937;
        }

        .company-meta {
            font-size: .85rem;
            color: #6b7280;
            margin-top: 2px;
        }

        .btn-sk {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: .82rem;
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
        }

        .btn-sk i {
            font-size: .9rem;
        }

        .col-sk {
            max-width: 180px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sisa-hari {
            font-weight: 700;
            color: #111827;
            white-space: nowrap;
        }

        .table-title {
            font-weight: 700;
            color: #1f2937;
        }

        .info-data {
            font-size: .88rem;
            color: #6b7280;
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

    .col-nosk {
        color: #6b7280;
    }   
    </style>
</head>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

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
                    <a class="nav-link active" href="izin_masa_berlaku.php"><i class="bi bi-calendar-event"></i> Masa Berlaku Izin</a>
                    <a class="nav-link" href="log_aktivitas.php">
                        <i class="bi bi-clock-history"></i> Log Aktivitas
                    </a>
                </nav>
            </aside>
        </div>

        <div class="col-lg-10">
            <main class="content-wrap">

                <div class="page-header">
                    <div class="page-header-left">
                        <h1 class="page-title">Monitoring Masa Berlaku Izin</h1>
                        <div class="page-subtitle">Pemantauan izin berdasarkan tanggal akhir SK</div>
                        <div class="small-note">Halaman ini membantu mengidentifikasi izin yang sudah berakhir atau mendekati masa berakhir secara lebih cepat dan terarah.</div>
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

                <div class="row g-3 mb-3">
                    <div class="col-md-6 col-xl-3">
                        <div class="card card-soft stat-card h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="icon-box" style="background:#eff6ff;color:#1d4ed8;">
                                    <i class="bi bi-file-earmark-text"></i>
                                </div>
                                <div>
                                    <div class="stat-label">Total Izin</div>
                                    <div class="stat-value" id="totalIzin">0</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card card-soft stat-card h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="icon-box" style="background:#fee2e2;color:#dc2626;">
                                    <i class="bi bi-exclamation-octagon"></i>
                                </div>
                                <div>
                                    <div class="stat-label">Sudah Berakhir</div>
                                    <div class="stat-value" id="izinBerakhir">0</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card card-soft stat-card h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="icon-box" style="background:#fff7ed;color:#d97706;">
                                    <i class="bi bi-alarm"></i>
                                </div>
                                <div>
                                    <div class="stat-label">≤ 6 Bulan</div>
                                    <div class="stat-value" id="izin6Bulan">0</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card card-soft stat-card h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="icon-box" style="background:#fef3c7;color:#a16207;">
                                    <i class="bi bi-calendar-event"></i>
                                </div>
                                <div>
                                    <div class="stat-label">≤ 1 Tahun</div>
                                    <div class="stat-value" id="izin1Tahun">0</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-soft toolbar-card mb-3">
                    <div class="toolbar-title">
                        <i class="bi bi-funnel me-1"></i>Filter Monitoring
                    </div>

                    <div class="row g-3 align-items-center">
                        <div class="col-lg-3">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Cari perusahaan atau nomor SK">
                            </div>
                        </div>

                        <div class="col-lg-3">
                            <select class="form-select" id="kabupatenFilter">
                                <option value="">Semua Kabupaten</option>
                            </select>
                        </div>

                        <div class="col-lg-2">
                            <select class="form-select" id="statusIzinFilter">
                                <option value="">Semua Status Izin</option>
                            </select>
                        </div>

                        <div class="col-lg-2">
                            <select class="form-select" id="statusTempoFilter">
                                <option value="">Semua Status Waktu</option>
                                <option value="berakhir">Sudah Berakhir</option>
                                <option value="kritis">≤ 6 Bulan</option>
                                <option value="waspada">≤ 1 Tahun</option>
                                <option value="aman">&gt; 1 Tahun</option>
                            </select>
                        </div>

                        <div class="col-lg-2">
                            <button class="btn btn-outline-primary w-100" id="refreshButton">
                                <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                            </button>
                        </div>
                    </div>
                </div>

                <div class="insight-box">
                    <div class="insight-title">
                        <i class="bi bi-lightbulb me-1"></i>Insight Monitoring
                    </div>
                    <p class="insight-text" id="insightText">
                        Memuat ringkasan pemantauan masa berlaku izin...
                    </p>
                </div>

                <div class="card card-soft">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
                            <div>
                                <div class="table-title">Daftar Masa Berlaku Izin</div>
                                <div class="small-note">Urutan default berdasarkan izin yang paling dekat masa berakhirnya.</div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Perusahaan</th>
                                        <th>No. SK</th>
                                        <th>Status Izin</th>
                                        <th class="text-center">Dokumen SK</th>
                                        <th>Tanggal Berakhir</th>
                                        <th class="text-end">Sisa Hari</th>
                                        <th>Status Waktu</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">Memuat data...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="info-data mt-2" id="infoData">Memuat data</div>
                    </div>
                </div>

            </main>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDokumenSk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header">
                <h5 class="modal-title">Dokumen SK</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="height: 80vh;">
                <iframe id="iframeDokumenSk" src="" width="100%" height="100%" style="border:0;"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
let dataIzin = [];
let dataTampil = [];

function simpanFilterMonitoringIzin() {
    const state = {
        searchInput: document.getElementById('searchInput').value || '',
        kabupatenFilter: document.getElementById('kabupatenFilter').value || '',
        statusIzinFilter: document.getElementById('statusIzinFilter').value || '',
        statusTempoFilter: document.getElementById('statusTempoFilter').value || ''
    };

    sessionStorage.setItem('monitoringIzinFilterState', JSON.stringify(state));
}

function pulihkanFilterMonitoringIzin() {
    const raw = sessionStorage.getItem('monitoringIzinFilterState');
    if (!raw) return;

    try {
        const state = JSON.parse(raw);

        document.getElementById('searchInput').value = state.searchInput || '';
        document.getElementById('kabupatenFilter').value = state.kabupatenFilter || '';
        document.getElementById('statusIzinFilter').value = state.statusIzinFilter || '';
        document.getElementById('statusTempoFilter').value = state.statusTempoFilter || '';
    } catch (error) {
        console.error('Gagal memulihkan filter Monitoring Izin:', error);
    }
}

function parseTanggalIndonesia(tanggalStr) {
    if (!tanggalStr) return null;

    let teks = String(tanggalStr).trim();
    if (!teks || teks === '-') return null;

    if (/^\d{4}-\d{2}-\d{2}$/.test(teks)) {
        const d = new Date(teks + 'T00:00:00');
        return isNaN(d.getTime()) ? null : d;
    }

    if (/^\d{2}-\d{2}-\d{4}$/.test(teks)) {
        const [dd, mm, yyyy] = teks.split('-');
        const d = new Date(`${yyyy}-${mm}-${dd}T00:00:00`);
        return isNaN(d.getTime()) ? null : d;
    }

    if (/^\d{2}\/\d{2}\/\d{4}$/.test(teks)) {
        const [dd, mm, yyyy] = teks.split('/');
        const d = new Date(`${yyyy}-${mm}-${dd}T00:00:00`);
        return isNaN(d.getTime()) ? null : d;
    }

    const bulanIndonesia = {
        januari: '01',
        februari: '02',
        maret: '03',
        april: '04',
        mei: '05',
        juni: '06',
        juli: '07',
        agustus: '08',
        september: '09',
        oktober: '10',
        november: '11',
        desember: '12'
    };

    const teksLower = teks.toLowerCase().replace(/\s+/g, ' ').trim();
    const match = teksLower.match(/^(\d{1,2})\s+([a-zA-Z]+)\s+(\d{4})$/);

    if (match) {
        const dd = match[1].padStart(2, '0');
        const namaBulan = match[2];
        const yyyy = match[3];

        if (bulanIndonesia[namaBulan]) {
            const mm = bulanIndonesia[namaBulan];
            const d = new Date(`${yyyy}-${mm}-${dd}T00:00:00`);
            return isNaN(d.getTime()) ? null : d;
        }
    }

    const parsed = new Date(teks);
    return isNaN(parsed.getTime()) ? null : parsed;
}

function formatTanggal(tanggal) {
    if (!tanggal) return '-';

    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    }).format(tanggal);
}

function hitungSisaHari(tanggalBerakhir) {
    if (!tanggalBerakhir) return null;

    const hariIni = new Date();
    hariIni.setHours(0, 0, 0, 0);

    const target = new Date(tanggalBerakhir);
    target.setHours(0, 0, 0, 0);

    const selisihMs = target - hariIni;
    return Math.round(selisihMs / (1000 * 60 * 60 * 24));
}

function tentukanStatusWaktu(sisaHari) {
    if (sisaHari === null) {
        return { kode: 'aman', label: 'Tanggal belum tersedia', className: 'status-aman' };
    }

    if (sisaHari < 0) {
        return { kode: 'berakhir', label: 'Sudah Berakhir', className: 'status-berakhir' };
    }

    if (sisaHari <= 183) {
        return { kode: 'kritis', label: '≤ 6 Bulan', className: 'status-kritis' };
    }

    if (sisaHari <= 365) {
        return { kode: 'waspada', label: '≤ 1 Tahun', className: 'status-waspada' };
    }

    return { kode: 'aman', label: '> 1 Tahun', className: 'status-aman' };
}

function isiKabupaten(data) {
    const select = document.getElementById('kabupatenFilter');
    const daftar = [...new Set(data.map(item => item.kabupaten).filter(Boolean))].sort();

    select.innerHTML = '<option value="">Semua Kabupaten</option>';

    daftar.forEach(kab => {
        const option = document.createElement('option');
        option.value = kab;
        option.textContent = kab;
        select.appendChild(option);
    });
}

function isiStatusIzin(data) {
    const select = document.getElementById('statusIzinFilter');
    const daftar = [...new Set(data.map(item => item.statusIzin).filter(Boolean))].sort();

    select.innerHTML = '<option value="">Semua Status Izin</option>';

    daftar.forEach(status => {
        const option = document.createElement('option');
        option.value = status;
        option.textContent = status;
        select.appendChild(option);
    });
}

function updateStatistik(data) {
    const total = data.length;
    const berakhir = data.filter(item => item.statusWaktu.kode === 'berakhir').length;
    const enamBulan = data.filter(item => item.sisaHari !== null && item.sisaHari >= 0 && item.sisaHari <= 183).length;
    const satuTahun = data.filter(item => item.sisaHari !== null && item.sisaHari >= 0 && item.sisaHari <= 365).length;

    document.getElementById('totalIzin').textContent = total.toLocaleString('id-ID');
    document.getElementById('izinBerakhir').textContent = berakhir.toLocaleString('id-ID');
    document.getElementById('izin6Bulan').textContent = enamBulan.toLocaleString('id-ID');
    document.getElementById('izin1Tahun').textContent = satuTahun.toLocaleString('id-ID');
}

function updateInsight(data) {
    const insight = document.getElementById('insightText');

    const jumlahBerakhir = data.filter(item => item.statusWaktu.kode === 'berakhir').length;
    const jumlahKritis = data.filter(item => item.statusWaktu.kode === 'kritis').length;
    const jumlahWaspada = data.filter(item => item.statusWaktu.kode === 'waspada').length;

    if (!data.length) {
        insight.textContent = 'Tidak ada data yang sesuai dengan filter yang dipilih.';
        return;
    }

    if (jumlahBerakhir > 0) {
        insight.textContent = `Terdapat ${jumlahBerakhir.toLocaleString('id-ID')} izin yang sudah berakhir dan ${jumlahKritis.toLocaleString('id-ID')} izin yang akan berakhir dalam 6 bulan. Data ini perlu menjadi prioritas pemantauan.`;
        return;
    }

    if (jumlahKritis > 0) {
        insight.textContent = `Terdapat ${jumlahKritis.toLocaleString('id-ID')} izin yang akan berakhir dalam 6 bulan dan ${jumlahWaspada.toLocaleString('id-ID')} izin yang akan berakhir dalam 1 tahun.`;
        return;
    }

    if (jumlahWaspada > 0) {
        insight.textContent = `Tidak ada izin yang sudah berakhir atau kritis. Namun terdapat ${jumlahWaspada.toLocaleString('id-ID')} izin yang akan berakhir dalam 1 tahun.`;
        return;
    }

    insight.textContent = 'Seluruh data yang tampil saat ini berada dalam kategori masa berlaku yang relatif aman atau belum memiliki tanggal akhir yang tersedia.';
}

function bukaDokumenSk(url) {
    if (!url) return;

    const iframe = document.getElementById('iframeDokumenSk');
    iframe.src = url;

    const modalEl = document.getElementById('modalDokumenSk');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

document.getElementById('modalDokumenSk').addEventListener('hidden.bs.modal', function () {
    document.getElementById('iframeDokumenSk').src = '';
});

function renderTabel(data) {
    const tbody = document.getElementById('tableBody');
    const infoData = document.getElementById('infoData');

    if (!data.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center text-muted py-4">Tidak ada data yang sesuai filter.</td>
            </tr>
        `;
        infoData.textContent = 'Menampilkan 0 data';
        updateInsight([]);
        return;
    }

    tbody.innerHTML = data.map(item => {
        let rowClass = '';

        if (item.statusWaktu.kode === 'kritis') {
            rowClass = 'row-kritis';
        } else if (item.statusWaktu.kode === 'waspada') {
            rowClass = 'row-waspada';
        } else if (item.statusWaktu.kode === 'berakhir') {
            rowClass = 'row-berakhir';
        }

        let sisaHariText = '-';
        if (item.sisaHari !== null) {
            sisaHariText = item.sisaHari < 0
                ? `${Math.abs(item.sisaHari).toLocaleString('id-ID')} hari lalu`
                : `${item.sisaHari.toLocaleString('id-ID')} hari`;
        }

        return `
            <tr class="${rowClass}">
                <td>
                    <div class="fw-company">${item.nama}</div>
                    <div class="company-meta">${item.kabupaten || '-'}</div>
                </td>

                <!-- KOLOM BARU NO SK -->
                <td class="col-nosk" title="${item.nomorSk || ''}">
                    ${item.nomorSk || '-'}
                </td>

                <td>${item.statusIzin || '-'}</td>

                <td class="text-center">
                    ${
                        item.linkSk
                            ? `<button type="button"
                                    class="btn btn-outline-primary btn-sm btn-sk"
                                    onclick="bukaDokumenSk('lihat_sk.php?file=${encodeURIComponent(item.linkSk)}')"
                                    title="${item.nomorSk || 'Lihat SK'}">
                                    <i class="bi bi-file-earmark-pdf"></i> Lihat
                            </button>`
                            : `<span class="text-muted">-</span>`
                    }
                </td>

                <td>${formatTanggal(item.tanggalBerakhir)}</td>

                <td class="text-end">
                    <span class="sisa-hari">${sisaHariText}</span>
                </td>

                <td>
                    <span class="status-chip ${item.statusWaktu.className}">
                        ${item.statusWaktu.label}
                    </span>
                </td>
            </tr>
        `;
    }).join('');

    infoData.textContent = `Menampilkan ${data.length.toLocaleString('id-ID')} data`;
    updateInsight(data);
}

function terapkanFilter() {
    const keyword = document.getElementById('searchInput').value.toLowerCase().trim();
    const kabupaten = document.getElementById('kabupatenFilter').value;
    const statusIzin = document.getElementById('statusIzinFilter').value;
    const statusTempo = document.getElementById('statusTempoFilter').value;

    dataTampil = dataIzin.filter(item => {
        const cocokCari =
            !keyword ||
            item.nama.toLowerCase().includes(keyword) ||
            item.nomorSk.toLowerCase().includes(keyword);

        const cocokKabupaten =
            !kabupaten || item.kabupaten === kabupaten;

        const cocokStatusIzin =
            !statusIzin || item.statusIzin === statusIzin;

        const cocokStatusTempo =
            !statusTempo || item.statusWaktu.kode === statusTempo;

        return cocokCari && cocokKabupaten && cocokStatusIzin && cocokStatusTempo;
    });

    renderTabel(dataTampil);
}

async function loadData() {
    try {
        const response = await fetch('data/iup.geojson');
        const geojson = await response.json();

        dataIzin = (geojson.features || []).map(feature => {
            const p = feature.properties || {};
            const tanggalBerakhir = parseTanggalIndonesia(p.AKHIR_SK || p.tanggal_berakhir || '');
            const sisaHari = hitungSisaHari(tanggalBerakhir);
            const statusWaktu = tentukanStatusWaktu(sisaHari);

            return {
                nama: String(p.NAME || '-'),
                kabupaten: String(p.KABUPATEN || ''),
                statusIzin: String(p.STATUS || p.status_izin || '-'),
                nomorSk: String(p.NOMOR_SK || '-'),
                linkSk: String(
                    p.LINK_SK ||
                    p.link_sk ||
                    p.URL_SK ||
                    p.url_sk ||
                    p.FILE_SK ||
                    p.file_sk ||
                    p.SK_PDF ||
                    p.sk_pdf ||
                    ''
                ),
                tanggalBerakhir,
                sisaHari,
                statusWaktu
            };
        }).sort((a, b) => {
            if (a.sisaHari === null && b.sisaHari === null) return 0;
            if (a.sisaHari === null) return 1;
            if (b.sisaHari === null) return -1;
            return a.sisaHari - b.sisaHari;
        });

        isiKabupaten(dataIzin);
        isiStatusIzin(dataIzin);
        updateStatistik(dataIzin);

        pulihkanFilterMonitoringIzin();
        terapkanFilter();
    } catch (error) {
        console.error(error);
        document.getElementById('tableBody').innerHTML = `
            <tr>
                <td colspan="7" class="text-center text-danger py-4">Gagal memuat data masa berlaku izin.</td>
            </tr>
        `;
        document.getElementById('insightText').textContent = 'Terjadi kesalahan saat memuat data monitoring masa berlaku izin.';
    }
}

document.getElementById('searchInput').addEventListener('input', function () {
    simpanFilterMonitoringIzin();
    terapkanFilter();
});

document.getElementById('kabupatenFilter').addEventListener('change', function () {
    simpanFilterMonitoringIzin();
    terapkanFilter();
});

document.getElementById('statusIzinFilter').addEventListener('change', function () {
    simpanFilterMonitoringIzin();
    terapkanFilter();
});

document.getElementById('statusTempoFilter').addEventListener('change', function () {
    simpanFilterMonitoringIzin();
    terapkanFilter();
});

document.getElementById('refreshButton').addEventListener('click', function () {
    sessionStorage.removeItem('monitoringIzinFilterState');

    document.getElementById('searchInput').value = '';
    document.getElementById('kabupatenFilter').value = '';
    document.getElementById('statusIzinFilter').value = '';
    document.getElementById('statusTempoFilter').value = '';

    terapkanFilter();
});

loadData();
</script>
</body>
</html>