<div class="mb-6">
    <div class="flex items-center justify-between mb-1">
        <h1 class="text-2xl font-bold font-display text-white"><?= __('menu_revenue') ?></h1>
        <a href="/reports/menu-revenue/export?date_from=<?= e($dateFrom ?? date('Y-m-01')) ?>&date_to=<?= e($dateTo ?? date('Y-m-t')) ?>"
            class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-primary text-white border-primary hover:bg-primary-hover hover:border-primary-hover hover:shadow-[0_0_15px_var(--color-gold-glow)] hover:text-white">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
            <?= __('export_csv') ?></a>
    </div>
</div>

<?php require __DIR__ . '/../partials/table-date-filter.php' ?>

<?php
$totalRev = array_sum(array_map(fn($m) => (float) $m['total_revenue'], $menus));
$totalCost = array_sum(array_map(fn($m) => (float) $m['total_cost'], $menus));
$totalProfit = $totalRev - $totalCost;
$avgMargin = $totalRev > 0 ? ($totalProfit / $totalRev) * 100 : 0;
?>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-[#111113] border border-white/5 rounded-xl p-4">
        <div class="text-[11px] text-muted uppercase tracking-wider font-semibold mb-1"><?= __('revenue') ?></div>
        <div class="text-xl font-bold font-display text-white">Rp <?= number_format($totalRev, 0, ',', '.') ?></div>
    </div>
    <div class="bg-[#111113] border border-white/5 rounded-xl p-4">
        <div class="text-[11px] text-muted uppercase tracking-wider font-semibold mb-1"><?= __('profit') ?></div>
        <div class="text-xl font-bold font-display <?= $totalProfit > 0 ? 'text-emerald-400' : 'text-red-400' ?>">Rp
            <?= number_format($totalProfit, 0, ',', '.') ?>
        </div>
    </div>
    <div class="bg-[#111113] border border-white/5 rounded-xl p-4">
        <div class="text-[11px] text-muted uppercase tracking-wider font-semibold mb-1"><?= __('profit_margin') ?></div>
        <div class="text-xl font-bold font-display <?= $avgMargin >= 0 ? 'text-emerald-400' : 'text-red-400' ?>">
            <?= number_format($avgMargin, 1) ?>%
        </div>
    </div>
</div>

<div id="table-container" class="bg-[#18181b] border border-border rounded-xl overflow-hidden">
    <?php if (empty($menus)): ?>
    <div class="col-span-full bg-card-bg border border-dashed border-border rounded-[20px] px-6 py-12 text-center text-muted">
        <p><?= __('no_orders') ?></p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr>
                    <th class="bg-black/30 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border px-4 py-3"><?= __('menu') ?></th>
                    <th class="bg-black/30 text-right text-xs font-semibold uppercase tracking-wider text-muted border-b border-border px-4 py-3"><?= __('price') ?></th>
                    <th class="bg-black/30 text-right text-xs font-semibold uppercase tracking-wider text-muted border-b border-border px-4 py-3"><?= __('cost_price') ?></th>
                    <th class="bg-black/30 text-right text-xs font-semibold uppercase tracking-wider text-muted border-b border-border px-4 py-3"><?= __('total_qty') ?></th>
                    <th class="bg-black/30 text-right text-xs font-semibold uppercase tracking-wider text-muted border-b border-border px-4 py-3"><?= __('revenue') ?></th>
                    <th class="bg-black/30 text-right text-xs font-semibold uppercase tracking-wider text-muted border-b border-border px-4 py-3"><?= __('profit') ?></th>
                    <th class="bg-black/30 text-right text-xs font-semibold uppercase tracking-wider text-muted border-b border-border px-4 py-3"><?= __('profit_margin') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($menus as $i => $m): ?>
                    <?php $margin = (float) $m['total_revenue'] > 0 ? ((float) $m['total_revenue'] - (float) $m['total_cost']) / (float) $m['total_revenue'] * 100 : 0; ?>
                    <?php
                    $marginClass = $margin >= 40 ? 'text-emerald-400' : ($margin >= 20 ? 'text-gold' : ($margin >= 0 ? 'text-yellow-400' : 'text-red-400'));
                    $badgeClass = $margin >= 40 ? 'bg-emerald-500/10 text-emerald-400' : ($margin >= 20 ? 'bg-gold/10 text-gold' : ($margin >= 0 ? 'bg-yellow-500/10 text-yellow-400' : 'bg-red-500/10 text-red-400'));
                    ?>
                    <tr class="hover:bg-white/[0.03]">
                        <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-white font-medium"><?= e($m['name']) ?></td>
                        <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-right text-white/70">Rp <?= number_format((float) $m['price'], 0, ',', '.') ?></td>
                        <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-right text-white/50">Rp <?= number_format((float) $m['cost_price'], 0, ',', '.') ?></td>
                        <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-right text-white/70"><?= (int) $m['total_qty'] ?></td>
                        <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-right text-gold font-medium">Rp <?= number_format((float) $m['total_revenue'], 0, ',', '.') ?></td>
                        <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-right font-medium <?= ((float) $m['total_revenue'] - (float) $m['total_cost']) > 0 ? 'text-emerald-400' : 'text-red-400' ?>">Rp <?= number_format((float) $m['total_revenue'] - (float) $m['total_cost'], 0, ',', '.') ?></td>
                        <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-right">
                            <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold <?= $badgeClass ?>"><?= number_format($margin, 1) ?>%</span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
