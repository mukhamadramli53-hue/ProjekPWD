<?php
$error = '';
$isEdit = false;
$editId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
$itemToEdit = null;

if ($editId !== false && $editId !== null && $editId > 0) {
    $fetchStmt = $pdo->prepare('SELECT * FROM produk_umkm WHERE id = :id LIMIT 1');
    $fetchStmt->execute(['id' => $editId]);
    $itemToEdit = $fetchStmt->fetch();
    if ($itemToEdit) {
        $isEdit = true;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $name = trim((string) ($_POST['nama'] ?? ''));
    $category = trim((string) ($_POST['kategori'] ?? ''));
    $description = trim((string) ($_POST['deskripsi'] ?? ''));
    $price = filter_var($_POST['harga'] ?? null, FILTER_VALIDATE_INT);
    $stock = filter_var($_POST['stok'] ?? null, FILTER_VALIDATE_INT);
    $gambar = trim((string) ($_POST['gambar'] ?? ''));
    if ($gambar === '') {
        $gambar = null;
    } elseif (mb_strlen($gambar) > 255) {
        $error = 'Path/URL gambar maksimal 255 karakter.';
    }
    $allowedCategories = ['Kopi', 'Minuman', 'Kudapan'];

    if ($error !== '') {
        // error already set
    } elseif (mb_strlen($name) < 2 || mb_strlen($name) > 120) {
        $error = 'Nama produk harus terdiri dari 2 sampai 120 karakter.';
    } elseif (!in_array($category, $allowedCategories, true)) {
        $error = 'Pilih kategori yang tersedia.';
    } elseif (mb_strlen($description) > 1000) {
        $error = 'Deskripsi maksimal 1.000 karakter.';
    } elseif ($price === false || $price < 1 || $price > 1000000000) {
        $error = 'Harga harus berupa bilangan bulat antara 1 dan 1.000.000.000.';
    } elseif ($stock === false || $stock < 0 || $stock > 1000000) {
        $error = 'Stok harus berupa bilangan bulat antara 0 dan 1.000.000.';
    } else {
        // Check duplicate name and category
        if ($isEdit) {
            $dupStmt = $pdo->prepare('SELECT id FROM produk_umkm WHERE nama = :nama AND kategori = :kategori AND id != :id LIMIT 1');
            $dupStmt->execute(['nama' => $name, 'kategori' => $category, 'id' => $editId]);
            if ($dupStmt->fetch()) {
                $error = "Produk dengan nama '{$name}' dalam kategori '{$category}' sudah digunakan oleh produk lain.";
            }
        } else {
            $dupStmt = $pdo->prepare('SELECT id, aktif FROM produk_umkm WHERE nama = :nama AND kategori = :kategori LIMIT 1');
            $dupStmt->execute(['nama' => $name, 'kategori' => $category]);
            $existing = $dupStmt->fetch();
            if ($existing) {
                if ((int) $existing['aktif'] === 1) {
                    $error = "Produk dengan nama '{$name}' dalam kategori '{$category}' sudah terdaftar di katalog. Silakan gunakan nama lain atau edit produk tersebut.";
                } else {
                    // Re-activate soft-deleted/inactive item
                    try {
                        $reactivate = $pdo->prepare(
                            'UPDATE produk_umkm SET deskripsi = :deskripsi, harga = :harga, stok = :stok, gambar = :gambar, aktif = 1 WHERE id = :id'
                        );
                        $reactivate->execute([
                            'deskripsi' => $description,
                            'harga' => $price,
                            'stok' => $stock,
                            'gambar' => $gambar,
                            'id' => (int) $existing['id']
                        ]);
                        if (ob_get_length()) {
                            ob_clean();
                        }
                        header('Location: ' . app_url('admin.php?route=produk&added=1'));
                        echo '<script>window.location.href=' . json_encode(app_url('admin.php?route=produk&added=1')) . ';</script>';
                        exit;
                    } catch (PDOException $e) {
                        $error = 'Gagal mengaktifkan kembali produk: ' . $e->getMessage();
                    }
                }
            }
        }

        if ($error === '') {
            try {
                if ($isEdit) {
                    $statement = $pdo->prepare(
                        'UPDATE produk_umkm 
                         SET nama = :nama, kategori = :kategori, deskripsi = :deskripsi, harga = :harga, stok = :stok, gambar = :gambar 
                         WHERE id = :id'
                    );
                    $statement->execute([
                        'nama' => $name,
                        'kategori' => $category,
                        'deskripsi' => $description,
                        'harga' => $price,
                        'stok' => $stock,
                        'gambar' => $gambar,
                        'id' => $editId,
                    ]);
                    if (ob_get_length()) {
                        ob_clean();
                    }
                    header('Location: ' . app_url('admin.php?route=produk&updated=1'));
                    echo '<script>window.location.href=' . json_encode(app_url('admin.php?route=produk&updated=1')) . ';</script>';
                    exit;
                } else {
                    $statement = $pdo->prepare(
                        'INSERT INTO produk_umkm (nama, kategori, deskripsi, harga, stok, gambar, aktif)
                         VALUES (:nama, :kategori, :deskripsi, :harga, :stok, :gambar, 1)'
                    );
                    $statement->execute([
                        'nama' => $name,
                        'kategori' => $category,
                        'deskripsi' => $description,
                        'harga' => $price,
                        'stok' => $stock,
                        'gambar' => $gambar,
                    ]);
                    if (ob_get_length()) {
                        ob_clean();
                    }
                    header('Location: ' . app_url('admin.php?route=produk&added=1'));
                    echo '<script>window.location.href=' . json_encode(app_url('admin.php?route=produk&added=1')) . ';</script>';
                    exit;
                }
            } catch (PDOException $e) {
                if ($e->getCode() === '23000' || str_contains($e->getMessage(), '1062')) {
                    $error = "Produk dengan nama '{$name}' dalam kategori '{$category}' sudah ada dalam katalog.";
                } else {
                    $error = 'Terjadi kesalahan basis data: ' . $e->getMessage();
                }
            }
        }
    }
}

$valNama = $_POST['nama'] ?? ($itemToEdit['nama'] ?? '');
$valKategori = $_POST['kategori'] ?? ($itemToEdit['kategori'] ?? '');
$valDeskripsi = $_POST['deskripsi'] ?? ($itemToEdit['deskripsi'] ?? '');
$valHarga = $_POST['harga'] ?? ($itemToEdit['harga'] ?? '');
$valStok = $_POST['stok'] ?? ($itemToEdit['stok'] ?? '');
$valGambar = $_POST['gambar'] ?? ($itemToEdit['gambar'] ?? '');
?>
<section class="max-w-3xl mx-auto space-y-6">
    <div class="relative flex flex-col min-w-0 break-words bg-white shadow-argon-xl rounded-2xl border-0 overflow-hidden">
        <!-- Card Header -->
        <div class="p-6 pb-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-800"><?= $isEdit ? 'Edit Produk Menu' : 'Tambah Menu Baru' ?></h3>
                <p class="text-xs text-slate-400"><?= $isEdit ? 'Perbarui informasi produk #' . (int) $editId : 'Lengkapi formulir untuk menambahkan varian menu ke katalog kedai.' ?></p>
            </div>
            <a href="<?= e(app_url('admin.php?route=produk')) ?>" class="text-xs font-bold text-slate-500 hover:text-blue-600 flex items-center gap-1.5 transition-colors">
                <i class="fa-solid fa-arrow-left text-[11px]"></i>
                <span>Kembali ke Katalog</span>
            </a>
        </div>

        <div class="p-6">
            <?php if ($error !== ''): ?>
                <div class="mb-5 rounded-xl bg-rose-50 border border-rose-200 p-4 text-xs font-semibold text-rose-700 flex items-center gap-2.5" role="alert">
                    <i class="fa-solid fa-triangle-exclamation text-rose-500 text-base"></i>
                    <span><?= e($error) ?></span>
                </div>
            <?php endif; ?>

            <form id="product-form" method="post" class="space-y-5">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Nama Produk <span class="text-rose-500">*</span></label>
                        <input required minlength="2" maxlength="120" name="nama" placeholder="Contoh: Es Kopi Susu Aren" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all" value="<?= e($valNama) ?>">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Kategori Menu <span class="text-rose-500">*</span></label>
                        <select required name="kategori" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                            <option value="">Pilih Kategori</option>
                            <?php foreach (['Kopi', 'Minuman', 'Kudapan'] as $category): ?>
                                <option value="<?= e($category) ?>" <?= $valKategori === $category ? 'selected' : '' ?>><?= e($category) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Deskripsi Produk</label>
                    <textarea maxlength="1000" name="deskripsi" rows="3" placeholder="Jelaskan cita rasa, racikan, atau keunikan menu ini…" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all"><?= e($valDeskripsi) ?></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Harga Jual (Rp) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                            <input required type="number" min="1" max="1000000000" step="1" name="harga" placeholder="20000" class="w-full rounded-xl border border-slate-200 pl-10 pr-4 py-2.5 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all" value="<?= e((string) $valHarga) ?>">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Jumlah Stok <span class="text-rose-500">*</span></label>
                        <input required type="number" min="0" max="1000000" step="1" name="stok" placeholder="10" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all" value="<?= e((string) $valStok) ?>">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Gambar Produk (Opsional)</label>
                    <input maxlength="255" name="gambar" placeholder="assets/img/produk/... atau URL gambar eksternal" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all" value="<?= e($valGambar) ?>">
                    <p class="mt-1 text-xs text-slate-400">Kosongkan jika ingin sistem mencocokkan foto secara otomatis berdasarkan nama menu.</p>
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                    <button id="btn-submit-product" type="submit" class="inline-flex items-center gap-2 px-6 py-3 text-xs font-bold uppercase tracking-wider text-white bg-gradient-to-tl from-blue-600 to-indigo-500 rounded-xl shadow-md shadow-blue-500/25 hover:scale-[1.02] active:scale-95 transition-all cursor-pointer">
                        <i class="fa-solid fa-<?= $isEdit ? 'pen-to-square' : 'floppy-disk' ?> text-xs"></i>
                        <span><?= $isEdit ? 'Simpan Perubahan' : 'Simpan Produk' ?></span>
                    </button>
                    <a href="<?= e(app_url('admin.php?route=produk')) ?>" class="inline-flex items-center px-5 py-3 text-xs font-bold uppercase tracking-wider text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>
<script>
    document.getElementById('product-form')?.addEventListener('submit', function() {
        const btn = document.getElementById('btn-submit-product');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin text-xs"></i> <span>Menyimpan...</span>';
        }
    });
</script>
