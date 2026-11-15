<?php
$products = $pdo->query(
    'SELECT id, nama, kategori, deskripsi, harga, stok, gambar, aktif
     FROM produk_umkm ORDER BY created_at DESC'
)->fetchAll();
?>
<table class="w-full min-w-[760px] text-left border-collapse">
    <thead class="bg-slate-50/70 border-b border-slate-100">
        <tr>
            <th class="px-6 py-3.5 text-xxs font-bold text-slate-400 uppercase tracking-wider">Produk</th>
            <th class="px-6 py-3.5 text-xxs font-bold text-slate-400 uppercase tracking-wider">Kategori</th>
            <th class="px-6 py-3.5 text-xxs font-bold text-slate-400 uppercase tracking-wider">Harga Satuan</th>
            <th class="px-6 py-3.5 text-xxs font-bold text-slate-400 uppercase tracking-wider text-center">Stok</th>
            <th class="px-6 py-3.5 text-xxs font-bold text-slate-400 uppercase tracking-wider text-center">Status</th>
            <th class="px-6 py-3.5 text-xxs font-bold text-slate-400 uppercase tracking-wider text-right">Aksi</th>
        </tr>
    </thead>
    <tbody id="product-table-body" class="divide-y divide-slate-100">
        <?php foreach ($products as $product): ?>
            <tr data-product-id="<?= (int) $product['id'] ?>" class="hover:bg-slate-50/60 transition-colors">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3.5">
                        <img src="<?= e(product_image_url($product['nama'], $product['kategori'], $product['gambar'] ?? null)) ?>" alt="<?= e($product['nama']) ?>" class="h-12 w-12 rounded-xl object-cover shadow-sm border border-slate-100 shrink-0" onerror="this.onerror=null; this.src='<?= e(app_url('assets/img/produk/kopi-default.jpg')) ?>';">
                        <div>
                            <span class="font-bold text-sm text-slate-800 leading-snug block"><?= e($product['nama']) ?></span>
                            <span class="mt-0.5 block max-w-xs truncate text-xs text-slate-400"><?= e($product['deskripsi']) ?></span>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="inline-block px-2.5 py-1 text-xxs font-bold rounded-lg uppercase tracking-wider <?= $product['kategori'] === 'Kopi' ? 'bg-amber-50 text-amber-700 border border-amber-200' : ($product['kategori'] === 'Minuman' ? 'bg-cyan-50 text-cyan-700 border border-cyan-200' : 'bg-orange-50 text-orange-700 border border-orange-200') ?>">
                        <?= e($product['kategori']) ?>
                    </span>
                </td>
                <td class="whitespace-nowrap px-6 py-4">
                    <span class="text-sm font-bold text-slate-700">Rp <?= number_format((int) $product['harga'], 0, ',', '.') ?></span>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="inline-block px-3 py-1 text-xs font-bold rounded-full <?= (int) $product['stok'] <= 5 ? 'bg-rose-50 text-rose-600 border border-rose-200' : 'bg-slate-100 text-slate-700' ?>">
                        <?= number_format((int) $product['stok'], 0, ',', '.') ?>
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xxs font-bold rounded-md <?= (int) $product['aktif'] === 1 ? 'bg-gradient-to-tl from-emerald-500 to-teal-400 text-white shadow-sm' : 'bg-slate-100 text-slate-600' ?>">
                        <i class="fa-solid fa-<?= (int) $product['aktif'] === 1 ? 'check' : 'minus' ?> text-[9px]"></i>
                        <?= (int) $product['aktif'] === 1 ? 'Aktif' : 'Nonaktif' ?>
                    </span>
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="inline-flex items-center gap-1.5 justify-end">
                        <a href="<?= e(app_url('admin.php?route=produk/tambah&id=' . (int) $product['id'])) ?>" class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 bg-white px-2.5 py-1.5 text-xs font-bold text-blue-600 shadow-sm hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all" title="Edit produk">
                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                            <span>Edit</span>
                        </a>
                        <button type="button" class="delete-product inline-flex items-center gap-1.5 rounded-xl border border-rose-200 bg-white px-2.5 py-1.5 text-xs font-bold text-rose-600 shadow-sm hover:bg-rose-600 hover:text-white hover:border-rose-600 transition-all" data-id="<?= (int) $product['id'] ?>" title="Hapus produk">
                            <i class="fa-solid fa-trash-can text-xs"></i>
                            <span>Hapus</span>
                        </button>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if ($products === []): ?>
            <tr>
                <td class="px-6 py-10 text-center text-slate-400 text-sm" colspan="6">Belum ada produk terdaftar.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<div class="px-6 py-3 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
    <p id="product-feedback" aria-live="polite">Menampilkan seluruh produk aktif kedai.</p>
    <span>Tip: Gunakan tombol 'Ekspor CSV' untuk backup data.</span>
</div>
