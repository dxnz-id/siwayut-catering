<div class="mb-8">
    <div class="flex items-center justify-between mb-1">
        <h1 class="text-2xl font-bold font-display text-white"><?= __('menu_revenue') ?></h1>
        <a href="/dashboard" class="text-sm text-muted hover:text-white transition-colors no-underline">&larr; <?= __('back') ?></a>
    </div>
    <div class="w-10 h-[2px] bg-gold rounded-full mt-2"></div>
</div>

<?php
$totalRev = array_sum(array_map(fn($m) => (float)$m['total_revenue'], $menus));
$totalCost = array_sum(array_map(fn($m) => (float)$m['total_cost'], $menus));
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
        <div class="text-xl font-bold font-display <?= $totalProfit > 0 ? 'text-emerald-400' : 'text-red-400' ?>">Rp <?= number_format($totalProfit, 0, ',', '.') ?></div>
    </div>
    <div class="bg-[#111113] border border-white/5 rounded-xl p-4">
        <div class="text-[11px] text-muted uppercase tracking-wider font-semibold mb-1"><?= __('profit_margin') ?></div>
        <div class="text-xl font-bold font-display <?= $avgMargin >= 0 ? 'text-emerald-400' : 'text-red-400' ?>"><?= number_format($avgMargin, 1) ?>%</div>
    </div>
</div>

<div class="bg-[#111113] border border-white/5 rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-white/[0.02]">
                    <th class="text-left text-[11px] text-muted uppercase tracking-wider font-semibold px-5 py-3"><?= __('menu') ?></th>
                    <th class="text-right text-[11px] text-muted uppercase tracking-wider font-semibold px-5 py-3"><?= __('price') ?></th>
                    <th class="text-right text-[11px] text-muted uppercase tracking-wider font-semibold px-5 py-3"><?= __('cost_price') ?></th>
                    <th class="text-right text-[11px] text-muted uppercase tracking-wider font-semibold px-5 py-3"><?= __('total_qty') ?></th>
                    <th class="text-right text-[11px] text-muted uppercase tracking-wider font-semibold px-5 py-3"><?= __('revenue') ?></th>
                    <th class="text-right text-[11px] text-muted uppercase tracking-wider font-semibold px-5 py-3"><?= __('profit') ?></th>
                    <th class="text-right text-[11px] text-muted uppercase tracking-wider font-semibold px-5 py-3"><?= __('profit_margin') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($menus)): ?>
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-muted text-sm"><?= __('no_orders') ?></td>
                </tr>
                <?php else: ?>
                <?php foreach ($menus as $i => $m): ?>
                <?php $margin = (float)$m['total_revenue'] > 0 ? ((float)$m['total_revenue'] - (float)$m['total_cost']) / (float)$m['total_revenue'] * 100 : 0; ?>
                <?php
                $marginClass = $margin >= 40 ? 'text-emerald-400' : ($margin >= 20 ? 'text-gold' : ($margin >= 0 ? 'text-yellow-400' : 'text-red-400'));
                $badgeClass = $margin >= 40 ? 'bg-emerald-500/10 text-emerald-400' : ($margin >= 20 ? 'bg-gold/10 text-gold' : ($margin >= 0 ? 'bg-yellow-500/10 text-yellow-400' : 'bg-red-500/10 text-red-400'));
                ?>
                <tr class="border-t border-white/5 <?= $i % 2 === 0 ? 'bg-white/[0.01]' : '' ?>">
                    <td class="px-5 py-3 text-white font-medium"><?= e($m['name']) ?></td>
                    <td class="px-5 py-3 text-right text-white/70">Rp <?= number_format((float)$m['price'], 0, ',', '.') ?></td>
                    <td class="px-5 py-3 text-right text-white/50">Rp <?= number_format((float)$m['cost_price'], 0, ',', '.') ?></td>
                    <td class="px-5 py-3 text-right text-white/70"><?= (int)$m['total_qty'] ?></td>
                    <td class="px-5 py-3 text-right text-gold font-medium">Rp <?= number_format((float)$m['total_revenue'], 0, ',', '.') ?></td>
                    <td class="px-5 py-3 text-right font-medium <?= ((float)$m['total_revenue'] - (float)$m['total_cost']) > 0 ? 'text-emerald-400' : 'text-red-400' ?>">Rp <?= number_format((float)$m['total_revenue'] - (float)$m['total_cost'], 0, ',', '.') ?></td>
                    <td class="px-5 py-3 text-right">
                        <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold <?= $badgeClass ?>"><?= number_format($margin, 1) ?>%</span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
