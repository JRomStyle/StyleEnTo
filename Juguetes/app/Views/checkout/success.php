<div class="max-w-lg mx-auto bg-white border border-slate-200/70 rounded-2xl shadow-soft p-6 text-center">
    <div class="mx-auto w-12 h-12 rounded-2xl bg-secondary/10 text-secondary grid place-items-center font-bold">OK</div>
    <h1 class="mt-4 text-2xl font-bold tracking-tight text-slate-900">¡Compra confirmada!</h1>
    <p class="mt-2 text-slate-700">Tu pedido #<?php echo (int)$orderId; ?> ha sido registrado.</p>
    <p class="mt-1 text-slate-700">Total: <span class="font-semibold text-primary">$<?php echo number_format((float)$total, 2); ?></span></p>
    <div class="mt-6 flex items-center justify-center gap-2">
        <a href="?route=home/index" class="inline-flex items-center justify-center px-4 py-2.5 bg-secondary text-white rounded-xl font-semibold hover:bg-blue-600 active:bg-blue-700 transition shadow-soft">Volver al inicio</a>
        <a href="?route=order/my" class="inline-flex items-center justify-center px-4 py-2.5 bg-white border border-slate-200 rounded-xl font-semibold text-slate-700 hover:bg-slate-50 transition">Ver pedidos</a>
    </div>
</div>
