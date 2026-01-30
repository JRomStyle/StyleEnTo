document.addEventListener('DOMContentLoaded', () => {
  const cartCountEl = document.querySelector('#cartCount');
  const updateCartCount = (n) => {
    if (cartCountEl) cartCountEl.textContent = n;
  };
  document.querySelectorAll('form.add-to-cart').forEach(form => {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const fd = new FormData(form);
      const res = await fetch(form.action, { method: 'POST', body: fd });
      const data = await res.json().catch(() => ({ ok: false }));
      if (data && data.ok) updateCartCount(data.count || 0);
    });
  });
  document.querySelectorAll('form.remove-item').forEach(form => {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const fd = new FormData(form);
      const res = await fetch(form.action, { method: 'POST', body: fd });
      const data = await res.json().catch(() => ({ ok: false }));
      if (data && data.ok) {
        updateCartCount(data.count || 0);
        location.reload();
      }
    });
  });
  document.querySelectorAll('input.qty-input').forEach(inp => {
    inp.addEventListener('change', async () => {
      const id = inp.getAttribute('data-id');
      const qty = parseInt(inp.value || '0', 10);
      const form = new FormData();
      form.append('id', id);
      form.append('qty', qty);
      const csrf = document.querySelector('input[name="csrf"]');
      if (csrf) form.append('csrf', csrf.value);
      const res = await fetch('?route=cart/update', { method: 'POST', body: form });
      const data = await res.json().catch(() => ({ ok: false }));
      if (data && data.ok) location.reload();
    });
  });
  const searchInput = document.querySelector('#searchInput');
  const catalogGrid = document.querySelector('#catalogGrid');
  const sortSelect = document.querySelector('#sortSelect');
  const categoryFilter = document.querySelector('#categoryFilter');
  const ageFilter = document.querySelector('#ageFilter');
  const priceFilter = document.querySelector('#priceFilter');
  const genderFilter = document.querySelector('#genderFilter');
  const isCatalog = !!(catalogGrid && sortSelect);
  const applyCatalogParams = (patch) => {
    const url = new URL(window.location.href);
    url.searchParams.set('route', 'product/index');
    url.searchParams.set('page', '1');
    Object.entries(patch).forEach(([k, v]) => {
      if (v === '' || v === null || v === undefined) {
        url.searchParams.delete(k);
      } else {
        url.searchParams.set(k, String(v));
      }
    });
    window.location.href = url.toString();
  };
  let t = null;
  if (isCatalog && searchInput) {
    searchInput.addEventListener('input', () => {
      clearTimeout(t);
      t = setTimeout(() => {
        applyCatalogParams({ q: searchInput.value.trim() });
      }, 400);
    });
  }
  if (isCatalog) {
    const handleChange = () => {
      applyCatalogParams({
        cat: categoryFilter ? categoryFilter.value : '',
        gender: genderFilter ? genderFilter.value : '',
        age: ageFilter ? ageFilter.value : '',
        price: priceFilter ? priceFilter.value : '',
        sort: sortSelect ? sortSelect.value : 'recent',
      });
    };
    [categoryFilter, genderFilter, ageFilter, priceFilter, sortSelect].forEach(el => {
      if (el) el.addEventListener('change', handleChange);
    });
  }
}); 
