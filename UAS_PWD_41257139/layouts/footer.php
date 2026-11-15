        <footer class="mt-auto px-6 py-4">
            <div class="flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 gap-2">
                <p>© <?= date('Y') ?> <strong class="text-slate-600 font-semibold">Kopi Cirebon</strong> — UMKM Studi Kasus Pemrograman Web Dasar.</p>
                <p class="flex items-center gap-1.5">
                    <span>Nuxt Argon Dashboard 2 UI</span>
                    <span>•</span>
                    <a href="<?= e(app_url()) ?>" class="text-blue-600 font-semibold hover:underline">Lihat Toko</a>
                </p>
            </div>
        </footer>
    </div>
    
    <!-- Mobile Sidenav Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('sidenav-main');
            const backdrop = document.getElementById('sidebar-backdrop');
            const toggleBtn = document.getElementById('sidebar-toggle-btn');
            const closeBtn = document.getElementById('sidebar-close-btn');

            const openSidebar = () => {
                sidebar?.classList.remove('-translate-x-[110%]');
                sidebar?.classList.add('translate-x-0');
                backdrop?.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            };

            const closeSidebar = () => {
                sidebar?.classList.add('-translate-x-[110%]');
                sidebar?.classList.remove('translate-x-0');
                backdrop?.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            };

            toggleBtn?.addEventListener('click', openSidebar);
            closeBtn?.addEventListener('click', closeSidebar);
            backdrop?.addEventListener('click', closeSidebar);

            // Connect topbar search to product search if available
            const topSearch = document.getElementById('topbar-search');
            const prodSearch = document.getElementById('product-search');
            if (topSearch && prodSearch) {
                topSearch.addEventListener('input', (e) => {
                    prodSearch.value = e.target.value;
                    prodSearch.dispatchEvent(new Event('input', { bubbles: true }));
                });
            } else if (topSearch) {
                topSearch.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter' && topSearch.value.trim() !== '') {
                        window.location.href = `<?= e(app_url('admin.php?route=produk')) ?>&q=` + encodeURIComponent(topSearch.value.trim());
                    }
                });
            }
        });
    </script>
    <script src="<?= e(app_url('assets/js/produk_async.js')) ?>" defer></script>
</body>
</html>
<?php
if (ob_get_level() > 0) {
    ob_end_flush();
}
?>
