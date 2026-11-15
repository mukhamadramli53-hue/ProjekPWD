<?php
$message = '';
$errors = [];
$allowedCategories = ['Kopi', 'Minuman', 'Kudapan'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    if (!isset($_FILES['csv']) || $_FILES['csv']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Berkas CSV gagal diunggah. Pilih berkas kembali dan coba lagi.';
    } elseif ($_FILES['csv']['size'] > 2 * 1024 * 1024) {
        $errors[] = 'Ukuran CSV maksimal 2 MB.';
    } elseif (!is_uploaded_file($_FILES['csv']['tmp_name'])) {
        $errors[] = 'Berkas unggahan tidak valid.';
    } else {
        $handle = fopen($_FILES['csv']['tmp_name'], 'rb');
        if ($handle === false) {
            $errors[] = 'Berkas CSV tidak dapat dibaca.';
        } else {
            $header = fgetcsv($handle, 0, ',', '"', '');
            if (is_array($header) && isset($header[0])) {
                $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $header[0]);
            }
            $requiredColumns = ['nama', 'kategori', 'deskripsi', 'harga', 'stok', 'aktif'];
            if ($header !== $requiredColumns) {
                $errors[] = 'Kolom CSV harus berurutan: nama,kategori,deskripsi,harga,stok,aktif.';
            } else {
                $rows = [];
                $line = 1;
                while (($row = fgetcsv($handle, 0, ',', '"', '')) !== false) {
                    $line++;
                    if ($row === [null] || $row === []) {
                        continue;
                    }
                    if (count($row) !== count($requiredColumns)) {
                        $errors[] = "Baris {$line}: jumlah kolom tidak sesuai.";
                        continue;
                    }
                    [$name, $category, $description, $rawPrice, $rawStock, $rawActive] = array_map(
                        static fn (mixed $value): string => trim((string) $value),
                        $row
                    );
                    $price = filter_var($rawPrice, FILTER_VALIDATE_INT);
                    $stock = filter_var($rawStock, FILTER_VALIDATE_INT);
                    if ($name === '' || mb_strlen($name) > 120) {
                        $errors[] = "Baris {$line}: nama wajib diisi (maksimal 120 karakter).";
                    } elseif (!in_array($category, $allowedCategories, true)) {
                        $errors[] = "Baris {$line}: kategori harus Kopi, Minuman, atau Kudapan.";
                    } elseif (mb_strlen($description) > 1000) {
                        $errors[] = "Baris {$line}: deskripsi maksimal 1.000 karakter.";
                    } elseif ($price === false || $price < 1 || $price > 1000000000) {
                        $errors[] = "Baris {$line}: harga tidak valid.";
                    } elseif ($stock === false || $stock < 0 || $stock > 1000000) {
                        $errors[] = "Baris {$line}: stok tidak valid.";
                    } elseif (!in_array($rawActive, ['0', '1'], true)) {
                        $errors[] = "Baris {$line}: aktif harus bernilai 0 atau 1.";
                    } else {
                        $rows[] = [$name, $category, $description, $price, $stock, (int) $rawActive];
                    }
                    if ($line > 501) {
                        $errors[] = 'Maksimal 500 baris data dalam satu berkas.';
                        break;
                    }
                }

                if ($errors === [] && $rows === []) {
                    $errors[] = 'Berkas CSV tidak memiliki baris data.';
                }
                if ($errors === []) {
                    $pdo->beginTransaction();
                    $insert = $pdo->prepare(
                        'INSERT INTO produk_umkm (nama, kategori, deskripsi, harga, stok, aktif)
                         VALUES (:nama, :kategori, :deskripsi, :harga, :stok, :aktif)'
                    );
                    foreach ($rows as [$name, $category, $description, $price, $stock, $active]) {
                        $insert->execute([
                            'nama' => $name,
                            'kategori' => $category,
                            'deskripsi' => $description,
                            'harga' => $price,
                            'stok' => $stock,
                            'aktif' => $active,
                        ]);
                    }
                    $pdo->commit();
                    $message = count($rows) . ' produk berhasil diimpor.';
                }
            }
            fclose($handle);
        }
    }
}
?>
<section class="max-w-3xl mx-auto space-y-6">
    <div class="relative flex flex-col min-w-0 break-words bg-white shadow-argon-xl rounded-2xl border-0 overflow-hidden">
        <!-- Card Header -->
        <div class="p-6 pb-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-800">Impor Produk CSV</h3>
                <p class="text-xs text-slate-400">Unggah berkas spreadsheet untuk menambahkan banyak produk sekaligus.</p>
            </div>
            <a href="<?= e(app_url('admin.php?route=produk')) ?>" class="text-xs font-bold text-slate-500 hover:text-blue-600 flex items-center gap-1.5 transition-colors">
                <i class="fa-solid fa-arrow-left text-[11px]"></i>
                <span>Kembali</span>
            </a>
        </div>

        <div class="p-6 space-y-5">
            <?php if ($message !== ''): ?>
                <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-xs font-semibold text-emerald-800 flex items-center gap-2.5" role="status">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i>
                    <span><?= e($message) ?></span>
                </div>
            <?php endif; ?>

            <?php if ($errors !== []): ?>
                <div class="rounded-xl bg-rose-50 border border-rose-200 p-4 text-xs text-rose-800" role="alert">
                    <p class="font-bold flex items-center gap-2 text-rose-900 mb-2">
                        <i class="fa-solid fa-circle-exclamation text-rose-500 text-base"></i>
                        <span>Berkas CSV tidak dapat diimpor. Perbaiki hal berikut:</span>
                    </p>
                    <ul class="list-inside list-disc space-y-1 ml-2 text-rose-700">
                        <?php foreach (array_slice($errors, 0, 10) as $err): ?>
                            <li><?= e($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Instructions & Template Box -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70 text-xs text-slate-600 space-y-3">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-circle-info text-blue-500 text-base mt-0.5"></i>
                    <div class="space-y-1">
                        <p class="font-semibold text-slate-700">Petunjuk Format Berkas:</p>
                        <p>Berkas CSV harus berformat <strong>UTF-8</strong> (maksimal 2 MB & 500 baris data). Kolom header wajib berurutan persis:</p>
                        <p class="font-mono bg-white border border-slate-200 px-2 py-1 rounded inline-block text-[11px] text-slate-800">nama,kategori,deskripsi,harga,stok,aktif</p>
                        <p class="text-[11px] text-slate-500">Pilihan kategori: <code>Kopi</code>, <code>Minuman</code>, <code>Kudapan</code>. Kolom aktif bernilai <code>1</code> atau <code>0</code>.</p>
                    </div>
                </div>
                <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between">
                    <span class="text-slate-500 font-medium">Gunakan template resmi untuk menghindari galat:</span>
                    <a href="<?= e(app_url('assets/templates/produk_template.csv')) ?>" download class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-slate-200 font-bold text-blue-600 hover:bg-blue-50 shadow-sm transition-all text-xs">
                        <i class="fa-solid fa-download text-[11px]"></i>
                        <span>Unduh Template CSV</span>
                    </a>
                </div>
            </div>

            <!-- Upload Form -->
            <form method="post" enctype="multipart/form-data" class="space-y-5">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="MAX_FILE_SIZE" value="2097152">

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Pilih Berkas CSV <span class="text-rose-500">*</span></label>
                    <div class="border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center hover:border-blue-400 transition-colors bg-slate-50/50">
                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-slate-400 mb-2 block"></i>
                        <input required type="file" name="csv" accept=".csv,text/csv" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                        <span class="text-[11px] text-slate-400 mt-2 block">Format berkas didukung: .csv (maksimal 2 MB)</span>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 text-xs font-bold uppercase tracking-wider text-white bg-gradient-to-tl from-purple-600 to-indigo-600 rounded-xl shadow-md shadow-purple-500/25 hover:scale-[1.02] active:scale-95 transition-all">
                        <i class="fa-solid fa-file-circle-check text-xs"></i>
                        <span>Validasi & Impor Produk</span>
                    </button>
                    <a href="<?= e(app_url('admin.php?route=produk')) ?>" class="inline-flex items-center px-5 py-3 text-xs font-bold uppercase tracking-wider text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>
