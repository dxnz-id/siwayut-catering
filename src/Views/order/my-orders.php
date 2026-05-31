

    <main class="max-w-[900px] mx-auto px-6 py-10">

        <h1 class="text-2xl font-bold font-display text-text mb-8"><?= __('my_orders') ?></h1>

        <?php if (empty($orders)): ?>
            <div class="bg-card-bg border border-dashed border-border rounded-xl py-16 text-center">
                <div class="text-5xl mb-4 opacity-40">📋</div>
                <p class="text-muted mb-6"><?= __('no_orders_yet') ?></p>
                <a href="/order-form"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-full text-sm font-semibold no-underline bg-gold border border-gold text-white shadow-[0_0_12px_var(--color-gold-glow)] hover:-translate-y-0.5 transition-all duration-300"><?= __('order_now') ?></a>
            </div>
        <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($orders as $ord): ?>
                    <a href="/track-order/<?= e($ord['order_number']) ?>"
                        class="flex items-center justify-between gap-4 px-5 py-4 rounded-xl bg-white/[0.02] border border-white/5 no-underline text-inherit hover:bg-white/[0.04] hover:border-white/10 transition-all duration-200 group">
                        <div class="min-w-0">
                            <div class="text-sm font-medium text-text"><?= e($ord['order_number']) ?></div>
                            <div class="text-xs text-muted mt-0.5"><?= date('d M Y', strtotime($ord['created_at'])) ?></div>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <span class="text-sm font-semibold text-success">Rp
                                <?= number_format((float) $ord['total_price'], 0, ',', '.') ?></span>
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.65rem] font-semibold uppercase tracking-wider"
                                style="background:<?php
                                $sc = ['pending' => 'rgba(245,158,11,0.12)', 'processing' => 'rgba(79,70,229,0.12)', 'delivering' => 'rgba(79,70,229,0.12)', 'completed' => 'rgba(16,185,129,0.12)', 'cancelled' => 'rgba(239,68,68,0.12)'];
                                echo $sc[$ord['status']] ?? 'rgba(161,161,170,0.12)';
                                ?>;color:<?php
                                $st = ['pending' => '#f59e0b', 'processing' => '#818cf8', 'delivering' => '#818cf8', 'completed' => '#10b981', 'cancelled' => '#ef4444'];
                                echo $st[$ord['status']] ?? '#a1a1aa';
                                ?>">
                                <?= e(__($ord['status'])) ?>
                            </span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor"
                                class="text-muted/30 group-hover:text-gold/50 transition-colors duration-200">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </main>