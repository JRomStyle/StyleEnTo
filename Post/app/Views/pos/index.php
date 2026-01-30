<div class="flex flex-col lg:flex-row gap-8 min-h-[calc(100vh-120px)]">
    <!-- Main POS Area -->
    <div class="flex-1 space-y-8">
        <!-- Search and Categories -->
        <div class="bg-white rounded-[32px] p-6 shadow-sm border border-slate-100">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
                <div>
                    <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Menú Digital</h2>
                    <p class="text-slate-400 text-sm font-medium">Selecciona los productos para el pedido</p>
                </div>
                <div class="relative group">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-[#ffbc0d] transition-colors">🔍</span>
                    <input type="text" placeholder="Buscar producto..." class="bg-slate-50 border-none rounded-2xl py-3 pl-12 pr-6 w-full md:w-64 focus:ring-2 focus:ring-[#ffbc0d] transition-all font-medium text-sm">
                </div>
            </div>

            <!-- Categories Scroll -->
            <div class="overflow-x-auto scrollbar-hide pb-2">
                <div class="flex gap-3 min-w-max" id="pos-categories">
                    <!-- Categories will be rendered here by JS -->
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="bg-white rounded-[32px] p-8 shadow-sm border border-slate-100 min-h-[500px]">
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6" id="pos-products">
                <!-- Products will be rendered here by JS -->
            </div>
        </div>
    </div>

    <!-- Right Sidebar: Order Summary -->
    <div class="w-full lg:w-[400px] shrink-0">
        <div class="bg-white rounded-[32px] shadow-2xl border border-slate-100 h-full flex flex-col overflow-hidden sticky top-8">
            <!-- Header -->
            <div class="p-8 border-b border-slate-50">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight">Pedido Actual</h2>
                    <span class="bg-red-50 text-red-600 text-[10px] font-bold px-2 py-1 rounded-full uppercase tracking-widest">En Proceso</span>
                </div>
                <p class="text-slate-400 text-xs font-medium">ID: #<?= date('YmdHi') ?></p>
            </div>

            <!-- Order Settings -->
            <div class="p-8 space-y-6 bg-slate-50/50">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Servicio</label>
                        <select id="pos-order-type" class="w-full bg-white border-none rounded-2xl px-4 py-3 text-sm font-bold shadow-sm focus:ring-2 focus:ring-[#ffbc0d] transition-all">
                            <?php foreach ($orderTypes as $type): ?>
                                <option value="<?= htmlspecialchars($type) ?>"><?= ucfirst($type) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Pago</label>
                        <select id="pos-payment-method" class="w-full bg-white border-none rounded-2xl px-4 py-3 text-sm font-bold shadow-sm focus:ring-2 focus:ring-[#ffbc0d] transition-all">
                            <?php foreach ($paymentMethods as $method): ?>
                                <option value="<?= htmlspecialchars($method) ?>"><?= ucfirst($method) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Cart Items -->
            <div class="flex-1 overflow-y-auto p-8 scrollbar-hide">
                <div id="pos-cart" class="space-y-4">
                    <!-- Cart items will be rendered here by JS -->
                </div>
                
                <!-- Empty State (Optional handled by JS) -->
                <div id="cart-empty" class="hidden flex flex-col items-center justify-center py-12 text-center">
                    <div class="text-4xl mb-4 opacity-20">🍔</div>
                    <p class="text-slate-400 text-sm font-medium">Tu pedido está vacío</p>
                </div>
            </div>

            <!-- Footer: Totals and Action -->
            <div class="p-8 bg-white border-t border-slate-50 space-y-6">
                <div class="space-y-3">
                    <div class="flex justify-between text-slate-400 font-medium text-sm">
                        <span>Subtotal</span>
                        <span id="pos-subtotal">$0.00</span>
                    </div>
                    <div class="flex justify-between text-slate-400 font-medium text-sm">
                        <span>Impuestos (0%)</span>
                        <span>$0.00</span>
                    </div>
                    <div class="flex justify-between items-center pt-3 border-t border-slate-50">
                        <span class="text-lg font-black text-slate-900 uppercase tracking-tight">Total</span>
                        <span id="pos-total" class="text-2xl font-black text-[#ffbc0d]">$0.00</span>
                    </div>
                </div>

                <button id="pos-submit" class="w-full bg-[#ffbc0d] text-slate-900 font-black rounded-2xl py-5 shadow-lg shadow-yellow-200 hover:bg-[#eab308] hover:shadow-yellow-300 transition-all active:scale-[0.98] uppercase tracking-widest text-sm">
                    Confirmar y Pagar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<script>
window.PosCatalog = <?= json_encode($categories, JSON_UNESCAPED_UNICODE) ?>;
window.PosCsrf = <?= json_encode($csrf ?? '') ?>;
</script>
