(() => {
    const slides = [...document.querySelectorAll('[data-slide]')];
    const dots = [...document.querySelectorAll('.slider-dot')];
    let activeSlide = 0;

    const showSlide = (next) => {
        activeSlide = (next + slides.length) % slides.length;
        slides.forEach((slide, index) => {
            slide.classList.toggle('is-active', index === activeSlide);
            slide.setAttribute('aria-hidden', index === activeSlide ? 'false' : 'true');
        });
        dots.forEach((dot, index) => dot.classList.toggle('is-active', index === activeSlide));
    };

    document.querySelector('.slider-prev')?.addEventListener('click', () => showSlide(activeSlide - 1));
    document.querySelector('.slider-next')?.addEventListener('click', () => showSlide(activeSlide + 1));
    dots.forEach((dot, index) => dot.addEventListener('click', () => showSlide(index)));
    if (slides.length > 1) window.setInterval(() => showSlide(activeSlide + 1), 6500);

    document.querySelectorAll('.gallery-filter').forEach((button) => {
        button.addEventListener('click', () => {
            const category = button.dataset.category;
            document.querySelectorAll('.gallery-filter').forEach((filter) => filter.classList.remove('is-selected'));
            button.classList.add('is-selected');
            document.querySelectorAll('.product-card').forEach((card) => {
                card.hidden = category !== 'all' && card.dataset.category !== category;
            });
        });
    });

    const order = new Map();
    const list = document.querySelector('#order-items');
    const total = document.querySelector('#order-total');
    const rupiah = (number) => `Rp ${new Intl.NumberFormat('id-ID').format(number)}`;

    const renderOrder = () => {
        list.replaceChildren();
        let sum = 0;
        order.forEach(({ price, quantity }, name) => {
            sum += price * quantity;
            const item = document.createElement('li');
            item.className = 'flex justify-between gap-3';
            const label = document.createElement('span');
            label.textContent = `${name} × ${quantity}`;
            const subtotal = document.createElement('span');
            subtotal.textContent = rupiah(price * quantity);
            item.append(label, subtotal);
            list.append(item);
        });
        if (order.size === 0) {
            const empty = document.createElement('li');
            empty.textContent = 'Belum ada produk dipilih.';
            list.append(empty);
        }
        total.textContent = rupiah(sum);
    };

    document.querySelectorAll('.add-to-order').forEach((button) => {
        button.addEventListener('click', () => {
            const { name, price } = button.dataset;
            const current = order.get(name) || { price: Number(price), quantity: 0 };
            current.quantity += 1;
            order.set(name, current);
            renderOrder();
        });
    });
    document.querySelector('#clear-order')?.addEventListener('click', () => {
        order.clear();
        renderOrder();
    });
})();
