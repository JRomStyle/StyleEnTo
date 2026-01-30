const byId = (id) => document.getElementById(id);

const formatMoney = (value) => {
    return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(value);
};

const setupPos = () => {
    if (!window.PosCatalog) return;
    const basePath = (window.BasePath || '').replace(/\/$/, '');
    const categoriesEl = byId('pos-categories');
    const productsEl = byId('pos-products');
    const cartEl = byId('pos-cart');
    const totalEl = byId('pos-total');
    const orderTypeEl = byId('pos-order-type');
    const paymentMethodEl = byId('pos-payment-method');
    const submitEl = byId('pos-submit');
    let activeCategory = window.PosCatalog[0]?.id;
    const cart = [];

    const renderCategories = () => {
        categoriesEl.innerHTML = '';
        window.PosCatalog.forEach((cat) => {
            const btn = document.createElement('button');
            const isActive = cat.id === activeCategory;
            btn.className = `px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all ${
                isActive 
                ? 'bg-[#ffbc0d] text-slate-900 shadow-md shadow-yellow-100 scale-105' 
                : 'bg-slate-50 text-slate-400 hover:bg-slate-100'
            }`;
            btn.textContent = cat.name;
            btn.onclick = () => {
                activeCategory = cat.id;
                renderCategories();
                renderProducts();
            };
            categoriesEl.appendChild(btn);
        });
    };

    const renderProducts = () => {
        productsEl.innerHTML = '';
        const category = window.PosCatalog.find((c) => c.id === activeCategory);
        (category?.products || []).forEach((product) => {
            const card = document.createElement('button');
            card.className = 'group bg-white border border-slate-50 rounded-[24px] p-5 text-left hover:border-[#ffbc0d] hover:shadow-xl hover:shadow-yellow-50 transition-all duration-300 flex flex-col gap-4';
            card.innerHTML = `
                <div class="w-full aspect-square bg-slate-50 rounded-2xl flex items-center justify-center text-4xl group-hover:scale-110 transition-transform duration-500">
                    ${product.image || '🍔'}
                </div>
                <div>
                    <div class="font-black text-slate-900 uppercase text-xs tracking-tight mb-1 line-clamp-1">${product.name}</div>
                    <div class="text-lg font-black text-[#ffbc0d]">${formatMoney(product.price)}</div>
                </div>
                <div class="mt-auto pt-2">
                    <div class="w-8 h-8 bg-slate-900 text-white rounded-full flex items-center justify-center text-xl font-light group-hover:bg-[#ffbc0d] group-hover:text-slate-900 transition-colors">+</div>
                </div>
            `;
            card.onclick = () => {
                const existing = cart.find((i) => i.product_id === product.id);
                if (existing) {
                    existing.quantity += 1;
                } else {
                    cart.push({
                        product_id: product.id,
                        name: product.name,
                        quantity: 1,
                        price: parseFloat(product.price),
                        extras: '',
                        notes: ''
                    });
                }
                renderCart();
            };
            productsEl.appendChild(card);
        });
    };

    const renderCart = () => {
        cartEl.innerHTML = '';
        const subtotalEl = byId('pos-subtotal');
        const emptyState = byId('cart-empty');
        let total = 0;

        if (cart.length === 0) {
            if (emptyState) emptyState.classList.remove('hidden');
        } else {
            if (emptyState) emptyState.classList.add('hidden');
        }

        cart.forEach((item, index) => {
            total += item.price * item.quantity;
            const row = document.createElement('div');
            row.className = 'flex items-center gap-4 bg-white p-4 rounded-2xl border border-slate-50 shadow-sm group hover:border-red-100 transition-colors';
            row.innerHTML = `
                <div class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center text-xl">🍔</div>
                <div class="flex-1">
                    <div class="font-bold text-slate-900 text-sm">${item.name}</div>
                    <div class="flex items-center gap-2 mt-1">
                        <button class="w-5 h-5 flex items-center justify-center rounded-md bg-slate-100 text-slate-400 hover:bg-red-50 hover:text-red-500 transition-colors dec-qty" data-index="${index}">-</button>
                        <span class="text-xs font-bold text-slate-600 w-4 text-center">${item.quantity}</span>
                        <button class="w-5 h-5 flex items-center justify-center rounded-md bg-slate-100 text-slate-400 hover:bg-green-50 hover:text-green-500 transition-colors inc-qty" data-index="${index}">+</button>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm font-black text-slate-900">${formatMoney(item.price * item.quantity)}</div>
                </div>
            `;
            cartEl.appendChild(row);
        });

        // Add event listeners for cart quantity buttons
        cartEl.querySelectorAll('.inc-qty').forEach(btn => {
            btn.onclick = (e) => {
                e.stopPropagation();
                cart[btn.dataset.index].quantity++;
                renderCart();
            };
        });
        cartEl.querySelectorAll('.dec-qty').forEach(btn => {
            btn.onclick = (e) => {
                e.stopPropagation();
                const idx = btn.dataset.index;
                if (cart[idx].quantity > 1) {
                    cart[idx].quantity--;
                } else {
                    cart.splice(idx, 1);
                }
                renderCart();
            };
        });

        if (subtotalEl) subtotalEl.textContent = formatMoney(total);
        totalEl.textContent = formatMoney(total);
    };

    submitEl.addEventListener('click', async () => {
        if (!cart.length) return;
        const payload = {
            items: cart,
            order_type: orderTypeEl.value,
            payment_method: paymentMethodEl.value
        };
        const formData = new FormData();
        formData.append('_csrf', window.PosCsrf || '');
        formData.append('payload', JSON.stringify(payload));
        const response = await fetch(`${basePath}/pos/order`, { method: 'POST', body: formData });
        const data = await response.json();
        if (data?.order) {
            cart.length = 0;
            renderCart();
        }
    });

    renderCategories();
    renderProducts();
    renderCart();
};

const setupKitchen = () => {
    const basePath = (window.BasePath || '').replace(/\/$/, '');
    const refresh = byId('kitchen-refresh');
    const buttons = document.querySelectorAll('.kitchen-status');
    if (refresh) {
        refresh.addEventListener('click', () => window.location.reload());
    }
    if (!buttons.length) return;
    buttons.forEach((btn) => {
        btn.addEventListener('click', async () => {
            const formData = new FormData();
            formData.append('_csrf', window.KitchenCsrf || '');
            formData.append('order_id', btn.dataset.id);
            formData.append('status', btn.dataset.status);
            await fetch(`${basePath}/kitchen/status`, { method: 'POST', body: formData });
            window.location.reload();
        });
    });
};

const setupInventory = () => {
    const basePath = (window.BasePath || '').replace(/\/$/, '');
    const form = document.getElementById('inventory-adjust-form');
    if (!form) return;
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        const response = await fetch(`${basePath}/inventory/adjust`, { method: 'POST', body: formData });
        if (response.ok) {
            window.location.reload();
        }
    });
};

document.addEventListener('DOMContentLoaded', () => {
    setupPos();
    setupKitchen();
    setupInventory();
});
