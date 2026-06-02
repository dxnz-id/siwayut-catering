<div class="mb-8">
    <h1 class="text-2xl font-bold font-display text-white"><?= __('dashboard') ?></h1>
    <div class="w-10 h-[2px] bg-gold rounded-full mt-2"></div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-[#111113] border border-white/5 rounded-xl p-5">
        <div class="flex items-center justify-between mb-3">
            <span
                class="text-[11px] text-muted uppercase tracking-[0.08em] font-semibold"><?= __('total_revenue') ?></span>
            <span class="w-7 h-7 rounded-lg bg-gold/10 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="#e58e26" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </span>
        </div>
        <div class="text-2xl font-bold font-display text-white tracking-tight">Rp
            <?= number_format((float) ($kpis['total_revenue'] ?? 0), 0, ',', '.') ?></div>
        <div class="text-[11px] text-muted mt-1.5"><?= __('revenue_this_month') ?> <span class="text-white/80">Rp
                <?= number_format((float) ($kpis['revenue_this_month'] ?? 0), 0, ',', '.') ?></span></div>
    </div>
    <div class="bg-[#111113] border border-white/5 rounded-xl p-5">
        <div class="flex items-center justify-between mb-3">
            <span
                class="text-[11px] text-muted uppercase tracking-[0.08em] font-semibold"><?= __('total_profit') ?></span>
            <span class="w-7 h-7 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="#10b981" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                </svg>
            </span>
        </div>
        <div class="text-2xl font-bold font-display text-white tracking-tight">Rp
            <?= number_format((float) ($kpis['total_profit'] ?? 0), 0, ',', '.') ?></div>
        <div class="text-[11px] text-muted mt-1.5"><?= __('profit_margin') ?> <span
                class="text-emerald-400"><?= ($kpis['total_revenue'] ?? 0) > 0 ? number_format((float) ($kpis['total_profit'] ?? 0) / (float) ($kpis['total_revenue'] ?? 0) * 100, 1) : 0 ?>%</span>
        </div>
    </div>
    <div class="bg-[#111113] border border-white/5 rounded-xl p-5">
        <div class="flex items-center justify-between mb-3">
            <span
                class="text-[11px] text-muted uppercase tracking-[0.08em] font-semibold"><?= __('total_orders') ?></span>
            <span class="w-7 h-7 rounded-lg bg-blue-500/10 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="#3b82f6" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>
            </span>
        </div>
        <div class="text-2xl font-bold font-display text-white tracking-tight">
            <?= number_format((int) ($kpis['total_orders'] ?? 0)) ?></div>
        <div class="text-[11px] text-muted mt-1.5"><?= __('orders_today') ?> <span
                class="text-white/80"><?= (int) ($kpis['orders_today'] ?? 0) ?></span> &middot;
            <?= __('unpaid_orders') ?> <span
                class="<?= ($kpis['unpaid_orders'] ?? 0) > 0 ? 'text-red-400' : 'text-white/80' ?>"><?= (int) ($kpis['unpaid_orders'] ?? 0) ?></span>
        </div>
    </div>
    <div class="bg-[#111113] border border-white/5 rounded-xl p-5">
        <div class="flex items-center justify-between mb-3">
            <span
                class="text-[11px] text-muted uppercase tracking-[0.08em] font-semibold"><?= __('avg_order_value') ?></span>
            <span class="w-7 h-7 rounded-lg bg-violet-500/10 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="#8b5cf6" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.746 2.148-7.175.012-.643-.51-1.1-1.145-1.1H4.5m5.25 0v-.75a2.25 2.25 0 0 0-2.25-2.25h-.75M6.75 14.25A2.25 2.25 0 1 0 7.5 12" />
                </svg>
            </span>
        </div>
        <div class="text-2xl font-bold font-display text-white tracking-tight">Rp
            <?= number_format((float) ($kpis['avg_order_value'] ?? 0), 0, ',', '.') ?></div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
    <div class="lg:col-span-2 bg-[#111113] border border-white/5 rounded-xl p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold font-display text-white"><?= __('revenue_trend') ?></h2>
            <a href="/reports/revenue"
                class="text-[11px] text-gold hover:text-gold/80 transition-colors no-underline"><?= __('revenue_report') ?>
                &rarr;</a>
        </div>
        <canvas id="revenueChart" class="w-full h-full"></canvas>
    </div>
    <div class="bg-[#111113] border border-white/5 rounded-xl p-5">
        <h2 class="text-sm font-semibold font-display text-white mb-4"><?= __('order_status') ?></h2>
        <canvas id="statusChart" class="w-full" height="180"></canvas>
    </div>
</div>

<div class="bg-[#111113] border border-white/5 rounded-xl p-5">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-semibold font-display text-white"><?= __('top_menus') ?></h2>
        <a href="/reports/menu-revenue"
            class="text-[11px] text-gold hover:text-gold/80 transition-colors no-underline"><?= __('menu_revenue') ?>
            &rarr;</a>
    </div>
    <?php if (empty($topMenus)): ?>
        <p class="text-sm text-muted"><?= __('no_orders') ?></p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-[11px] text-muted uppercase tracking-wider">
                        <th class="text-left pb-3 font-medium"><?= __('menu') ?></th>
                        <th class="text-right pb-3 font-medium"><?= __('total_qty') ?></th>
                        <th class="text-right pb-3 font-medium"><?= __('revenue') ?></th>
                        <th class="text-right pb-3 font-medium"><?= __('profit') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $maxRev = max(array_map(fn($m) => (float) $m['total_revenue'], $topMenus)); ?>
                    <?php foreach ($topMenus as $m): ?>
                        <?php $pct = $maxRev > 0 ? ((float) $m['total_revenue'] / $maxRev) * 100 : 0; ?>
                        <tr class="border-t border-white/5">
                            <td class="py-3 pr-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 min-w-0">
                                        <div class="text-white font-medium truncate"><?= e($m['name']) ?></div>
                                        <div class="h-1.5 bg-white/5 rounded-full mt-1.5 overflow-hidden">
                                            <div class="h-full bg-gold/60 rounded-full" style="width:<?= $pct ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 text-right text-white/70"><?= (int) $m['total_qty'] ?></td>
                            <td class="py-3 text-right text-gold font-medium">Rp
                                <?= number_format((float) $m['total_revenue'], 0, ',', '.') ?></td>
                            <td
                                class="py-3 text-right <?= ((float) $m['total_revenue'] - (float) $m['total_cost']) > 0 ? 'text-emerald-400' : 'text-red-400' ?> font-medium">
                                Rp <?= number_format((float) $m['total_revenue'] - (float) $m['total_cost'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script id="dashboard-data" type="application/json"><?= json_encode([
    'chartData' => $chartData,
    'statusBreakdown' => $statusBreakdown,
    'lang' => [
        'revenue' => __('revenue'),
        'profit' => __('profit'),
    ],
], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?></script>
<script src="/assets/js/modules/dashboard-charts.js"></script>