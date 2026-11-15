<?php
declare(strict_types=1);

require_once __DIR__ . '/auth_check.php';
$pdo = require __DIR__ . '/../config/database.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    $statement = $pdo->prepare('SELECT id, nama, email, password FROM users WHERE email = :email LIMIT 1');
    $statement->execute(['email' => $email]);
    $user = $statement->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_name'] = $user['nama'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        header('Location: ' . app_url('admin.php'));
        exit;
    }

    $error = 'Email atau kata sandi tidak sesuai.';
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk Pengelola | Kopi Cirebon — Argon Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Open Sans"', 'sans-serif'],
                    },
                    boxShadow: {
                        'argon-xl': '0 15px 35px rgba(50,50,93,0.1), 0 5px 15px rgba(0,0,0,0.07)',
                    }
                }
            }
        }
    </script>
</head>
<body class="m-0 font-sans antialiased bg-slate-100 text-slate-600 min-h-screen">
    <!-- Argon Header Curved Banner -->
    <div class="relative overflow-hidden min-h-[300px] m-4 rounded-3xl bg-gradient-to-tl from-blue-600 via-indigo-600 to-blue-700 flex flex-col items-center justify-center text-center p-6 text-white shadow-xl">
        <a href="<?= e(app_url()) ?>" class="inline-flex items-center gap-2 mb-4 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md text-white text-xs font-semibold hover:bg-white/20 transition-all">
            <i class="fa-solid fa-arrow-left text-[10px]"></i>
            <span>Kembali ke Toko</span>
        </a>
        <div class="flex items-center justify-center w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md text-white mb-2 shadow-md">
            <i class="fa-solid fa-mug-hot text-lg"></i>
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Selamat Datang Kembali!</h1>
        <p class="text-xs sm:text-sm text-white/80 max-w-sm mt-1">Masuk ke panel pengelola Kedai Kopi Cirebon.</p>
        
        <!-- Decorative blurred shape -->
        <div class="absolute -right-10 -bottom-10 w-44 h-44 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
    </div>

    <!-- Floating Auth Card -->
    <main class="relative -mt-20 max-w-md w-full mx-auto px-4 z-10 mb-12">
        <div class="bg-white rounded-2xl shadow-argon-xl p-8 border-0">
            <div class="text-center mb-6">
                <h2 class="text-lg font-bold text-slate-800">Masuk Akun Pengelola</h2>
                <p class="text-xs text-slate-400 mt-0.5">Kelola data menu, inventaris stok, dan katalog UMKM.</p>
            </div>

            <?php if ($error !== ''): ?>
                <div class="mb-5 rounded-xl bg-rose-50 border border-rose-200 p-3.5 text-xs font-semibold text-rose-700 flex items-center gap-2.5" role="alert">
                    <i class="fa-solid fa-triangle-exclamation text-rose-500 text-sm"></i>
                    <span><?= e($error) ?></span>
                </div>
            <?php endif; ?>

            <form method="post" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Alamat Email</label>
                    <div class="relative">
                        <i class="fa-solid fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                        <input required type="email" name="email" autocomplete="username" placeholder="admin@kopi-cirebon.local" class="w-full pl-10 pr-4 py-2.5 text-xs rounded-xl border border-slate-200 text-slate-700 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all" value="<?= e($_POST['email'] ?? '') ?>">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Kata Sandi</label>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                        <input required type="password" name="password" autocomplete="current-password" placeholder="••••••••••••" class="w-full pl-10 pr-4 py-2.5 text-xs rounded-xl border border-slate-200 text-slate-700 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3 px-4 rounded-xl text-xs font-bold uppercase tracking-wider text-white bg-gradient-to-tl from-blue-600 to-indigo-600 shadow-md shadow-blue-500/25 hover:scale-[1.02] active:scale-95 transition-all">
                        Masuk ke Dashboard
                    </button>
                </div>
            </form>

            <!-- Demo Account Info Box -->
            <div class="mt-6 p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-[11px] text-slate-500 space-y-1">
                <p class="font-bold text-slate-700 flex items-center gap-1.5">
                    <i class="fa-solid fa-key text-blue-500"></i>
                    <span>Akun Demo Tugas UAS:</span>
                </p>
                <p class="font-mono text-slate-600">Email: admin@kopi-cirebon.local</p>
                <p class="font-mono text-slate-600">Sandi: KopiCirebon!2026</p>
            </div>

            <div class="mt-6 text-center text-xs text-slate-400">
                <span>Belum punya akun?</span>
                <a class="font-bold text-blue-600 hover:underline ml-1" href="<?= e(app_url('auth/registrasi.php')) ?>">Daftar Pengelola</a>
            </div>
        </div>
    </main>
</body>
</html>
