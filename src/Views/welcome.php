<div class="parallax-orbs">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
</div>

<div class="relative z-10">
    <main>
            <div class="max-w-[1200px] mx-auto px-6">
                <!-- Hero Section -->
                <section class="py-20 md:py-24 text-center relative">
                    <span
                        class="inline-flex items-center bg-gold/10 border border-gold/20 text-gold px-5 py-2 rounded-full text-sm font-semibold mb-6 tracking-wide uppercase">✨
                        <?= __('premium_holiday_catering') ?></span>
                    <h1
                        class="text-[clamp(2.3rem,5vw,3.5rem)] font-extrabold leading-[1.15] tracking-tight mb-6 bg-gradient-to-r from-white via-zinc-100 to-gold bg-clip-text text-transparent">
                        <?= __('exquisite_taste') ?>
                    </h1>
                    <p class="text-lg text-muted max-w-[700px] mx-auto mb-10 leading-relaxed"><?= __('hero_desc') ?></p>
                    <div class="flex items-center justify-center gap-5 flex-wrap">
                        <a href="/menu"
                            class="inline-flex items-center justify-center gap-2 px-8 py-3 rounded-full text-base font-semibold no-underline whitespace-nowrap min-w-[210px] bg-gold border border-gold text-white shadow-[0_0_15px_var(--color-gold-glow)] hover:-translate-y-0.5 hover:shadow-[0_0_25px_var(--color-gold-glow)] transition-all duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="currentColor" class="shrink-0">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                            </svg>
                            <?= __('order_now') ?>
                        </a>
                        <a href="/track-order"
                            class="inline-flex items-center justify-center gap-2 px-8 py-3 rounded-full text-base font-semibold no-underline whitespace-nowrap min-w-[210px] bg-white/5 border border-white/5 text-text backdrop-blur-[8px] hover:text-gold hover:bg-gold/20 hover:border-gold/40 hover:-translate-y-0.5 transition-all duration-300"><?= __('track_order') ?></a>
                    </div>
                </section>

                <?php
                $catMap = [];
                foreach ($categories as $cat) {
                    $catMap[$cat['id']] = $cat['name'];
                }
                $eventMap = [];
                foreach ($events as $ev) {
                    $eventMap[$ev['id']] = $ev['name'];
                }
                ?>
            </div>

            <!-- Food Gallery -->
            <section class="food-gallery">
                <?php
                $galleryMenus = array_filter($menus, fn($m) => ($m['status'] ?? 'active') === 'active' && $m['image']);
                $galleryMenus = array_values($galleryMenus);
                $count = count($galleryMenus);
                if ($count > 10):
                    $widthSets = [[320, 280, 340, 260, 300, 360], [300, 340, 270, 310, 290, 350], [330, 280, 310, 290, 350, 260]];
                    $numRows = $count <= 20 ? 2 : 3;
                    for ($r = 0; $r < $numRows; $r++):
                        shuffle($galleryMenus);
                        $w = $widthSets[$r];
                        ?>
                        <div class="row row-<?= $r + 1 ?>">
                            <?php for ($i = 0; $i < $count; $i++):
                                $m = $galleryMenus[$i];
                                $wi = $w[$i % count($w)];
                                ?>
                                <?php component('progressive-image', ['src' => $m['image'], 'alt' => $m['name'], 'class' => 'border border-border hover:-translate-y-[5px] hover:border-gold/25 hover:shadow-xl', 'style' => "width:{$wi}px"]); ?>
                            <?php endfor; ?>
                            <?php for ($i = 0; $i < $count; $i++):
                                $m = $galleryMenus[$i];
                                $wi = $w[$i % count($w)];
                                ?>
                                <?php component('progressive-image', ['src' => $m['image'], 'alt' => $m['name'], 'class' => 'border border-border hover:-translate-y-[5px] hover:border-gold/25 hover:shadow-xl', 'style' => "width:{$wi}px"]); ?>
                            <?php endfor; ?>
                        </div>
                    <?php endfor; endif; ?>
            </section>

            <div class="max-w-[1200px] mx-auto px-6">
                <!-- Featured Menus -->
                <section>
                    <div class="flex items-end justify-between pb-3 mb-8 border-b border-border">
                        <div>
                            <h2 class="text-3xl font-bold text-white font-display"><?= __('featured_menu') ?></h2>
                            <div class="w-[50px] h-[1.5px] bg-gold shadow-[0_0_8px_var(--color-gold)] mt-1"></div>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2 mb-6" id="category-filters">
                        <button class="filter-tab active" data-category=""><?= __('all_categories') ?></button>
                        <?php foreach ($categories as $cat): ?>
                            <button class="filter-tab"
                                data-category="<?= e((string) $cat['id']) ?>"><?= e($cat['name']) ?></button>
                        <?php endforeach; ?>
                    </div>
                    <div class="grid grid-cols-[repeat(auto-fill,minmax(280px,1fr))] gap-6 mb-20" id="menu-grid">
                        <?php if (empty($initialMenus)): ?>
                            <div
                                class="col-span-full bg-card-bg border border-dashed border-border rounded-xl p-12 text-center text-muted">
                                <div class="text-4xl mb-4 opacity-50">🍽️</div>
                                <p><?= __('no_menu_avail') ?></p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($initialMenus as $menu): ?>
                                <a href="/menu/<?= e($menu['menu_code']) ?>"
                                    class="bg-card-bg border border-border backdrop-blur-[16px] rounded-xl overflow-hidden flex flex-col no-underline text-inherit transition-all duration-300 hover:-translate-y-[5px] hover:border-gold/25 hover:shadow-xl group">
                                    <div
                                        class="h-[180px] bg-gradient-to-br from-gold/20 to-accent-red/10 relative flex items-center justify-center text-white/15">
                                        <?php if ($menu['image']): ?>
                                            <?php component('progressive-image', ['src' => $menu['image'], 'alt' => $menu['name'], 'class' => 'w-full h-full']); ?>
                                        <?php else: ?>
                                            <span class="text-6xl">🍱</span>
                                        <?php endif; ?>

                                        <?php if (isset($eventMap[$menu['event_id']])): ?>
                                            <span
                                                class="absolute bottom-3 left-3 bg-bg/80 border border-border backdrop-blur-[6px] text-gold text-xs font-semibold px-3 py-1 rounded-[6px]"><?= \App\Core\View::e($eventMap[$menu['event_id']]) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="p-5 flex flex-col flex-1">
                                        <h3
                                            class="text-lg font-bold mb-2 text-white font-display group-hover:text-gold transition-colors duration-200">
                                            <?= \App\Core\View::e($menu['name']) ?>
                                        </h3>
                                        <p class="text-sm text-muted mb-5 flex-1 line-clamp-2">
                                            <?= \App\Core\View::e($menu['description']) ?>
                                        </p>
                                        <div class="flex items-center justify-between border-t border-border pt-3 mt-auto">
                                            <span class="font-display text-xl font-bold text-gold">Rp
                                                <?= number_format((float) $menu['price'], 0, ',', '.') ?></span>
                                            <span
                                                class="text-xs text-muted bg-white/5 px-2 py-0.5 rounded border border-border"><?= __('min_portions', ['min' => (int) $menu['minimum_portions']]) ?></span>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <?php if ($lastPage > 1): ?>
                        <div class="text-center mt-10">
                            <a id="see-more-btn" href="javascript:void(0)"
                                class="text-muted no-underline cursor-pointer text-sm hover:text-gold transition-colors duration-200">
                                <?= __('see_more') ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </section>
            </div>

            <!-- Our Location -->
            <section class="my-20">
                <div class="max-w-[1200px] mx-auto px-6">
                    <div class="flex items-end justify-between pb-3 mb-8 border-b border-border">
                        <div>
                            <h2 class="text-3xl font-bold text-white font-display"><?= __('our_location') ?></h2>
                            <div class="w-[50px] h-[1.5px] bg-gold shadow-[0_0_8px_var(--color-gold)] mt-1"></div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                        <div class="rounded-xl overflow-hidden border border-border bg-card-bg relative">
                            <div class="absolute inset-0 z-[1] bg-bg/35 pointer-events-none"></div>
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3962.450988611377!2d110.8339305!3d-6.7146883!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e70db1830041541%3A0x986aac86d66f3252!2sSiwayut%20Catering!5e0!3m2!1sen!2sid!4v1780116984452!5m2!1sen!2sid"
                                loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                                class="w-full h-[320px] max-md:h-[250px] block border-0 relative z-0"></iframe>
                        </div>
                        <div class="flex flex-col gap-6 py-2">
                            <h3 class="font-display text-2xl font-bold text-white mb-1">Siwayut Catering</h3>
                            <div class="flex items-start gap-3 text-muted text-sm leading-relaxed">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="shrink-0 text-gold mt-0.5">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                    <circle cx="12" cy="10" r="3" />
                                </svg>
                                <a href="https://www.google.com/maps?daddr=-6.714749301936785,110.8340308035105"
                                    target="_blank"
                                    class="text-muted no-underline hover:text-gold transition-colors duration-200">7RPM+4HF,
                                    Krandu, Kedungsari, Kec. Gebog,
                                    Kabupaten Kudus, Jawa Tengah 59333</a>
                            </div>
                            <div class="flex items-start gap-3 text-muted text-sm leading-relaxed">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="shrink-0 text-gold mt-0.5">
                                    <path
                                        d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                                </svg>
                                <a href="tel:+6287865252313"
                                    class="text-muted no-underline hover:text-gold transition-colors duration-200">+62
                                    878-6525-2313</a>
                            </div>
                            <div class="flex items-start gap-3 text-muted text-sm leading-relaxed">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="shrink-0 text-gold mt-0.5">
                                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5" />
                                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                                    <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" />
                                </svg>
                                <a href="https://www.instagram.com/siwayut_90/" target="_blank"
                                    class="text-muted no-underline hover:text-gold transition-colors duration-200">@siwayut_90</a>
                            </div>
                            <div class="flex items-start gap-3 text-muted text-sm leading-relaxed">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="shrink-0 text-gold mt-0.5">
                                    <circle cx="12" cy="12" r="10" />
                                    <polyline points="12 6 12 12 16 14" />
                                </svg>
                                <span><?= __('working_hours') ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

    </div>

    <script id="menu-data" type="application/json"><?= json_encode([
        'perPage' => $perPage,
        'currentPage' => $currentPage,
        'lastPage' => $lastPage,
        'eventMap' => $eventMap,
        'categories' => array_map(fn($c) => ['id' => (int) $c['id'], 'name' => $c['name']], $categories),
    ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?></script>

    <script>
        (function () {
            const orbs = document.querySelectorAll('.parallax-orbs .orb');
            const speeds = [0.15, 0.08, 0.12];
            function update() {
                const scrollY = window.scrollY;
                orbs.forEach((orb, i) => {
                    orb.style.transform = `translateY(${-0.5 * scrollY * speeds[i % speeds.length]}px)`;
                });
            }
            window.addEventListener('scroll', update, { passive: true });
            update();
        })();
    </script>