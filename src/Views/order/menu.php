<div class="max-w-[1200px] mx-auto px-6 pb-32">
    <script id="menu-data" type="application/json"><?= json_encode([
        'currentPage' => $currentPage,
        'lastPage' => $lastPage,
        'total' => $totalMenus,
        'cartCount' => $cartCount,
        'addToCartText' => __('add_to_cart'),
        'csrfToken' => \App\Core\Csrf::token(),
    ], JSON_UNESCAPED_UNICODE) ?></script>

    <!-- Header -->
    <div class="flex items-center justify-between mt-10 mb-6">
        <h1 class="text-[1.75rem] max-md:text-[1.4rem] font-bold font-display"><?= __('choose_menu') ?></h1>
        <a href="/cart"
           id="cart-badge-link"
           class="relative inline-flex items-center justify-center w-11 h-11 rounded-xl bg-white/5 border border-border text-muted hover:text-gold hover:border-gold/30 transition-all no-underline <?= $cartCount > 0 ? '' : 'pointer-events-none' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
            </svg>
            <span id="cart-badge"
                  class="absolute -top-1.5 -right-1.5 min-w-[20px] h-5 flex items-center justify-center rounded-full bg-gold text-white text-[0.65rem] font-bold px-1 leading-none <?= $cartCount > 0 ? '' : 'hidden' ?>">
                <?= $cartCount ?>
            </span>
        </a>
    </div>

    <!-- Search bar -->
    <div class="mb-6">
        <input type="search" id="menu-search"
               placeholder="<?= __('search_menu_ph') ?>"
               class="w-full px-5 py-3 bg-white/5 border border-border rounded-xl text-text text-[0.95rem] outline-none transition-all duration-300 placeholder:text-white/20 focus:border-gold focus:ring-[3px] focus:ring-gold/20">
    </div>

    <!-- Category tabs -->
    <div id="category-filters" class="flex gap-2 overflow-x-auto pb-2 mb-8 scrollbar-none">
        <button class="filter-tab active shrink-0 px-4 py-2 rounded-full text-sm font-medium border transition-all duration-200 cursor-pointer whitespace-nowrap bg-white/5 border-border text-muted hover:text-text hover:border-gold/30" data-category=""><?= __('all_categories') ?></button>
        <?php foreach ($categories as $cat): ?>
            <button class="filter-tab shrink-0 px-4 py-2 rounded-full text-sm font-medium border transition-all duration-200 cursor-pointer whitespace-nowrap bg-white/5 border-border text-muted hover:text-text hover:border-gold/30" data-category="<?= (int) $cat['id'] ?>">
                <?= e($cat['name']) ?>
            </button>
        <?php endforeach; ?>
    </div>

    <!-- Menu grid -->
    <div id="menu-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        <?php foreach ($initialMenus as $menu): ?>
            <div class="bg-card-bg border border-border backdrop-blur-[16px] rounded-xl overflow-hidden flex flex-col">
                <div class="h-[180px] bg-gradient-to-br from-gold/20 to-accent-red/10 relative flex items-center justify-center text-white/15">
                    <?php if ($menu['image']): ?>
                        <?php component('progressive-image', ['src' => $menu['image'], 'alt' => $menu['name'], 'class' => 'w-full h-full']) ?>
                    <?php else: ?>
                        <span class="text-6xl">🍱</span>
                    <?php endif; ?>
                    <?php if (!empty($menu['event_name'])): ?>
                        <span class="absolute bottom-3 left-3 bg-bg/80 border border-border backdrop-blur-[6px] text-gold text-xs font-semibold px-3 py-1 rounded-[6px]">
                            <?= e($menu['event_name']) ?>
                        </span>
                    <?php endif; ?>
                </div>
                <div class="p-5 flex flex-col flex-1">
                    <h3 class="text-lg font-bold mb-2 text-white font-display"><?= e($menu['name']) ?></h3>
                    <p class="text-sm text-muted mb-4 flex-1 line-clamp-2"><?= e($menu['description'] ?? '') ?></p>
                    <div class="flex items-center justify-between mb-4">
                        <span class="font-display text-xl font-bold text-gold">Rp <?= number_format((float) $menu['price'], 0, ',', '.') ?></span>
                        <span class="text-xs text-muted bg-white/5 px-2 py-0.5 rounded border border-border"><?= __('min_portion') ?> <?= (int) $menu['minimum_portions'] ?></span>
                    </div>
                    <button type="button"
                            class="w-full px-4 py-2 bg-gold border border-gold rounded-lg text-white text-sm font-semibold cursor-pointer transition-all duration-200 hover:bg-primary-hover hover:border-primary-hover active:scale-[0.97] add-to-cart-btn"
                            data-id="<?= (int) $menu['id'] ?>"
                            data-name="<?= e($menu['name']) ?>"
                            data-price="<?= (int) $menu['price'] ?>"
                            data-min="<?= (int) $menu['minimum_portions'] ?>"
                            data-image="<?= e($menu['image'] ?? '') ?>">
                        + <?= __('add_to_cart') ?>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- See More -->
    <div id="see-more-wrap" class="text-center mb-16" <?= $currentPage >= $lastPage ? 'style="display:none"' : '' ?>>
        <button id="see-more-btn"
                class="inline-flex items-center justify-center gap-2 px-8 py-3 rounded-full text-base font-semibold cursor-pointer border transition-all duration-200 no-underline whitespace-nowrap bg-white/5 border-border text-text hover:text-gold hover:border-gold/40 hover:bg-gold/10">
            <?= __('see_more') ?>
        </button>
    </div>


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
<script src="/assets/js/modules/menu-browse.js"></script>
<script>
(function () {
    var module = window.AppModules && window.AppModules.menuBrowse;
    if (module && typeof module.init === 'function') {
        module.init();
    }
})();
</script>
