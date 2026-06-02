<div class="mb-8">
    <div class="flex items-center justify-between mb-1">
        <h1 class="text-2xl font-bold font-display text-white"><?= __('revenue_report') ?></h1>
        <a href="/dashboard" class="text-sm text-muted hover:text-white transition-colors no-underline">&larr; <?= __('back') ?></a>
    </div>
    <div class="w-10 h-[2px] bg-gold rounded-full mt-2"></div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-[#111113] border border-white/5 rounded-xl p-4">
        <div class="text-[11px] text-muted uppercase tracking-wider font-semibold mb-1"><?= __('revenue') ?></div>
        <div class="text-xl font-bold font-display text-white">Rp <?= number_format((float)($totals['revenue'] ?? 0), 0, ',', '.') ?></div>
    </div>
    <div class="bg-[#111113] border border-white/5 rounded-xl p-4">
        <div class="text-[11px] text-muted uppercase tracking-wider font-semibold mb-1"><?= __('profit') ?></div>
        <div class="text-xl font-bold font-display <?= ($totals['profit'] ?? 0) > 0 ? 'text-emerald-400' : 'text-red-400' ?>">Rp <?= number_format((float)($totals['profit'] ?? 0), 0, ',', '.') ?></div>
    </div>
    <div class="bg-[#111113] border border-white/5 rounded-xl p-4">
        <div class="text-[11px] text-muted uppercase tracking-wider font-semibold mb-1"><?= __('total_orders') ?></div>
        <div class="text-xl font-bold font-display text-white"><?= (int)($totals['orders'] ?? 0) ?></div>
    </div>
</div>

<div class="bg-[#111113] border border-white/5 rounded-xl p-4 mb-6">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-[11px] font-medium text-muted mb-1"><?= __('date_from') ?></label>
            <input type="date" name="date_from" value="<?= e($dateFrom) ?>" class="px-3 py-1.5 text-sm bg-black/40 border border-white/10 rounded-lg text-white placeholder:text-muted focus:outline-none focus:border-gold/50 transition-colors">
        </div>
        <div>
            <label class="block text-[11px] font-medium text-muted mb-1"><?= __('date_to') ?></label>
            <input type="date" name="date_to" value="<?= e($dateTo) ?>" class="px-3 py-1.5 text-sm bg-black/40 border border-white/10 rounded-lg text-white placeholder:text-muted focus:outline-none focus:border-gold/50 transition-colors">
        </div>
        <button type="submit" class="px-4 py-1.5 text-sm font-medium bg-gold/10 border border-gold/20 text-gold rounded-lg hover:bg-gold/20 transition-colors"><?= __('apply_filter') ?></button>
        <a href="/reports/revenue/export?date_from=<?= e($dateFrom) ?>&date_to=<?= e($dateTo) ?>" class="px-4 py-1.5 text-sm font-medium bg-white/5 border border-white/10 text-muted rounded-lg hover:text-white hover:bg-white/10 transition-colors no-underline"><?= __('export_csv') ?></a>
    </form>
</div>

<div class="bg-[#111113] border border-white/5 rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-white/[0.02]">
                    <th class="text-left text-[11px] text-muted uppercase tracking-wider font-semibold px-5 py-3"><?= __('date') ?></th>
                    <th class="text-right text-[11px] text-muted uppercase tracking-wider font-semibold px-5 py-3"><?= __('orders') ?></th>
                    <th class="text-right text-[11px] text-muted uppercase tracking-wider font-semibold px-5 py-3"><?= __('revenue') ?></th>
                    <th class="text-right text-[11px] text-muted uppercase tracking-wider font-semibold px-5 py-3"><?= __('profit') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                <tr>
                    <td colspan="4" class="px-5 py-12 text-center text-muted text-sm"><?= __('no_orders') ?></td>
                </tr>
                <?php else: ?>
                <?php foreach ($rows as $i => $row): ?>
                <tr class="border-t border-white/5 <?= $i % 2 === 0 ? 'bg-white/[0.01]' : '' ?>">
                    <td class="px-5 py-3 text-white/80"><?= e($row['date']) ?></td>
                    <td class="px-5 py-3 text-right text-white/70"><?= (int)$row['orders'] ?></td>
                    <td class="px-5 py-3 text-right text-gold font-medium">Rp <?= number_format((float)$row['revenue'], 0, ',', '.') ?></td>
                    <td class="px-5 py-3 text-right font-medium <?= (float)$row['profit'] > 0 ? 'text-emerald-400' : 'text-red-400' ?>">Rp <?= number_format((float)$row['profit'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
                <?php $__totalProfit = (float)($totals['profit'] ?? 0); ?>
                <tr class="border-t-2 border-gold/20 bg-gold/[0.02]">
                    <td class="px-5 py-3 text-sm font-semibold text-white"><?= __('total') ?></td>
                    <td class="px-5 py-3 text-right font-semibold text-white"><?= (int)($totals['orders'] ?? 0) ?></td>
                    <td class="px-5 py-3 text-right font-semibold text-gold">Rp <?= number_format((float)($totals['revenue'] ?? 0), 0, ',', '.') ?></td>
                    <td class="px-5 py-3 text-right font-semibold <?= $__totalProfit > 0 ? 'text-emerald-400' : 'text-red-400' ?>">Rp <?= number_format($__totalProfit, 0, ',', '.') ?></td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
