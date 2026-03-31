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
        body {
            background: #f5f7fb;
            font-family: "Segoe UI", Tahoma, sans-serif;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background: url('./assets/logo-sumut.png') no-repeat center;
            background-size: 500px; /* diperbesar */
            opacity: 0.40; /* sedikit lebih terlihat */
            z-index: 0;
        }
        
        .login-wrap {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            z-index: 1;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            border-radius: 20px;

            background: rgba(255, 255, 255, 0.15); /* transparan */
            backdrop-filter: blur(7px); /* efek kaca */
            -webkit-backdrop-filter: blur(10px);

            border: 1px solid rgba(255,255,255,0.3);
            box-shadow: 0 20px 40px rgba(0,0,0,0.55);

            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(180deg, rgba(30,78,140,0.25), rgba(22,58,107,0.9));
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(7px);

            color: white;
            padding: 24px;
            text-align: center;
        }

        .login-title {
            font-weight: 700;
            margin-bottom: 4px;
        }

        .login-subtitle {
            font-size: 14px;
            opacity: 0.9;
        }

        .form-control {
            background: rgba(255,255,255,0.9);
            border: 1px solid rgba(0,0,0,0.08);
            border-radius: 10px;
        }

        .form-control:focus {
            background: rgba(255,255,255,0.85);
            border-color: #1E4E8C;
            box-shadow: 0 0 0 3px rgba(30,78,140,0.15);
        }

        a.small {
            color: #1d4ed8;
            font-weight: 500;
        }

    </style>
</head>
<body>
    <div class="login-wrap">
        <div class="card login-card">
            <div class="login-header">
                <div class="login-title">Dashboard Minerba</div>
                <div class="login-subtitle">Login Internal Cabang Dinas ESDM Wilayah II</div>
            </div>

            <div class="card-body p-4">
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
                        <input type="password" name="password" class="form-control" autocomplete="new-password" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Masuk</button>

                    <div class="text-center mt-3">
                        <a href="ubah_password.php" class="text-decoration-none small">Ubah password</a>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
</body>
</html>