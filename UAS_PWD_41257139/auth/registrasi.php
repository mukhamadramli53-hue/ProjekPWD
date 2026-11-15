<?php
declare(strict_types=1);

require_once __DIR__ . '/auth_check.php';
$pdo = require __DIR__ . '/../config/database.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $name = trim((string) ($_POST['nama'] ?? ''));
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');

    if (mb_strlen($name) < 2 || mb_strlen($name) > 100) {
        $error = 'Nama harus terdiri dari 2 sampai 100 karakter.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) {
        $error = 'Masukkan alamat email yang valid.';
    } elseif (strlen($password) < 10) {
        $error = 'Kata sandi harus memiliki minimal 10 karakter.';
    } else {
        $check = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
        $check->execute(['email' => $email]);
        if ($check->fetch()) {
            $error = 'Email sudah terdaftar.';
        } else {
            $insert = $pdo->prepare('INSERT INTO users (nama, email, password) VALUES (:nama, :email, :password)');
            $insert->execute([
                'nama' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
            ]);
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $pdo->lastInsertId();
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            header('Location: ' . app_url('admin.php'));
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Pengelola | Kopi Cirebon — Argon Dashboard</title>
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
            <i class="fa-solid fa-user-plus text-lg"></i>
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">Pendaftaran Pengelola</h1>
        <p class="text-xs sm:text-sm text-white/80 max-w-sm mt-1">Buat akun pengelola baru untuk sistem inventaris kedai.</p>
        
        <!-- Decorative blurred shape -->
        <div class="absolute -right-10 -bottom-10 w-44 h-44 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
    </div>

    <!-- Floating Auth Card -->
    <main class="relative -mt-20 max-w-md w-full mx-auto px-4 z-10 mb-12">
        <div class="bg-white rounded-2xl shadow-argon-xl p-8 border-0">
            <div class="text-center mb-6">
                <h2 class="text-lg font-bold text-slate-800">Registrasi Akun</h2>
                <p class="text-xs text-slate-400 mt-0.5">Daftarkan akun operasional kedai kopi.</p>
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
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Nama Lengkap</label>
                    <div class="relative">
                        <i class="fa-solid fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                        <input required maxlength="100" name="nama" placeholder="Nama pengelola" class="w-full pl-10 pr-4 py-2.5 text-xs rounded-xl border border-slate-200 text-slate-700 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all" value="<?= e($_POST['nama'] ?? '') ?>">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Alamat Email</label>
                    <div class="relative">
                        <i class="fa-solid fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                        <input required type="email" name="email" autocomplete="email" placeholder="nama@kopi-cirebon.local" class="w-full pl-10 pr-4 py-2.5 text-xs rounded-xl border border-slate-200 text-slate-700 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all" value="<?= e($_POST['email'] ?? '') ?>">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Kata Sandi (Minimal 10 Karakter)</label>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                        <input required minlength="10" type="password" name="password" autocomplete="new-password" placeholder="Minimal 10 karakter" class="w-full pl-10 pr-4 py-2.5 text-xs rounded-xl border border-slate-200 text-slate-700 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3 px-4 rounded-xl text-xs font-bold uppercase tracking-wider text-white bg-gradient-to-tl from-blue-600 to-indigo-600 shadow-md shadow-blue-500/25 hover:scale-[1.02] active:scale-95 transition-all">
                        Buat Akun Pengelola
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center text-xs text-slate-400">
                <span>Sudah memiliki akun?</span>
                <a class="font-bold text-blue-600 hover:underline ml-1" href="<?= e(app_url('auth/login.php')) ?>">Masuk Sekarang</a>
            </div>
        </div>
    </main>
</body>
</html>
