<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Dashboard Minerba</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-esdm: #f2d500;
            --primary-esdm-dark: #c9b100;
            --text-dark: #1f2937;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: linear-gradient(135deg, #111111 0%, #1f1f1f 100%);
            font-family: "Segoe UI", Tahoma, sans-serif;
            position: relative;
            min-height: 100vh;
            overflow: hidden;
            color: #1f2937;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background: url('./assets/logo-sumut.png') no-repeat center;
            background-size: 520px; /* sedikit diperbesar */
            opacity: 0.18; /* diperjelas */
            z-index: 0;
            pointer-events: none;
        }

        body::after {
            content: "";
            position: fixed;
            inset: 0;
            background:
                radial-gradient(circle at top left, rgba(242,213,0,0.10), transparent 35%),
                radial-gradient(circle at bottom right, rgba(242,213,0,0.08), transparent 30%);
            z-index: 0;
            pointer-events: none;
        }

        .login-wrap {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            z-index: 1;
        }

        .login-card {
            width: 100%;
            max-width: 430px;
            border-radius: 22px;

            background: rgba(255, 255, 255, 0.18); /* transparan */
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);

            border: 1px solid rgba(255,255,255,0.35);
            box-shadow: 0 25px 60px rgba(0,0,0,0.55);

            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #111111 0%, #1f1f1f 100%);
            color: #ffffff;
            padding: 26px 24px 22px;
            text-align: center;
            border-bottom: 4px solid var(--primary-esdm);
        }

        .login-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(242,213,0,0.12);
            color: var(--primary-esdm);
            border: 1px solid rgba(242,213,0,0.30);
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .login-title {
            font-weight: 800;
            margin-bottom: 4px;
            font-size: 1.5rem;
            letter-spacing: .2px;
        }

        .login-subtitle {
            font-size: 14px;
            color: rgba(255,255,255,0.82);
            line-height: 1.45;
        }

        .card-body {
            padding: 28px 24px 24px;
        }

        .form-label {
            font-weight: 700;
            color: #374151;
            margin-bottom: 8px;
            font-size: .92rem;
        }

        .form-control {
            min-height: 46px;
            background: rgba(255,255,255,0.15);
            border: 1px solid #d1d5db;
            border-radius: 12px;
            color: #111111;
            padding-left: 14px;
            padding-right: 14px;
        }

        .form-control:focus {
            background: #ffffff;
            border-color: var(--primary-esdm);
            box-shadow: 0 0 0 0.15rem rgba(242,213,0,0.18);
        }

        .btn-login {
            width: 100%;
            min-height: 46px;
            border-radius: 12px;
            border: 1px solid var(--primary-esdm);
            background: var(--primary-esdm);
            color: #111111;
            font-weight: 800;
            transition: all .2s ease;
        }

        .btn-login:hover,
        .btn-login:focus {
            background: var(--primary-esdm-dark);
            border-color: var(--primary-esdm-dark);
            color: #111111;
        }

        .alert {
            border-radius: 14px;
            font-size: .92rem;
            margin-bottom: 18px;
        }

        .login-footer-link {
            color: #111111;
            font-weight: 600;
            text-decoration: none;
            transition: all .2s ease;
            border-bottom: 1px solid transparent;
        }

        .login-footer-link:hover,
        .login-footer-link:focus {
            color: #111111;
            border-bottom-color: var(--primary-esdm);
        }

        .login-note {
            text-align: center;
            margin-top: 16px;
            font-size: .85rem;
            color: #6b7280;
        }

        .card-body {
            padding: 28px 24px 24px;
            background: rgba(255,255,255,0.55); /* layer dalam */
            backdrop-filter: blur(4px);
        }

        @media (max-width: 576px) {
            .login-wrap {
                padding: 16px;
            }

            .login-header {
                padding: 22px 18px 18px;
            }

            .card-body {
                padding: 22px 18px 20px;
            }

            .login-title {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrap">
        <div class="card login-card">
            <div class="login-header">
                <div class="login-badge">Dashboard Internal</div>
                <div class="login-title">Dashboard Minerba</div>
                <div class="login-subtitle">Login Internal Cabang Dinas ESDM Wilayah II</div>
            </div>

            <div class="card-body">
                <?php if ($error === '1'): ?>
                    <div class="alert alert-danger">Email atau password salah.</div>
                <?php endif; ?>

                <?php if ($success === 'password_changed'): ?>
                    <div class="alert alert-success">Password berhasil diubah. Silakan login kembali.</div>
                <?php endif; ?>

                <form action="proses_login.php" method="POST" autocomplete="off">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" autocomplete="username" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" autocomplete="current-password" required>
                    </div>

                    <button type="submit" class="btn btn-login">Masuk</button>

                    <div class="text-center mt-3">
                        <a href="ubah_password.php" class="login-footer-link">Ubah password</a>
                    </div>

                    <div class="login-note">
                        Akses terbatas untuk pengguna internal.
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>