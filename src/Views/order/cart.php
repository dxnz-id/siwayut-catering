<div class="max-w-[1200px] mx-auto px-6 pb-32">
    <script id="cart-data" type="application/json"><?= json_encode([
        'csrfToken' => \App\Core\Csrf::token(),
    ], JSON_UNESCAPED_UNICODE) ?></script>

    <div class="flex items-center justify-between mt-10 mb-8">
        <h1 class="text-[1.75rem] max-md:text-[1.4rem] font-bold font-display"><?= __('cart') ?></h1>
        <?php if ($cartCount > 0): ?>
            <span class="text-sm text-muted"><?= $cartCount ?> <?= __('total_items') ?></span>
        <?php endif; ?>
    </div>

    <?php if (empty($items)): ?>
        <div class="flex flex-col items-center justify-center py-24 text-center">
            <span class="text-7xl mb-6 opacity-30">🛒</span>
            <h2 class="text-xl font-bold font-display text-text mb-2"><?= __('cart_empty') ?></h2>
            <p class="text-sm text-muted mb-8 max-w-xs"><?= __('cart_empty_desc') ?></p>
            <a href="/menu"
               class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold bg-gold border border-gold text-white no-underline hover:bg-primary-hover hover:border-primary-hover transition-all duration-200">
                <?= __('browse_menu') ?>
            </a>
        </div>
    <?php else: ?>
        <div class="flex gap-8 flex-col lg:flex-row items-start">
            <!-- Left column (items) -->
            <div class="w-full lg:w-[68%]">
                <div id="cart-items" class="bg-card-bg border border-border backdrop-blur-[16px] rounded-xl overflow-hidden">
                    <!-- Select All header -->
                    <div class="px-5 py-4 flex items-center gap-3 border-b border-border">
                        <input type="checkbox" id="select-all" value="" class="w-4 h-4 border border-border rounded-xs bg-white/5 accent-gold focus:ring-2 focus:ring-gold/30">
                        <label for="select-all" class="text-base font-semibold text-text cursor-pointer select-none"><?= __('select_all') ?></label>
                        <button type="button" id="remove-selected-btn"
                                class="text-muted/30 cursor-not-allowed pointer-events-none transition-colors duration-200 bg-transparent border-0 ml-auto p-1"
                                aria-label="Remove selected">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                        </button>
                    </div>
                    <!-- Product items -->
                    <?php $lastIdx = count($items) - 1; ?>
                    <?php foreach ($items as $i => $item): ?>
                    <div class="cart-item px-5 py-5 <?= $i < $lastIdx ? 'border-b border-border' : '' ?>" data-menu-id="<?= (int) $item['menu_id'] ?>" data-min="<?= (int) $item['minimum_portions'] ?>">
                        <div class="flex items-start gap-4">
                            <!-- Slot 1: Checkbox -->
                            <div class="pt-1 shrink-0">
                                <input type="checkbox" value="" class="w-4 h-4 border border-border rounded-xs bg-white/5 accent-gold focus:ring-2 focus:ring-gold/30 item-check">
                            </div>

                            <!-- Slot 2: Image -->
                            <div class="shrink-0 rounded-lg overflow-hidden bg-gradient-to-br from-gold/20 to-accent-red/10 relative flex items-center justify-center text-white/15" style="width: 80px; height: 80px;">
                                <?php if ($item['image']): ?>
                                    <?php component('progressive-image', ['src' => $item['image'], 'alt' => $item['name'], 'class' => 'w-full h-full object-cover']) ?>
                                <?php else: ?>
                                    <span class="text-3xl">🍱</span>
                                <?php endif; ?>
                            </div>

                            <!-- Main Content Column -->
                            <div class="flex-1 flex flex-col justify-between" style="min-height: 80px;">
                                <!-- Top Row: Title + Price -->
                                <div class="flex justify-between items-start gap-4">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm font-medium text-text line-clamp-2 leading-snug"><?= e($item['name']) ?></h3>
                                        <p class="text-xs text-muted mt-1"><?= __('min_portion') ?> <?= (int) $item['minimum_portions'] ?></p>
                                    </div>
                                    <div class="text-right whitespace-nowrap pl-4">
                                        <span class="text-base font-bold text-white">Rp<?= number_format((float) $item['price'], 0, ',', '.') ?></span>
                                    </div>
                                </div>

                                <!-- Bottom Row: Actions -->
                                <div class="flex justify-end items-center gap-4 mt-3">
                                    <!-- Subtotal -->
                                    <span class="cart-subtotal text-xs text-muted whitespace-nowrap">Rp <?= number_format((float) $item['subtotal'], 0, ',', '.') ?></span>
                                    <!-- Trash Icon -->
                                    <button type="button" class="cart-remove-btn text-muted hover:text-danger transition-colors cursor-pointer bg-transparent border-0" aria-label="Remove item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                    </button>
                                    <!-- Quantity Pill -->
                                    <div class="flex items-center border border-border rounded-full h-8 px-1 bg-transparent">
                                        <button type="button" class="cart-qty-minus w-7 h-full flex items-center justify-center text-muted hover:text-white transition-colors cursor-pointer bg-transparent border-0 text-sm font-medium select-none">–</button>
                                        <input type="text" inputmode="numeric" pattern="[0-9]*" value="<?= (int) $item['quantity'] ?>"
                                               class="cart-qty-input w-8 h-full text-center bg-transparent border-none text-text text-sm font-medium outline-none">
                                        <button type="button" class="cart-qty-plus w-7 h-full flex items-center justify-center text-muted hover:text-white transition-colors cursor-pointer bg-transparent border-0 text-sm font-medium select-none">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right column: Summary card (desktop) -->
            <div class="w-full lg:w-[32%] shrink-0 lg:sticky lg:top-24 self-start hidden lg:block">
                <div class="bg-card-bg border border-border backdrop-blur-[16px] rounded-xl p-6">
                    <h3 class="text-sm font-semibold text-muted uppercase tracking-widest mb-5"><?= __('summary') ?></h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-muted"><?= __('subtotal') ?></span>
                            <span id="cart-summary-subtotal" class="text-text font-semibold">Rp <?= number_format((float) $total, 0, ',', '.') ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-muted"><?= __('total_items') ?></span>
                            <span id="cart-summary-count" class="text-text font-semibold"><?= $cartCount ?></span>
                        </div>
                    </div>
                    <div class="border-t border-white/5 mt-4 pt-4">
                        <div class="flex items-center justify-between">
                            <span class="text-text font-bold font-display"><?= __('total') ?></span>
                            <span id="cart-summary-total" class="text-lg font-bold text-gold font-display">Rp <?= number_format((float) $total, 0, ',', '.') ?></span>
                        </div>
                    </div>
                    <a href="/checkout"
                       class="block w-full mt-5 px-5 py-3 rounded-xl text-sm font-semibold text-center no-underline bg-gold border border-gold text-white shadow-[0_0_12px_var(--color-gold-glow)] hover:bg-primary-hover hover:border-primary-hover hover:-translate-y-0.5 hover:shadow-[0_0_20px_var(--color-gold-glow)] transition-all duration-300">
                        <?= __('checkout') ?>
                    </a>
                </div>
            </div>
        </div>

    <?php endif; ?>

    <!-- Sticky bottom bar (mobile) -->
    <?php if ($cartCount > 0): ?>
    <div id="cart-bottom-bar"
         class="fixed bottom-0 left-0 right-0 bg-[#111113]/95 backdrop-blur-[12px] border-t border-white/10 p-4 lg:hidden transition-all duration-300"
         style="z-index: 100">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-xs text-muted"><?= __('total') ?></span>
                <div class="text-base font-bold text-gold font-display">Rp <span id="cart-bar-total"><?= number_format((float) $total, 0, ',', '.') ?></span></div>
            </div>
            <a href="/checkout"
               class="px-6 py-2.5 rounded-lg text-sm font-semibold no-underline bg-gold border border-gold text-white shadow-[0_0_12px_var(--color-gold-glow)] hover:bg-primary-hover hover:border-primary-hover transition-all duration-300">
                <?= __('checkout') ?>
            </a>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php component('cart-modal') ?>
<?php
$flashSuccess = \App\Core\Session::getFlash('success');
$flashError = \App\Core\Session::getFlash('error');
$pageFlashes = [];
if ($flashSuccess) $pageFlashes[] = ['type' => 'success', 'message' => $flashSuccess];
if ($flashError) $pageFlashes[] = ['type' => 'error', 'message' => $flashError];
?>
<?php if (!empty($pageFlashes)): ?>
    <?php component('toast', ['flashes' => $pageFlashes]) ?>
<?php endif; ?>
<script>
(function () {
    var selectAll = document.getElementById('select-all');
    var removeBtn = document.getElementById('remove-selected-btn');
    var cartData = document.getElementById('cart-data');
    if (!selectAll) return;

    var csrfToken = '';
    try { csrfToken = JSON.parse(cartData.textContent).csrfToken || ''; } catch (e) {}

    function getChecked() {
        return document.querySelectorAll('.item-check:checked');
    }

    function updateUI() {
        var checks = document.querySelectorAll('.item-check');
        var all = true;
        var checked = getChecked();
        checks.forEach(function (c) { if (!c.checked) all = false; });
        selectAll.checked = all;
        if (checked.length > 0) {
            removeBtn.className = 'text-danger hover:text-red-400 cursor-pointer transition-colors duration-200 bg-transparent border-0 ml-auto p-1';
        } else {
            removeBtn.className = 'text-muted/30 cursor-not-allowed pointer-events-none transition-colors duration-200 bg-transparent border-0 ml-auto p-1';
        }
    }

    selectAll.addEventListener('change', function () {
        var checked = this.checked;
        document.querySelectorAll('.item-check').forEach(function (c) { c.checked = checked; });
        updateUI();
    });

    document.querySelector('#cart-items').addEventListener('change', function (e) {
        if (e.target.classList.contains('item-check')) {
            updateUI();
        }
    });

    if (removeBtn) {
        removeBtn.addEventListener('click', function () {
            var checked = getChecked();
            if (checked.length === 0) return;
            if (!confirm('<?= __('confirm_delete') ?>')) return;

            var ids = [];
            checked.forEach(function (c) {
                var item = c.closest('.cart-item');
                if (item) ids.push(item.getAttribute('data-menu-id'));
            });

            var fd = new FormData();
            fd.append('_csrf_token', csrfToken);
            ids.forEach(function (id) { fd.append('menu_ids[]', id); });

            fetch('/cart/remove-selected', {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function (r) { return r.json(); })
            .then(function (res) {
                if (res.success) {
                    checked.forEach(function (c) {
                        var item = c.closest('.cart-item');
                        if (item) {
                            item.style.transition = 'opacity 200ms, transform 200ms';
                            item.style.opacity = '0';
                            item.style.transform = 'scale(0.95)';
                            setTimeout(function () { item.remove(); }, 200);
                        }
                    });
                    setTimeout(function () {
                        var remaining = document.querySelectorAll('.cart-item').length;
                        if (remaining === 0) {
                            window.location.reload();
                        }
                    }, 250);
                    if (typeof window.showToast === 'function') {
                        window.showToast('<?= e(__('remove')) ?>', 'success');
                    }
                } else {
                    if (typeof window.showToast === 'function') {
                        window.showToast(res.message || 'Error', 'error');
                    }
                }
            })
            .catch(function () {});
        });
    }

    updateUI();
})();
</script>
