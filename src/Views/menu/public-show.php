

    <main class="max-w-[1040px] mx-auto px-6 py-10">

        <!-- Back -->
        <a href="javascript:history.back()" onclick="history.back();return false"
            class="inline-flex items-center gap-2 mb-8 text-sm text-muted no-underline hover:text-gold transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24"
                stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            <?= __('back_to_menus') ?>
        </a>

        <!-- Hero -->
        <div class="relative mb-10">
            <?php if ($menu['image']): ?>
                <div
                    class="relative h-[400px] max-md:h-[260px] rounded-2xl overflow-hidden bg-gradient-to-br from-gold/20 to-accent-red/10">
                    <?php component('progressive-image', ['src' => $menu['image'], 'alt' => $menu['name'], 'class' => 'w-full h-full']); ?>
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-[#09090b] via-[#09090b]/20 to-transparent pointer-events-none">
                    </div>
                </div>
            <?php else: ?>
                <div
                    class="h-[300px] max-md:h-[200px] rounded-2xl bg-gradient-to-br from-gold/20 to-accent-red/10 flex items-center justify-center">
                    <span class="text-8xl opacity-25">🍱</span>
                </div>
            <?php endif; ?>

            <!-- Floating info -->
            <div class="absolute -bottom-8 left-6 right-6 max-md:static max-md:mt-6 max-md:px-0">
                <div
                    class="bg-[#18181b]/90 backdrop-blur-[20px] border border-white/10 rounded-xl px-6 py-5 flex items-center justify-between flex-wrap gap-4 shadow-xl">
                    <div>
                        <div class="inline-flex items-center gap-2.5 mb-2">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[0.7rem] font-semibold uppercase tracking-widest"
                                style="background:<?= $menu['status'] === 'active' ? 'rgba(16,185,129,0.15)' : 'rgba(239,68,68,0.15)' ?>;color:<?= $menu['status'] === 'active' ? '#10b981' : '#ef4444' ?>">
                                <?= e(__($menu['status'])) ?>
                            </span>
                            <span class="text-[0.7rem] text-muted uppercase tracking-widest"><?= __('menu') ?></span>
                        </div>
                        <h1 class="text-2xl md:text-3xl font-bold font-display text-text leading-tight">
                            <?= e($menu['name']) ?></h1>
                        <div class="text-xs text-muted mt-1 font-mono tracking-wide"><?= e($menu['menu_code']) ?></div>
                    </div>
                    <div class="text-right shrink-0">
                        <div class="text-xs text-muted uppercase tracking-wider font-medium"><?= __('price') ?></div>
                        <div class="font-display text-2xl md:text-3xl font-bold text-gold">Rp
                            <?= number_format((float) $menu['price'], 0, ',', '.') ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="h-8 max-md:hidden"></div>

        <!-- Badges -->
        <div class="flex flex-wrap items-center gap-2.5 mb-10">
            <span
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/[0.04] border border-white/5 text-xs text-muted font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" class="text-gold">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                </svg>
                <?= e($category['name'] ?? __('uncategorized')) ?>
            </span>
            <?php if ($event): ?>
                <span
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/[0.04] border border-white/5 text-xs text-muted font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" class="text-gold">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                    </svg>
                    <?= e($event['name']) ?>
                </span>
            <?php endif; ?>
            <span
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/[0.04] border border-white/5 text-xs text-muted font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" class="text-gold">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
                <?= __('min_portion') ?>: <?= (int) $menu['minimum_portions'] ?>
            </span>
        </div>

        <!-- Description + Price breakdown -->
        <div class="grid grid-cols-1 md:grid-cols-[1.6fr_1fr] gap-8 mb-16">
            <div>
                <h2 class="text-sm font-semibold text-muted uppercase tracking-widest mb-4 flex items-center gap-3">
                    <span class="w-6 h-px bg-gold/50"></span>
                    <?= __('about_this_menu') ?>
                </h2>
                <div class="text-sm md:text-base text-text leading-relaxed whitespace-pre-line font-body">
                    <?= $menu['description'] ? nl2br(e($menu['description'])) : '<span class="text-muted italic">' . __('no_description') . '</span>' ?>
                </div>
            </div>

            <div class="bg-white/[0.03] border border-white/5 rounded-xl p-6">
                <div class="space-y-4">
                    <div>
                        <div class="text-xs text-muted uppercase tracking-wider font-medium mb-1">
                            <?= __('price_per_portion') ?></div>
                        <div class="font-display text-3xl font-bold text-gold">Rp
                            <?= number_format((float) $menu['price'], 0, ',', '.') ?></div>
                    </div>
                    <div class="pt-3 border-t border-white/5">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-muted"><?= __('minimum_order') ?></span>
                            <span class="text-text font-semibold"><?= (int) $menu['minimum_portions'] ?>
                                <?= __('portions') ?></span>
                        </div>
                        <div class="flex items-center justify-between text-sm mt-2">
                            <span class="text-muted"><?= __('total_starting_from') ?></span>
                            <span class="text-text font-semibold text-gold">Rp
                                <?= number_format((float) $menu['price'] * (int) $menu['minimum_portions'], 0, ',', '.') ?></span>
                        </div>
                    </div>
                    <div class="pt-3">
                        <button type="button"
                                class="flex items-center justify-center gap-2 w-full px-6 py-3 rounded-xl text-sm font-semibold bg-gold border border-gold text-white shadow-[0_0_12px_var(--color-gold-glow)] hover:-translate-y-0.5 hover:shadow-[0_0_20px_var(--color-gold-glow)] transition-all duration-300 cursor-pointer add-to-cart-btn"
                                data-id="<?= (int) $menu['id'] ?>"
                                data-name="<?= e($menu['name']) ?>"
                                data-price="<?= (int) $menu['price'] ?>"
                                data-min="<?= (int) $menu['minimum_portions'] ?>"
                                data-image="<?= e($menu['image'] ?? '') ?>">
                            + <?= __('add_to_cart') ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related menus -->
        <?php if (!empty($related)): ?>
            <div class="mb-16">
                <h2 class="text-sm font-semibold text-muted uppercase tracking-widest mb-6 flex items-center gap-3">
                    <span class="w-6 h-px bg-gold/50"></span>
                    <?= __('related_menus') ?>
                </h2>
                <div class="grid grid-cols-[repeat(auto-fill,minmax(260px,1fr))] gap-5">
                    <?php foreach ($related as $rm): ?>
                        <a href="/menu/<?= e($rm['menu_code']) ?>"
                            class="bg-card-bg border border-border backdrop-blur-[16px] rounded-xl overflow-hidden flex flex-col no-underline text-inherit transition-all duration-300 hover:-translate-y-[5px] hover:border-gold/25 hover:shadow-xl group">
                            <div
                                class="h-[140px] bg-gradient-to-br from-gold/20 to-accent-red/10 relative flex items-center justify-center text-white/15">
                                <?php if ($rm['image']): ?>
                                    <?php component('progressive-image', ['src' => $rm['image'], 'alt' => $rm['name'], 'class' => 'w-full h-full']); ?>
                                <?php else: ?>
                                    <span class="text-5xl">🍱</span>
                                <?php endif; ?>
                            </div>
                            <div class="p-4 flex flex-col flex-1">
                                <h3
                                    class="text-base font-bold mb-1 text-white font-display group-hover:text-gold transition-colors duration-200">
                                    <?= e($rm['name']) ?></h3>
                                <p class="text-xs text-muted mb-3 flex-1 line-clamp-2"><?= e($rm['description']) ?></p>
                                <div class="font-display text-base font-bold text-gold">Rp
                                    <?= number_format((float) $rm['price'], 0, ',', '.') ?></div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    <?php component('cart-modal') ?>
    </main>