<div class="max-w-[1040px] mx-auto">
    <!-- Back + header -->
    <div class="flex items-center justify-between mb-10 flex-wrap gap-3">
        <a href="/menus"
            class="inline-flex items-center gap-2 px-0 text-sm text-muted no-underline hover:text-gold transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            Back to Menus
        </a>
        <div class="flex items-center gap-3">
            <a href="#" data-edit="menus" data-id="<?= (int)$menu['id'] ?>"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-full text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-white/5 border-border text-text backdrop-blur-[8px] hover:bg-gold hover:border-gold hover:shadow-[0_0_15px_var(--color-gold-glow)] hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/></svg>
                Edit
            </a>
            <form action="/menus/<?= (int)$menu['id'] ?>/delete" method="POST" class="inline">
                <?= csrf_field() ?>
                <button type="submit" data-modal-confirm="Are you sure you want to delete this menu?"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-full text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-white/5 border-border text-text backdrop-blur-[8px] hover:bg-danger hover:border-danger hover:shadow-[0_0_15px_rgba(239,68,68,0.3)] hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Hero: Image + floating info panel -->
    <div class="relative mb-10">
        <?php if ($menu['image']): ?>
        <div class="relative h-[340px] max-md:h-[260px] rounded-2xl overflow-hidden bg-gradient-to-br from-gold/20 to-accent-red/10">
            <?php component('progressive-image', ['src' => $menu['image'], 'alt' => $menu['name'], 'class' => 'w-full h-full']); ?>
            <div class="absolute inset-0 bg-gradient-to-t from-[#09090b] via-[#09090b]/20 to-transparent pointer-events-none"></div>
        </div>
        <?php else: ?>
        <div class="h-[260px] rounded-2xl bg-gradient-to-br from-gold/20 to-accent-red/10 flex items-center justify-center">
            <span class="text-7xl opacity-25">🍱</span>
        </div>
        <?php endif; ?>

        <!-- Floating info panel -->
        <div class="absolute -bottom-8 left-6 right-6 max-md:static max-md:mt-6 max-md:px-0">
            <div class="bg-[#18181b]/90 backdrop-blur-[20px] border border-white/10 rounded-xl px-6 py-5 flex items-center justify-between flex-wrap gap-4 shadow-xl">
                <div>
                    <div class="inline-flex items-center gap-2.5 mb-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[0.7rem] font-semibold uppercase tracking-widest"
                            style="background:<?= $menu['status'] === 'active' ? 'rgba(16,185,129,0.15)' : 'rgba(239,68,68,0.15)' ?>;color:<?= $menu['status'] === 'active' ? '#10b981' : '#ef4444' ?>">
                            <?= e(ucfirst($menu['status'])) ?>
                        </span>
                        <span class="text-[0.7rem] text-muted uppercase tracking-widest">Menu</span>
                    </div>
                    <h1 class="text-2xl md:text-3xl font-bold font-display text-text leading-tight"><?= e($menu['name']) ?></h1>
                </div>
                <div class="text-right shrink-0">
                    <div class="text-xs text-muted uppercase tracking-wider font-medium">Price</div>
                    <div class="font-display text-2xl md:text-3xl font-bold text-gold">Rp <?= number_format((float)$menu['price'], 0, ',', '.') ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Spacer for floating panel (desktop) -->
    <div class="h-8 max-md:hidden"></div>

    <!-- Premium stat badges -->
    <div class="flex flex-wrap items-center gap-2.5 mb-10">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/[0.04] border border-white/5 text-xs text-muted font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-gold"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z"/></svg>
            <?= e($category['name'] ?? 'Uncategorized') ?>
        </span>
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/[0.04] border border-white/5 text-xs text-muted font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-gold"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
            <?= e($event['name'] ?? 'General') ?>
        </span>
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/[0.04] border border-white/5 text-xs text-muted font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-gold"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
            Min <?= (int)$menu['minimum_portions'] ?> portions
        </span>
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/[0.04] border border-white/5 text-xs text-muted font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-gold"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            Created <?= date('d M Y', strtotime($menu['created_at'])) ?>
        </span>
    </div>

    <!-- Two-column: Description + price detail -->
    <div class="grid grid-cols-1 md:grid-cols-[1.6fr_1fr] gap-8 mb-10">
        <!-- Description -->
        <div>
            <h2 class="text-sm font-semibold text-muted uppercase tracking-widest mb-4 flex items-center gap-3">
                <span class="w-6 h-px bg-gold/50"></span>
                About This Menu
            </h2>
            <div class="text-sm md:text-base text-text leading-relaxed whitespace-pre-line font-body">
                <?= $menu['description'] ? nl2br(e($menu['description'])) : '<span class="text-muted italic">No description provided.</span>' ?>
            </div>
        </div>

        <!-- Sidebar: Price breakdown -->
        <div class="bg-white/[0.03] border border-white/5 rounded-xl p-6">
            <div class="space-y-4">
                <div>
                    <div class="text-xs text-muted uppercase tracking-wider font-medium mb-1">Price per Portion</div>
                    <div class="font-display text-3xl font-bold text-gold">Rp <?= number_format((float)$menu['price'], 0, ',', '.') ?></div>
                </div>
                <div class="pt-3 border-t border-white/5">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-muted">Minimum order</span>
                        <span class="text-text font-semibold"><?= (int)$menu['minimum_portions'] ?> portions</span>
                    </div>
                    <div class="flex items-center justify-between text-sm mt-2">
                        <span class="text-muted">Total starting from</span>
                        <span class="text-text font-semibold text-gold">Rp <?= number_format((float)$menu['price'] * (int)$menu['minimum_portions'], 0, ',', '.') ?></span>
                    </div>
                </div>
                <?php if ($menu['status'] === 'active'): ?>
                <div class="pt-3">
                    <a href="/order-form?menu_id=<?= (int)$menu['id'] ?>"
                        class="flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-lg text-sm font-semibold no-underline whitespace-nowrap bg-gold border border-gold text-white shadow-[0_0_12px_var(--color-gold-glow)] hover:-translate-y-0.5 hover:shadow-[0_0_20px_var(--color-gold-glow)] transition-all duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        Order This Menu
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Orders as activity feed -->
    <div>
        <h2 class="text-sm font-semibold text-muted uppercase tracking-widest mb-6 flex items-center gap-3">
            <span class="w-6 h-px bg-gold/50"></span>
            Recent Orders
            <span class="text-xs font-normal text-muted/60 tracking-normal">(last 10)</span>
        </h2>

        <?php if (empty($recentOrders)): ?>
        <div class="bg-white/[0.02] border border-dashed border-white/5 rounded-xl py-12 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="mx-auto mb-3 text-muted/40"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15a2.25 2.25 0 0 1 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"/></svg>
            <p class="text-sm text-muted/60">No orders have included this menu yet.</p>
        </div>
        <?php else: ?>
        <div class="space-y-2">
            <?php foreach ($recentOrders as $ord): ?>
            <a href="/orders/<?= (int)$ord['id'] ?>"
                class="flex items-center justify-between gap-4 px-5 py-3.5 rounded-xl bg-white/[0.02] border border-white/5 no-underline text-inherit hover:bg-white/[0.04] hover:border-white/10 transition-all duration-200 group">
                <div class="flex items-center gap-4 min-w-0">
                    <div class="w-9 h-9 rounded-lg bg-white/[0.04] border border-white/5 flex items-center justify-center shrink-0 group-hover:border-gold/30 group-hover:bg-gold/5 transition-all duration-200">
                        <span class="text-xs font-mono font-semibold text-muted group-hover:text-gold transition-colors duration-200">#<?= (int)$ord['id'] ?></span>
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-medium text-text truncate"><?= e($ord['customer_name'] ?? 'Unknown') ?></div>
                        <div class="text-xs text-muted"><?= date('d M Y', strtotime($ord['created_at'])) ?></div>
                    </div>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <span class="text-sm font-semibold text-success">Rp <?= number_format((float)$ord['total_price'], 0, ',', '.') ?></span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.65rem] font-semibold uppercase tracking-wider"
                        style="background:<?php
                            $sc = ['pending'=>'rgba(245,158,11,0.12)','processing'=>'rgba(79,70,229,0.12)','delivering'=>'rgba(79,70,229,0.12)','completed'=>'rgba(16,185,129,0.12)','cancelled'=>'rgba(239,68,68,0.12)'];
                            echo $sc[$ord['status']] ?? 'rgba(161,161,170,0.12)';
                        ?>;color:<?php
                            $st = ['pending'=>'#f59e0b','processing'=>'#818cf8','delivering'=>'#818cf8','completed'=>'#10b981','cancelled'=>'#ef4444'];
                            echo $st[$ord['status']] ?? '#a1a1aa';
                        ?>">
                        <?= e($ord['status']) ?>
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-muted/30 group-hover:text-gold/50 transition-colors duration-200"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
