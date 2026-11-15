(() => {
    const search = document.querySelector('#product-search');
    const tableBody = document.querySelector('#product-table-body');
    const feedback = document.querySelector('#product-feedback');
    if (!tableBody || !feedback) return;

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const appBase = document.querySelector('meta[name="app-base"]')?.content || '';
    const rupiah = (number) => `Rp ${new Intl.NumberFormat('id-ID').format(Number(number))}`;
    let searchTimer;

    const cell = (text, className = 'px-6 py-4') => {
        const element = document.createElement('td');
        element.className = className;
        element.textContent = text;
        return element;
    };

    const renderRows = (items) => {
        tableBody.replaceChildren();
        if (items.length === 0) {
            const row = document.createElement('tr');
            const empty = cell('Produk tidak ditemukan.', 'px-6 py-10 text-center text-slate-400 text-sm');
            empty.colSpan = 6;
            row.append(empty);
            tableBody.append(row);
            return;
        }

        items.forEach((product) => {
            const row = document.createElement('tr');
            row.dataset.productId = product.id;
            row.className = 'hover:bg-slate-50/60 transition-colors';

            // Product Cell
            const productCell = document.createElement('td');
            productCell.className = 'px-6 py-4';
            const container = document.createElement('div');
            container.className = 'flex items-center gap-3.5';

            const img = document.createElement('img');
            img.src = product.image_url || `${appBase}/assets/img/produk/kopi-default.jpg`;
            img.alt = product.nama;
            img.className = 'h-12 w-12 rounded-xl object-cover shadow-sm border border-slate-100 shrink-0';
            img.onerror = () => { img.src = `${appBase}/assets/img/produk/kopi-default.jpg`; };
            container.append(img);

            const textWrap = document.createElement('div');
            const name = document.createElement('span');
            name.className = 'font-bold text-sm text-slate-800 leading-snug block';
            name.textContent = product.nama;
            const description = document.createElement('span');
            description.className = 'mt-0.5 block max-w-xs truncate text-xs text-slate-400';
            description.textContent = product.deskripsi;
            textWrap.append(name, description);
            container.append(textWrap);
            productCell.append(container);

            // Category Cell
            const categoryCell = document.createElement('td');
            categoryCell.className = 'px-6 py-4';
            const catBadge = document.createElement('span');
            let catColor = 'bg-orange-50 text-orange-700 border border-orange-200';
            if (product.kategori === 'Kopi') catColor = 'bg-amber-50 text-amber-700 border border-amber-200';
            if (product.kategori === 'Minuman') catColor = 'bg-cyan-50 text-cyan-700 border border-cyan-200';
            catBadge.className = `inline-block px-2.5 py-1 text-xxs font-bold rounded-lg uppercase tracking-wider ${catColor}`;
            catBadge.textContent = product.kategori;
            categoryCell.append(catBadge);

            // Price Cell
            const priceCell = document.createElement('td');
            priceCell.className = 'whitespace-nowrap px-6 py-4';
            const priceSpan = document.createElement('span');
            priceSpan.className = 'text-sm font-bold text-slate-700';
            priceSpan.textContent = rupiah(product.harga);
            priceCell.append(priceSpan);

            // Stock Cell
            const stockCell = document.createElement('td');
            stockCell.className = 'px-6 py-4 text-center';
            const stockSpan = document.createElement('span');
            const isLowStock = Number(product.stok) <= 5;
            stockSpan.className = `inline-block px-3 py-1 text-xs font-bold rounded-full ${isLowStock ? 'bg-rose-50 text-rose-600 border border-rose-200' : 'bg-slate-100 text-slate-700'}`;
            stockSpan.textContent = new Intl.NumberFormat('id-ID').format(Number(product.stok));
            stockCell.append(stockSpan);

            // Status Cell
            const statusCell = document.createElement('td');
            statusCell.className = 'px-6 py-4 text-center';
            const status = document.createElement('span');
            const isActive = Number(product.aktif) === 1;
            status.className = `inline-flex items-center gap-1 px-2.5 py-1 text-xxs font-bold rounded-md ${isActive ? 'bg-gradient-to-tl from-emerald-500 to-teal-400 text-white shadow-sm' : 'bg-slate-100 text-slate-600'}`;
            status.innerHTML = `<i class="fa-solid fa-${isActive ? 'check' : 'minus'} text-[9px]"></i> ${isActive ? 'Aktif' : 'Nonaktif'}`;
            statusCell.append(status);

            // Action Cell
            const actionCell = document.createElement('td');
            actionCell.className = 'px-6 py-4 text-right';
            const actionWrap = document.createElement('div');
            actionWrap.className = 'inline-flex items-center gap-1.5 justify-end';

            const editLink = document.createElement('a');
            editLink.href = `${appBase}/admin.php?route=produk/tambah&id=${product.id}`;
            editLink.className = 'inline-flex items-center gap-1.5 rounded-xl border border-blue-200 bg-white px-2.5 py-1.5 text-xs font-bold text-blue-600 shadow-sm hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all';
            editLink.title = 'Edit produk';
            editLink.innerHTML = '<i class="fa-solid fa-pen-to-square text-xs"></i><span>Edit</span>';
            actionWrap.append(editLink);

            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'delete-product inline-flex items-center gap-1.5 rounded-xl border border-rose-200 bg-white px-2.5 py-1.5 text-xs font-bold text-rose-600 shadow-sm hover:bg-rose-600 hover:text-white hover:border-rose-600 transition-all';
            button.dataset.id = product.id;
            button.title = 'Hapus produk';
            button.innerHTML = '<i class="fa-solid fa-trash-can text-xs"></i><span>Hapus</span>';
            actionWrap.append(button);
            actionCell.append(actionWrap);

            row.append(
                productCell,
                categoryCell,
                priceCell,
                stockCell,
                statusCell,
                actionCell
            );
            tableBody.append(row);
        });
    };

    search?.addEventListener('input', () => {
        window.clearTimeout(searchTimer);
        searchTimer = window.setTimeout(async () => {
            feedback.textContent = 'Mencari produk…';
            try {
                const url = new URL(`${appBase}/api/cari_produk.php`, window.location.origin);
                url.searchParams.set('q', search.value);
                const response = await fetch(url, { headers: { Accept: 'application/json' } });
                const data = await response.json();
                if (!response.ok) throw new Error(data.error || 'Pencarian gagal.');
                renderRows(data.items);
                feedback.textContent = `${data.items.length} produk ditemukan.`;
            } catch (error) {
                feedback.textContent = error instanceof Error ? error.message : 'Pencarian gagal.';
            }
        }, 250);
    });

    tableBody.addEventListener('click', async (event) => {
        const button = event.target.closest('.delete-product');
        if (!button) return;
        if (!window.confirm('Hapus produk ini? Tindakan ini tidak dapat dibatalkan.')) return;
        button.disabled = true;
        try {
            const response = await fetch(`${appBase}/api/hapus_produk.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken, Accept: 'application/json' },
                body: JSON.stringify({ id: Number(button.dataset.id) }),
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.error || 'Produk gagal dihapus.');
            button.closest('tr').remove();
            feedback.textContent = 'Produk berhasil dihapus.';
            if (tableBody.children.length === 0) {
                const row = document.createElement('tr');
                const empty = cell('Belum ada produk.', 'px-5 py-8 text-center text-stone-500');
                empty.colSpan = 6;
                row.append(empty);
                tableBody.append(row);
            }
        } catch (error) {
            button.disabled = false;
            feedback.textContent = error instanceof Error ? error.message : 'Produk gagal dihapus.';
        }
    });

    const urlParams = new URLSearchParams(window.location.search);
    const initialQuery = urlParams.get('q');
    if (initialQuery && search) {
        search.value = initialQuery;
        search.dispatchEvent(new Event('input', { bubbles: true }));
    }
})();
