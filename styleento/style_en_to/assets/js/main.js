(() => {
  const CART_KEY = "style_en_to_cart_v1";
  const BASE = (document.querySelector('meta[name="app-base"]')?.content || "").replace(/\/$/, "");
  const withBase = (p) => `${BASE}/${String(p || "").replace(/^\//, "")}`;

  const money = (n) => {
    const num = Number(n || 0);
    return new Intl.NumberFormat("es-ES", { style: "currency", currency: "EUR" }).format(num);
  };

  const getCart = () => {
    try {
      const raw = localStorage.getItem(CART_KEY);
      const parsed = raw ? JSON.parse(raw) : [];
      return Array.isArray(parsed) ? parsed : [];
    } catch {
      return [];
    }
  };

  const setCart = (items) => {
    localStorage.setItem(CART_KEY, JSON.stringify(items));
    updateCartBadge(items);
  };

  const normalizeSize = (size) => {
    const v = (size ?? "").toString().trim();
    return v === "" ? "U" : v;
  };

  const addToCart = (item) => {
    const next = getCart();
    const id = String(item.id);
    const size = normalizeSize(item.size);
    const existing = next.find((x) => String(x.id) === id && String(x.size) === size);
    if (existing) existing.qty = Math.min(99, Number(existing.qty || 1) + Number(item.qty || 1));
    else next.push({ id, name: item.name, price: Number(item.price), image: item.image || "", size, qty: Math.max(1, Number(item.qty || 1)) });
    setCart(next);
    toast("Agregado al carrito");
  };

  const removeFromCart = (id, size) => {
    const next = getCart().filter((x) => !(String(x.id) === String(id) && String(x.size) === String(size)));
    setCart(next);
  };

  const updateQty = (id, size, qty) => {
    const next = getCart();
    const target = next.find((x) => String(x.id) === String(id) && String(x.size) === String(size));
    if (!target) return;
    target.qty = Math.min(99, Math.max(1, Number(qty || 1)));
    setCart(next);
  };

  const totals = (items) => {
    const total = items.reduce((acc, it) => acc + Number(it.price || 0) * Number(it.qty || 0), 0);
    return { total };
  };

  const updateCartBadge = (items = getCart()) => {
    const count = items.reduce((acc, it) => acc + Number(it.qty || 0), 0);
    const el = document.getElementById("cart-count");
    if (el) el.textContent = String(count);
  };

  const toast = (message) => {
    let root = document.getElementById("toast-root");
    if (!root) {
      root = document.createElement("div");
      root.id = "toast-root";
      root.className = "fixed bottom-6 left-1/2 -translate-x-1/2 z-50 space-y-2";
      document.body.appendChild(root);
    }
    const node = document.createElement("div");
    node.className = "glass border border-black/10 text-black px-4 py-3 rounded-full shadow-lg text-sm";
    node.textContent = message;
    root.appendChild(node);
    setTimeout(() => node.classList.add("opacity-0"), 1600);
    setTimeout(() => node.remove(), 2100);
  };

  const escapeHtml = (s) => String(s).replace(/[&<>"']/g, (c) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;" }[c]));

  const hashString = (s) => {
    let h = 0;
    const str = String(s ?? "");
    for (let i = 0; i < str.length; i++) h = (h * 31 + str.charCodeAt(i)) >>> 0;
    return h;
  };

  const fallbackImgSrc = () => {
    return withBase("style_en_to/assets/img/placeholder-product.jpg");
  };

  const bindAddButtons = () => {
    document.querySelectorAll("[data-add-to-cart]").forEach((btn) => {
      btn.addEventListener("click", (ev) => {
        ev.preventDefault();
        ev.stopPropagation();
        const { id, name, price, image, size } = btn.dataset;
        addToCart({ id, name, price, image, size, qty: 1 });
      });
    });

    const detailBtn = document.querySelector("[data-add-to-cart-detail]");
    if (detailBtn) {
      detailBtn.addEventListener("click", (ev) => {
        ev.preventDefault();
        ev.stopPropagation();
        const { id, name, price, image } = detailBtn.dataset;
        const sizeSel = document.getElementById("size");
        const size = sizeSel ? sizeSel.value : "U";
        addToCart({ id, name, price, image, size, qty: 1 });
      });
    }
  };

  const renderCartPage = () => {
    const list = document.getElementById("cart-items");
    if (!list) return;

    const checkoutBtn = document.getElementById("checkout-btn");
    if (checkoutBtn && !checkoutBtn.dataset.href) {
      checkoutBtn.dataset.href = checkoutBtn.getAttribute("href") || withBase("checkout.php");
    }
    if (checkoutBtn && !checkoutBtn.dataset.bound) {
      checkoutBtn.dataset.bound = "1";
      checkoutBtn.addEventListener("click", (ev) => {
        const disabled = checkoutBtn.getAttribute("aria-disabled") === "true";
        if (disabled) ev.preventDefault();
      });
    }

    const updateSummary = (items) => {
      const totalEl = document.getElementById("cart-total");
      const { total } = totals(items);
      if (totalEl) totalEl.textContent = money(total);
      if (checkoutBtn) {
        const empty = items.length === 0;
        checkoutBtn.setAttribute("aria-disabled", empty ? "true" : "false");
        checkoutBtn.classList.toggle("pointer-events-none", empty);
        checkoutBtn.classList.toggle("opacity-50", empty);
        checkoutBtn.classList.toggle("cursor-not-allowed", empty);
        checkoutBtn.setAttribute("href", empty ? "#" : checkoutBtn.dataset.href);
      }
    };

    const render = () => {
      const items = getCart();
      list.innerHTML = "";
      if (items.length === 0) {
        list.innerHTML = `<div class="border border-black/10 rounded-2xl p-8 bg-white">
          <div class="text-xl font-semibold">Tu carrito está vacío</div>
          <div class="text-black/70 mt-2">Explora la colección y agrega tus favoritos.</div>
          <a href="${withBase("tienda.php")}" class="inline-flex mt-6 px-6 py-3 rounded-full btn-primary">Ir a la tienda</a>
        </div>`;
        updateSummary(items);
        return;
      }

      items.forEach((it) => {
        const row = document.createElement("div");
        row.className = "flex gap-4 items-center border border-black/10 rounded-2xl p-4 bg-white";
        row.innerHTML = `
          <img src="${it.image}" alt="" class="w-24 h-24 object-cover rounded-xl border border-black/10" />
          <div class="flex-1">
            <div class="flex items-center justify-between gap-4">
              <div class="min-w-0">
                <div class="font-semibold truncate">${escapeHtml(it.name || "")}</div>
                <div class="text-sm text-black/60">Talla: ${escapeHtml(String(it.size || "U"))}</div>
              </div>
              <div class="font-semibold">${money(Number(it.price || 0) * Number(it.qty || 0))}</div>
            </div>
            <div class="mt-3 flex items-center justify-between gap-4">
              <div class="inline-flex items-center gap-2 border border-black/10 rounded-full px-3 py-1">
                <button class="px-2 py-1 text-lg leading-none" data-qty-dec>−</button>
                <input class="w-10 text-center outline-none bg-transparent" value="${Number(it.qty || 1)}" inputmode="numeric" data-qty-input />
                <button class="px-2 py-1 text-lg leading-none" data-qty-inc>+</button>
              </div>
              <button class="text-sm text-black/60 link-underline" data-remove>Eliminar</button>
            </div>
          </div>
        `;

        row.querySelector("[data-remove]").addEventListener("click", () => {
          removeFromCart(it.id, it.size);
          render();
        });
        row.querySelector("[data-qty-dec]").addEventListener("click", () => {
          updateQty(it.id, it.size, Number(it.qty || 1) - 1);
          render();
        });
        row.querySelector("[data-qty-inc]").addEventListener("click", () => {
          updateQty(it.id, it.size, Number(it.qty || 1) + 1);
          render();
        });
        row.querySelector("[data-qty-input]").addEventListener("change", (ev) => {
          updateQty(it.id, it.size, ev.target.value);
          render();
        });

        list.appendChild(row);
      });

      updateSummary(items);
    };

    render();
  };

  const renderCheckout = () => {
    const form = document.getElementById("checkout-form");
    if (!form) return;

    const items = getCart();
    const summary = document.getElementById("checkout-summary");
    const hidden = document.getElementById("cart_json");
    const total = document.getElementById("checkout-total");
    const { total: totalValue } = totals(items);

    if (hidden) hidden.value = JSON.stringify(items);
    if (total) total.textContent = money(totalValue);

    if (summary) {
      summary.innerHTML = items
        .map(
          (it) =>
            `<div class="flex items-center justify-between gap-4 py-3 border-b border-black/10">
              <div class="min-w-0">
                <div class="font-medium truncate">${escapeHtml(it.name || "")}</div>
                <div class="text-sm text-black/60">Talla ${escapeHtml(String(it.size || "U"))} · x${Number(it.qty || 1)}</div>
              </div>
              <div class="font-semibold">${money(Number(it.price || 0) * Number(it.qty || 0))}</div>
            </div>`
        )
        .join("");
    }

    if (items.length === 0) {
      form.querySelectorAll("input,button,select,textarea").forEach((el) => (el.disabled = true));
    }
  };

  const bindReveal = () => {
    const els = Array.from(document.querySelectorAll("[data-reveal]"));
    if (els.length === 0) return;
    const io = new IntersectionObserver(
      (entries) => {
        entries.forEach((e) => {
          if (!e.isIntersecting) return;
          e.target.classList.add("reveal-in");
          io.unobserve(e.target);
        });
      },
      { threshold: 0.15 }
    );
    els.forEach((el) => {
      el.classList.add("reveal");
      io.observe(el);
    });
  };

  const bindFilters = () => {
    const toggleBtn = document.querySelector("[data-toggle-filters]");
    if (!toggleBtn) return;

    const overlay = document.getElementById("filters-overlay");
    const desktopAside = document.querySelector("[data-desktop-filters]");
    const mqDesktop = window.matchMedia("(min-width: 1024px)");

    const labelEl = toggleBtn.querySelector("span") || toggleBtn;
    const setLabel = (v) => (labelEl.textContent = String(v));

    const openOverlay = () => {
      if (!overlay) return;
      overlay.classList.remove("hidden");
      document.body.classList.add("overflow-hidden");
    };
    const closeOverlay = () => {
      if (!overlay) return;
      overlay.classList.add("hidden");
      document.body.classList.remove("overflow-hidden");
    };

    const sync = () => {
      if (mqDesktop.matches) {
        const hidden = desktopAside?.classList.contains("lg:hidden") ?? false;
        setLabel(hidden ? "Mostrar filtros" : "Ocultar filtros");
        closeOverlay();
      } else {
        setLabel("Filtros");
        if (desktopAside) desktopAside.classList.remove("lg:hidden");
      }
    };

    toggleBtn.addEventListener("click", () => {
      if (mqDesktop.matches) {
        if (desktopAside) desktopAside.classList.toggle("lg:hidden");
        sync();
        return;
      }
      if (overlay && !overlay.classList.contains("hidden")) closeOverlay();
      else openOverlay();
    });

    document.querySelectorAll("[data-close-filters]").forEach((btn) => btn.addEventListener("click", closeOverlay));
    window.addEventListener("keydown", (ev) => {
      if (ev.key === "Escape") closeOverlay();
    });
    mqDesktop.addEventListener?.("change", sync);
    sync();
  };

  window.StyleEnToCart = { getCart, setCart, addToCart, removeFromCart, updateQty, totals, money };

  document.addEventListener("DOMContentLoaded", () => {
    document.addEventListener(
      "error",
      (ev) => {
        const target = ev.target;
        if (!target || target.tagName !== "IMG") return;
        if (target.dataset.fallbackDone === "1") return;
        target.dataset.fallbackDone = "1";
        const current = target.getAttribute("src") || "";
        target.setAttribute("src", fallbackImgSrc(current));
      },
      true
    );
    updateCartBadge();
    bindAddButtons();
    bindReveal();
    bindFilters();
    renderCartPage();
    renderCheckout();
  });
})();

