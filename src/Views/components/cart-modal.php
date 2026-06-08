<div id="cart-modal" class="fixed inset-0 z-[200] flex items-center justify-center p-4 transition-opacity duration-200"
     style="background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);-webkit-backdrop-filter:blur(4px);display:none;opacity:0">
    <div id="cart-modal-card"
         class="w-full max-w-md bg-[#18181b] border border-white/10 rounded-2xl shadow-2xl p-6 relative"
         style="transform:scale(0.95) translateY(10px);opacity:0;transition:transform 200ms cubic-bezier(0.16,1,0.3,1),opacity 200ms ease-out">
        <button id="cart-modal-close" type="button"
                class="absolute top-3 right-3 w-8 h-8 flex items-center justify-center rounded-lg text-muted hover:text-text hover:bg-white/5 transition-all duration-150 cursor-pointer border-0 bg-transparent">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
        </button>

        <h3 class="text-lg font-bold text-white font-display mb-5 pr-6"><?= __('add_to_cart') ?></h3>

        <div class="flex gap-4 mb-6">
            <div id="cart-modal-image-wrap" class="w-24 h-24 rounded-xl bg-gradient-to-br from-gold/20 to-accent-red/10 flex items-center justify-center shrink-0 overflow-hidden">
                <span id="cart-modal-image" class="text-4xl">🍱</span>
            </div>
            <div class="flex flex-col justify-center min-w-0">
                <h4 id="cart-modal-name" class="text-base font-bold text-white font-display truncate"></h4>
                <p id="cart-modal-price" class="text-sm text-gold font-semibold mt-1"></p>
                <p id="cart-modal-min" class="text-xs text-muted mt-0.5"></p>
            </div>
        </div>

        <div class="bg-white/5 border border-border rounded-xl p-4 mb-6">
            <div class="flex items-center justify-between">
                <span class="text-sm text-muted font-medium"><?= __('qty') ?></span>
                <div class="flex items-center border border-border rounded-lg overflow-hidden">
                    <button type="button" id="modal-qty-minus" class="w-10 h-10 flex items-center justify-center text-muted hover:text-text hover:bg-white/5 transition-all cursor-pointer bg-transparent border-0 text-lg font-medium select-none">–</button>
                    <input type="text" id="modal-qty-input" inputmode="numeric" pattern="[0-9]*" class="w-16 h-10 text-center bg-transparent border-x border-border text-text text-sm font-medium outline-none" value="1">
                    <button type="button" id="modal-qty-plus" class="w-10 h-10 flex items-center justify-center text-muted hover:text-text hover:bg-white/5 transition-all cursor-pointer bg-transparent border-0 text-lg font-medium select-none">+</button>
                </div>
            </div>
            <div class="flex items-center justify-between pt-3 mt-3 border-t border-white/5">
                <span class="text-sm text-muted"><?= __('total') ?></span>
                <span id="cart-modal-total" class="text-base font-bold text-gold font-display">Rp&nbsp;0</span>
            </div>
        </div>

        <input type="hidden" id="cart-modal-csrf" value="<?= \App\Core\Csrf::token() ?>">
        <input type="hidden" id="cart-modal-menu-id" value="">
        <input type="hidden" id="cart-modal-min-qty" value="">
        <input type="hidden" id="cart-modal-unit-price" value="">

        <div class="flex items-center justify-end gap-3">
            <button type="button" id="cart-modal-cancel"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-white/6 text-text border-border hover:bg-white/10 hover:text-text">
                <?= __('cancel') ?>
            </button>
            <button type="button" id="cart-modal-add"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-gold text-white border-gold hover:bg-primary-hover hover:border-primary-hover">
                + <?= __('add_to_cart') ?>
            </button>
        </div>
    </div>
</div>
