<div class="mb-6">
    <div class="flex items-center justify-between mb-1">
        <h1 class="text-2xl font-bold font-display text-white"><?= __('revenue_report') ?></h1>
        <a href="/reports/revenue/export?date_from=<?= e($dateFrom) ?>&date_to=<?= e($dateTo) ?>"
            class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-primary text-white border-primary hover:bg-primary-hover hover:border-primary-hover hover:shadow-[0_0_15px_var(--color-gold-glow)] hover:text-white">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
            <?= __('export_csv') ?></a>
    </div>
</div>

<?php require __DIR__ . '/../partials/table-date-filter.php' ?>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-[#111113] border border-white/5 rounded-xl p-4">
        <div class="text-[11px] text-muted uppercase tracking-wider font-semibold mb-1"><?= __('revenue') ?></div>
        <div class="text-xl font-bold font-display text-white">Rp
            <?= number_format((float) ($totals['revenue'] ?? 0), 0, ',', '.') ?>
        </div>
    </div>
    <div class="bg-[#111113] border border-white/5 rounded-xl p-4">
        <div class="text-[11px] text-muted uppercase tracking-wider font-semibold mb-1"><?= __('profit') ?></div>
        <div
            class="text-xl font-bold font-display <?= ($totals['profit'] ?? 0) > 0 ? 'text-emerald-400' : 'text-red-400' ?>">
            Rp <?= number_format((float) ($totals['profit'] ?? 0), 0, ',', '.') ?></div>
    </div>
    <div class="bg-[#111113] border border-white/5 rounded-xl p-4">
        <div class="text-[11px] text-muted uppercase tracking-wider font-semibold mb-1"><?= __('total_orders') ?></div>
        <div class="text-xl font-bold font-display text-white"><?= (int) ($totals['orders'] ?? 0) ?></div>
    </div>
</div>

<?php require __DIR__ . '/../partials/table-sort.php' ?>
<div id="table-container" class="bg-[#18181b] border border-border rounded-xl overflow-hidden">
    <?php if (empty($rows)): ?>
    <div class="col-span-full bg-card-bg border border-dashed border-border rounded-[20px] px-6 py-12 text-center text-muted">
        <p><?= __('no_orders') ?></p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr>
                    <th class="bg-black/30 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border">
                        <a href="<?= $sortUrl('date') ?>" class="flex items-center gap-1 px-4 py-3 group text-muted hover:text-gold transition-colors no-underline"><?= __('date') ?><?= $sortIcon('date') ?></a>
                    </th>
                    <th class="bg-black/30 text-right text-xs font-semibold uppercase tracking-wider text-muted border-b border-border">
                        <a href="<?= $sortUrl('orders') ?>" class="flex items-center gap-1 px-4 py-3 group justify-end text-muted hover:text-gold transition-colors no-underline"><?= __('orders') ?><?= $sortIcon('orders') ?></a>
                    </th>
                    <th class="bg-black/30 text-right text-xs font-semibold uppercase tracking-wider text-muted border-b border-border">
                        <a href="<?= $sortUrl('revenue') ?>" class="flex items-center gap-1 px-4 py-3 group justify-end text-muted hover:text-gold transition-colors no-underline"><?= __('revenue') ?><?= $sortIcon('revenue') ?></a>
                    </th>
                    <th class="bg-black/30 text-right text-xs font-semibold uppercase tracking-wider text-muted border-b border-border">
                        <a href="<?= $sortUrl('profit') ?>" class="flex items-center gap-1 px-4 py-3 group justify-end text-muted hover:text-gold transition-colors no-underline"><?= __('profit') ?><?= $sortIcon('profit') ?></a>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $i => $row): ?>
                    <tr class="hover:bg-white/[0.03]">
                        <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-white/80"><?= e($row['date']) ?></td>
                        <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-right text-white/70"><?= (int) $row['orders'] ?></td>
                        <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-right text-gold font-medium">Rp <?= number_format((float) $row['revenue'], 0, ',', '.') ?></td>
                        <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-right font-medium <?= (float) $row['profit'] > 0 ? 'text-emerald-400' : 'text-red-400' ?>">Rp <?= number_format((float) $row['profit'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php $__totalProfit = (float) ($totals['profit'] ?? 0); ?>
                <tr class="border-t-2 border-gold/20 bg-gold/[0.02]">
                    <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle font-semibold text-white"><?= __('total') ?></td>
                    <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-right font-semibold text-white"><?= (int) ($totals['orders'] ?? 0) ?></td>
                    <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-right font-semibold text-gold">Rp <?= number_format((float) ($totals['revenue'] ?? 0), 0, ',', '.') ?></td>
                    <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-right font-semibold <?= $__totalProfit > 0 ? 'text-emerald-400' : 'text-red-400' ?>">Rp <?= number_format($__totalProfit, 0, ',', '.') ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php $totalLabel = __('rows'); require __DIR__ . '/../partials/table-pagination.php' ?>
    <?php endif; ?>
</div>
