<div class="max-w-[1040px] mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-10 flex-wrap gap-3">
        <a href="/orders" onclick="history.back();return false"
            class="inline-flex items-center gap-2 px-0 text-sm text-muted no-underline hover:text-gold transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24"
                stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            <?= __('back_to_orders') ?>
        </a>
        <div class="flex items-center gap-3">
            <a href="/orders/<?= e($order['order_number']) ?>/receipt" target="_blank"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-full text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-white/5 border-border text-text backdrop-blur-[8px] hover:bg-primary hover:border-primary hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                </svg>
                <?= __('print') ?>
            </a>
            <button type="button" onclick="showEditOrderModal()"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-full text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-white/5 border-border text-text backdrop-blur-[8px] hover:bg-gold hover:border-gold hover:shadow-[0_0_15px_var(--color-gold-glow)] hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                </svg>
                <?= __('update') ?>
            </button>
        </div>
    </div>

    <!-- Hero: Gradient panel + floating info -->
    <div class="relative mb-10">
        <div
            class="h-[200px] max-md:h-[160px] rounded-2xl bg-gradient-to-br from-gold/15 via-gold/5 to-accent-red/5 flex items-center justify-center overflow-hidden relative">
            <div class="absolute inset-0 opacity-[0.06]">
                <svg class="w-full h-full" viewBox="0 0 1040 200" preserveAspectRatio="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 120 Q 260 40, 520 100 T 1040 80 L 1040 200 L 0 200 Z" fill="currentColor" />
                </svg>
            </div>
        </div>

        <!-- Floating info panel -->
        <div class="absolute -bottom-8 left-6 right-6 max-md:static max-md:mt-6 max-md:px-0">
            <div
                class="bg-[#18181b]/90 backdrop-blur-[20px] border border-white/10 rounded-xl px-6 py-5 flex items-center justify-between flex-wrap gap-4 shadow-xl">
                <div class="flex items-center gap-4 flex-wrap">
                    <div>
                        <div class="inline-flex items-center gap-2.5 mb-2">
                            <span
                                class="order-status-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-[0.7rem] font-semibold uppercase tracking-widest"
                                style="background:<?php
                                $sc = ['pending' => 'rgba(245,158,11,0.12)', 'processing' => 'rgba(79,70,229,0.12)', 'delivering' => 'rgba(79,70,229,0.12)', 'completed' => 'rgba(16,185,129,0.12)', 'cancelled' => 'rgba(239,68,68,0.12)'];
                                echo $sc[$order['status']] ?? 'rgba(161,161,170,0.12)';
                                ?>;color:<?php
                                $st = ['pending' => '#f59e0b', 'processing' => '#818cf8', 'delivering' => '#818cf8', 'completed' => '#10b981', 'cancelled' => '#ef4444'];
                                echo $st[$order['status']] ?? '#a1a1aa';
                                ?>" data-status="<?= e($order['status']) ?>">
                                <?= e(__($order['status'])) ?>
                            </span>
                            <span
                                class="payment-status-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-[0.7rem] font-semibold uppercase tracking-widest"
                                style="background:<?php
                                $pc = ['unpaid' => 'rgba(245,158,11,0.12)', 'paid' => 'rgba(16,185,129,0.12)', 'refunded' => 'rgba(239,68,68,0.12)'];
                                echo $pc[$order['payment_status']] ?? 'rgba(161,161,170,0.12)';
                                ?>;color:<?php
                                $pt = ['unpaid' => '#f59e0b', 'paid' => '#10b981', 'refunded' => '#ef4444'];
                                echo $pt[$order['payment_status']] ?? '#a1a1aa';
                                ?>" data-payment="<?= e($order['payment_status']) ?>">
                                <?= e(__($order['payment_status'])) ?>
                            </span>
                            <span class="text-[0.7rem] text-muted uppercase tracking-widest"><?= __('order') ?></span>
                        </div>
                        <h1 class="text-2xl md:text-3xl font-bold font-display text-text leading-tight">
                            <?= __('order') ?> <span class="text-gold"><?= e($order['order_number']) ?></span>
                        </h1>
                        <?php if (!empty($order['invoice_number'])): ?>
                            <div class="text-xs text-muted mt-1"><?= __('invoice') ?>: <?= e($order['invoice_number']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <div class="text-xs text-muted uppercase tracking-wider font-medium mb-1"><?= __('total') ?></div>
                    <div class="font-display text-2xl md:text-3xl font-bold text-gold" id="order-total">Rp
                        <?= number_format((float) $order['total_price'], 0, ',', '.') ?>
                    </div>
                    <?php if ((float) ($order['total_cost'] ?? 0) > 0): ?>
                        <div class="text-xs text-muted mt-1"><?= __('cost_price') ?>: Rp
                            <?= number_format((float) $order['total_cost'], 0, ',', '.') ?>
                        </div>
                        <div
                            class="text-xs <?= ((float) $order['total_price'] - (float) $order['total_cost']) > 0 ? 'text-success' : 'text-danger' ?>">
                            <?= __('profit') ?>: Rp
                            <?= number_format((float) $order['total_price'] - (float) $order['total_cost'], 0, ',', '.') ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Spacer for floating panel (desktop) -->
    <div class="h-8 max-md:hidden"></div>

    <!-- Order stats badges -->
    <div class="flex flex-wrap items-center gap-2.5 mb-10">
        <span
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/[0.04] border border-white/5 text-xs text-muted font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24"
                stroke-width="1.5" stroke="currentColor" class="text-gold">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
            </svg>
            <?= e($customer['name'] ?? __('unknown_customer')) ?>
        </span>
        <span
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/[0.04] border border-white/5 text-xs text-muted font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24"
                stroke-width="1.5" stroke="currentColor" class="text-gold">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
            </svg>
            <?= e(date('d M Y', strtotime($order['event_date'])) . (!empty($order['event_time']) ? ' — ' . date('H:i', strtotime($order['event_time'])) : '')) ?>
        </span>
        <span
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/[0.04] border border-white/5 text-xs text-muted font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24"
                stroke-width="1.5" stroke="currentColor" class="text-gold">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <?= __('ordered_on') ?> <?= date('d M Y', strtotime($order['created_at'])) ?>
        </span>
    </div>

    <!-- Two-column: Timeline + Details -->
    <div class="grid grid-cols-1 md:grid-cols-[1fr_1.6fr] gap-8 mb-10">
        <!-- Left: Status Timeline -->
        <div>
            <h2 class="text-sm font-semibold text-muted uppercase tracking-widest mb-5 flex items-center gap-3">
                <span class="w-6 h-px bg-gold/50"></span>
                <?= __('order_progress') ?>
            </h2>

            <div class="flex flex-col gap-5" id="status-timeline">
                <?php
                $steps = [
                    'pending' => ['label' => __('order_received'), 'desc' => __('being_reviewed')],
                    'processing' => ['label' => __('processing'), 'desc' => __('preparing_order')],
                    'delivering' => ['label' => __('delivering'), 'desc' => __('on_its_way')],
                    'completed' => ['label' => __('completed'), 'desc' => __('order_arrived')],
                ];
                $orderStatus = $order['status'];
                foreach ($steps as $key => $step):
                    $isActive = in_array($orderStatus, ['pending', 'processing', 'delivering', 'completed']) && (
                        ($key === 'pending') ||
                        ($key === 'processing' && in_array($orderStatus, ['processing', 'delivering', 'completed'])) ||
                        ($key === 'delivering' && in_array($orderStatus, ['delivering', 'completed'])) ||
                        ($key === 'completed' && $orderStatus === 'completed')
                    );
                    if ($orderStatus === 'cancelled')
                        $isActive = $key === 'pending';
                    ?>
                    <div class="flex gap-4 items-start status-step" data-step="<?= e($key) ?>">
                        <div class="flex flex-col items-center">
                            <div
                                class="w-3.5 h-3.5 rounded-full border-2 transition-all duration-300 status-dot <?= $isActive ? 'border-gold bg-gold shadow-[0_0_8px_var(--color-gold-glow)]' : 'border-white/20 bg-transparent' ?>">
                            </div>
                            <?php if ($key !== 'completed'): ?>
                                <div
                                    class="w-px flex-1 min-h-[28px] <?= $isActive && $orderStatus !== $key ? 'bg-gradient-to-b from-gold/50 to-white/5' : 'bg-white/5' ?>">
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="pb-1">
                            <div
                                class="font-semibold text-sm transition-colors duration-300 <?= $isActive ? 'text-text' : 'text-muted/60' ?>">
                                <?= e($step['label']) ?>
                            </div>
                            <div class="text-xs text-muted/50"><?= e($step['desc']) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if ($orderStatus === 'cancelled'): ?>
                    <div class="flex gap-4 items-start" data-step="cancelled">
                        <div class="flex flex-col items-center">
                            <div
                                class="w-3.5 h-3.5 rounded-full border-2 border-red-500 bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.3)]">
                            </div>
                        </div>
                        <div>
                            <div class="font-semibold text-sm text-[#ef4444]"><?= __('cancelled') ?></div>
                            <div class="text-xs text-muted/50"><?= __('order_cancelled') ?></div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right: Customer + Items -->
        <div class="flex flex-col gap-6">
            <!-- Customer details -->
            <div>
                <h2 class="text-sm font-semibold text-muted uppercase tracking-widest mb-5 flex items-center gap-3">
                    <span class="w-6 h-px bg-gold/50"></span>
                    <?= __('customer') ?>
                </h2>
                <div class="bg-white/[0.03] border border-white/5 rounded-xl p-5">
                    <div class="grid grid-cols-[110px_1fr] max-md:grid-cols-1 gap-y-3 gap-x-3 text-sm">
                        <div class="text-muted"><?= __('name') ?></div>
                        <div class="text-text font-medium"><?= e($customer['name'] ?? '-') ?></div>
                        <div class="text-muted"><?= __('phone') ?></div>
                        <div class="text-text">
                            <?php if ($customer['phone'] ?? null): ?>
                                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $customer['phone']) ?>"
                                    target="_blank"
                                    class="text-gold hover:text-gold no-underline inline-flex items-center gap-1.5">
                                    <?= e($customer['phone']) ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                    </svg>
                                </a>
                            <?php else: ?><span class="text-muted">-</span><?php endif; ?>
                        </div>
                        <div class="text-muted"><?= __('event_date') ?></div>
                        <div class="text-text"><?= e(date('d F Y', strtotime($order['event_date'])) . (!empty($order['event_time']) ? ', ' . date('H:i', strtotime($order['event_time'])) : '')) ?></div>
                        <?php if ($order['occasion']): ?>
                            <div class="text-muted"><?= __('occasion') ?></div>
                            <div class="text-text">
                                <?php $occKey = 'occasion_' . $order['occasion'];
                                $occLabel = __($occKey);
                                echo \App\Core\View::e($occLabel !== $occKey ? $occLabel : $order['occasion']); ?>
                            </div>
                        <?php endif; ?>
                        <div class="text-muted"><?= __('address') ?></div>
                        <div class="text-text"><?= nl2br(e($order['delivery_address'])) ?></div>
                        <?php if (!empty($order['payment_method'])): ?>
                            <div class="text-muted"><?= __('payment_method') ?></div>
                            <div class="text-text"><?= e(__($order['payment_method'])) ?></div>
                        <?php endif; ?>
                        <?php if (!empty($order['paid_at'])): ?>
                            <div class="text-muted"><?= __('paid_at') ?></div>
                            <div class="text-text"><?= date('d M Y H:i', strtotime($order['paid_at'])) ?></div>
                        <?php endif; ?>
                        <?php if ($order['notes']): ?>
                            <div class="text-muted"><?= __('notes') ?></div>
                            <div class="text-text text-muted italic"><?= nl2br(e($order['notes'])) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Menu Items -->
            <div>
                <h2 class="text-sm font-semibold text-muted uppercase tracking-widest mb-5 flex items-center gap-3">
                    <span class="w-6 h-px bg-gold/50"></span>
                    <?= __('menu_items') ?>
                </h2>
                <div class="bg-white/[0.03] border border-white/5 rounded-xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr>
                                    <th
                                        class="text-left text-xs font-semibold uppercase tracking-wider text-muted/60 px-5 py-3 border-b border-white/5 bg-white/[0.02]">
                                        <?= __('menu') ?>
                                    </th>
                                    <th
                                        class="text-right text-xs font-semibold uppercase tracking-wider text-muted/60 px-5 py-3 border-b border-white/5 bg-white/[0.02]">
                                        <?= __('qty') ?>
                                    </th>
                                    <th
                                        class="text-right text-xs font-semibold uppercase tracking-wider text-muted/60 px-5 py-3 border-b border-white/5 bg-white/[0.02]">
                                        <?= __('price') ?>
                                    </th>
                                    <th
                                        class="text-right text-xs font-semibold uppercase tracking-wider text-muted/60 px-5 py-3 border-b border-white/5 bg-white/[0.02]">
                                        <?= __('subtotal') ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $total = 0;
                                foreach ($items as $item):
                                    $total += (float) $item['subtotal']; ?>
                                    <tr
                                        class="border-b border-white/[0.03] hover:bg-white/[0.02] transition-all duration-150">
                                        <td class="px-5 py-3.5 text-sm text-text"><?= e($item['menu_name']) ?></td>
                                        <td class="px-5 py-3.5 text-sm text-text text-right"><?= (int) $item['quantity'] ?>x
                                        </td>
                                        <td class="px-5 py-3.5 text-sm text-text text-right">Rp
                                            <?= number_format((float) $item['price_at_time'], 0, ',', '.') ?>
                                        </td>
                                        <td class="px-5 py-3.5 text-sm text-text text-right font-medium">Rp
                                            <?= number_format((float) $item['subtotal'], 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="px-5 py-3.5 text-sm font-semibold text-text text-right">
                                        <?= __('subtotal') ?>
                                    </td>
                                    <td class="px-5 py-3.5 text-sm font-bold text-gold text-right" id="items-total">Rp
                                        <?= number_format($total, 0, ',', '.') ?>
                                    </td>
                                </tr>
                                <?php if ((float) ($order['discount_amount'] ?? 0) > 0): ?>
                                    <tr>
                                        <td colspan="3" class="px-5 py-2 text-sm text-muted text-right">
                                            <?= __('discount') ?>
                                            <?php if ($order['discount_type'] === 'percentage'): ?>(<?= (float) $order['discount_value'] ?>%)<?php endif; ?>
                                        </td>
                                        <td class="px-5 py-2 text-sm text-danger text-right">- Rp
                                            <?= number_format((float) $order['discount_amount'], 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ((float) ($order['tax_amount'] ?? 0) > 0): ?>
                                    <tr>
                                        <td colspan="3" class="px-5 py-2 text-sm text-muted text-right"><?= __('tax') ?>
                                            (<?= (float) $order['tax_rate'] ?>%)</td>
                                        <td class="px-5 py-2 text-sm text-text text-right">Rp
                                            <?= number_format((float) $order['tax_amount'], 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <tr>
                                    <td colspan="3" class="px-5 py-3.5 text-sm font-semibold text-text text-right">
                                        <?= __('grand_total') ?>
                                    </td>
                                    <td class="px-5 py-3.5 text-sm font-bold text-gold text-right"
                                        id="order-grand-total">Rp
                                        <?= number_format((float) ($order['grand_total'] ?? $total), 0, ',', '.') ?>
                                    </td>
                                </tr>
                                <?php if ((float) ($order['down_payment'] ?? 0) > 0): ?>
                                    <tr>
                                        <td colspan="3" class="px-5 py-2 text-sm text-muted text-right">
                                            <?= __('down_payment') ?>
                                        </td>
                                        <td class="px-5 py-2 text-sm text-warning text-right">- Rp
                                            <?= number_format((float) $order['down_payment'], 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="px-5 py-2 text-sm font-semibold text-text text-right">
                                            <?= __('remaining_balance') ?>
                                        </td>
                                        <td
                                            class="px-5 py-2 text-sm font-bold <?= (float) $order['remaining_balance'] > 0 ? 'text-warning' : 'text-success' ?> text-right">
                                            Rp <?= number_format((float) $order['remaining_balance'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endif; ?>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$occKey = 'occasion_' . $order['occasion'];
$occLabel = __($occKey);
$isPredefined = $occLabel !== $occKey;
$selOccasion = $isPredefined ? $order['occasion'] : '__other__';
$customOccasion = $isPredefined ? '' : $order['occasion'];
?>
<div id="editOrderModal" class="hidden fixed inset-0 z-50 overflow-y-auto"
    style="background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);-webkit-backdrop-filter:blur(4px)">
    <div class="flex min-h-full items-center justify-center p-4" style="pointer-events:none">
        <div class="bg-[#18181b] border border-white/10 rounded-2xl shadow-2xl w-full max-w-[700px] mx-auto max-h-[90vh] flex flex-col overflow-hidden"
            style="pointer-events:auto;transform:scale(0.95) translateY(10px);opacity:0;transition:transform 200ms cubic-bezier(0.16,1,0.3,1),opacity 200ms ease-out">
            <div class="flex items-center justify-between p-6 border-b border-white/10 shrink-0">
                <h3 class="text-lg font-bold font-display text-white"><?= __('edit_order') ?></h3>
                <button type="button" onclick="closeEditOrderModal()"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-muted hover:text-text hover:bg-white/5 transition-all duration-150 cursor-pointer border-0 bg-transparent">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="editOrderModal-form" method="POST" action="/orders/<?= e($order['order_number']) ?>"
                class="flex flex-col flex-1 min-h-0" data-order-number="<?= e($order['order_number']) ?>">
                <?= csrf_field() ?>
                <div id="editOrderModal-errors"
                    class="hidden p-4 mx-6 mt-4 rounded-lg bg-danger/10 border border-danger/30 text-danger text-sm shrink-0">
                </div>
                <div class="p-6 space-y-4 overflow-y-auto flex-1 min-h-0">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-text mb-1.5"><?= __('customer_name') ?> <span
                                    class="text-danger">*</span></label>
                            <?php if ($canEditCustomerName): ?>
                                <input type="text" name="customer_name" value="<?= e($customer['name'] ?? '') ?>" required
                                    class="w-full px-3 py-3 border border-border rounded-lg text-sm text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light">
                            <?php else: ?>
                                <div class="relative">
                                    <input type="text" name="customer_name" value="<?= e($customer['name'] ?? '') ?>"
                                        readonly required
                                        class="w-full px-3 py-3 border border-border rounded-lg text-sm text-text bg-black/40 font-body opacity-60 cursor-not-allowed focus:outline-none">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none"
                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                        </svg>
                                    </span>
                                </div>
                                <p class="text-xs text-muted mt-1"><?= __('customer_name_locked') ?></p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-text mb-1.5"><?= __('phone') ?></label>
                            <input type="text" readonly value="<?= e($customer['phone'] ?? '') ?>"
                                class="w-full px-3 py-3 border border-border rounded-lg text-sm text-text bg-black/40 font-body opacity-60 cursor-not-allowed">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text mb-1.5"><?= __('delivery_address') ?> <span
                                class="text-danger">*</span></label>
                        <textarea name="delivery_address" required
                            class="w-full px-3 py-3 border border-border rounded-lg text-sm text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light min-h-[80px] resize-vertical"><?= e($order['delivery_address']) ?></textarea>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-text mb-1.5"><?= __('event_date') ?> <span
                                    class="text-danger">*</span></label>
                            <input type="date" name="event_date" value="<?= e($order['event_date']) ?>"
                                min="<?= date('Y-m-d') ?>" required
                                class="w-full px-3 py-3 border border-border rounded-lg text-sm text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-text mb-1.5"><?= __('event_time') ?></label>
                            <input type="time" name="event_time" value="<?= e(!empty($order['event_time']) ? substr($order['event_time'], 0, 5) : '') ?>"
                                class="w-full px-3 py-3 border border-border rounded-lg text-sm text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light">
                        </div>
                        <div class="relative">
                            <label class="block text-sm font-medium text-text mb-1.5"><?= __('occasion') ?> <span
                                    class="text-danger">*</span></label>
                            <select id="edit-occasion-select" name="occasion" onchange="toggleOccasionEdit(this)"
                                class="w-full px-3 py-3 border border-border rounded-lg text-sm text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light">
                                <option value=""><?= __('occasion_placeholder') ?></option>
                                <option value="birthday" <?= $selOccasion === 'birthday' ? 'selected' : '' ?>>
                                    <?= __('occasion_birthday') ?>
                                </option>
                                <option value="wedding" <?= $selOccasion === 'wedding' ? 'selected' : '' ?>>
                                    <?= __('occasion_wedding') ?>
                                </option>
                                <option value="corporate" <?= $selOccasion === 'corporate' ? 'selected' : '' ?>>
                                    <?= __('occasion_corporate') ?>
                                </option>
                                <option value="family" <?= $selOccasion === 'family' ? 'selected' : '' ?>>
                                    <?= __('occasion_family') ?>
                                </option>
                                <option value="arisan" <?= $selOccasion === 'arisan' ? 'selected' : '' ?>>
                                    <?= __('occasion_arisan') ?>
                                </option>
                                <option value="khitanan" <?= $selOccasion === 'khitanan' ? 'selected' : '' ?>>
                                    <?= __('occasion_khitanan') ?>
                                </option>
                                <option value="__other__" <?= $selOccasion === '__other__' ? 'selected' : '' ?>>
                                    <?= __('occasion_other') ?>
                                </option>
                            </select>
                            <input type="text" id="edit-occasion-custom" value="<?= e($customOccasion) ?>"
                                placeholder="<?= __('occasion_custom_placeholder') ?>"
                                class="w-full px-3 py-3 border border-border rounded-lg text-sm text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light mt-2"
                                style="display:<?= $selOccasion === '__other__' ? '' : 'none' ?>">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-text mb-1.5"><?= __('status') ?> <span
                                    class="text-danger">*</span></label>
                            <select name="status"
                                class="w-full px-3 py-3 border border-border rounded-lg text-sm text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light">
                                <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>
                                    <?= __('pending') ?>
                                </option>
                                <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>
                                    <?= __('processing') ?>
                                </option>
                                <option value="delivering" <?= $order['status'] === 'delivering' ? 'selected' : '' ?>>
                                    <?= __('delivering') ?>
                                </option>
                                <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>
                                    <?= __('completed') ?>
                                </option>
                                <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>
                                    <?= __('cancelled') ?>
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-text mb-1.5"><?= __('payment') ?> <span
                                    class="text-danger">*</span></label>
                            <select name="payment_status"
                                class="w-full px-3 py-3 border border-border rounded-lg text-sm text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light">
                                <option value="unpaid" <?= $order['payment_status'] === 'unpaid' ? 'selected' : '' ?>>
                                    <?= __('unpaid') ?>
                                </option>
                                <option value="paid" <?= $order['payment_status'] === 'paid' ? 'selected' : '' ?>>
                                    <?= __('paid') ?>
                                </option>
                                <option value="refunded" <?= $order['payment_status'] === 'refunded' ? 'selected' : '' ?>>
                                    <?= __('refunded') ?>
                                </option>
                            </select>
                        </div>
                    </div>
                    <hr class="border-white/5 my-2">
                    <h4 class="text-sm font-semibold text-muted uppercase tracking-wider mb-3"><?= __('menu_items') ?>
                    </h4>
                    <div id="edit-menu-items-container" class="flex flex-col gap-3">
                        <?php foreach ($items as $idx => $item): ?>
                            <div class="edit-menu-item-row flex items-start gap-2" data-index="<?= $idx ?>">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-text mb-1.5"><?= __('menu') ?> <span
                                            class="text-danger">*</span></label>
                                    <select name="items[<?= $idx ?>][menu_id]" required
                                        class="w-full px-3 py-3 border border-border rounded-lg text-sm text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light">
                                        <option value="">-- <?= __('select_menu') ?> --</option>
                                        <?php foreach ($menus ?? [] as $m): ?>
                                            <option value="<?= (int) $m['id'] ?>" <?= (int) $m['id'] === (int) $item['menu_id'] ? 'selected' : '' ?>><?= e($m['name']) ?> (Rp
                                                <?= number_format((float) $m['price'], 0, ',', '.') ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="w-28 shrink-0">
                                    <label class="block text-sm font-medium text-text mb-1.5"><?= __('qty') ?> <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="items[<?= $idx ?>][quantity]"
                                        value="<?= (int) $item['quantity'] ?>" min="1" required
                                        class="w-full px-3 py-3 border border-border rounded-lg text-sm text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light">
                                </div>
                                <button type="button"
                                    class="remove-edit-menu-item mt-6 w-9 h-9 flex items-center justify-center rounded-lg text-muted hover:text-danger hover:bg-danger/10 transition-all duration-150 cursor-pointer border-0 bg-transparent shrink-0 <?= count($items) <= 1 ? 'hidden' : '' ?>"
                                    data-index="<?= $idx ?>" title="<?= __('remove') ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" id="add-edit-menu-item"
                        class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-[0.8125rem] rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-white/6 text-text border-border hover:bg-white/10 hover:text-text mt-2"><?= __('add_another_menu') ?></button>
                    <hr class="border-white/5 my-2">
                    <h4 class="text-sm font-semibold text-muted uppercase tracking-wider mb-3"><?= __('billing') ?></h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-text mb-1.5"><?= __('discount_type') ?></label>
                            <select name="discount_type"
                                class="w-full px-3 py-3 border border-border rounded-lg text-sm text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light">
                                <option value="none" <?= ($order['discount_type'] ?? 'none') === 'none' ? 'selected' : '' ?>><?= __('none') ?></option>
                                <option value="percentage" <?= ($order['discount_type'] ?? '') === 'percentage' ? 'selected' : '' ?>><?= __('discount_percentage') ?></option>
                                <option value="fixed" <?= ($order['discount_type'] ?? '') === 'fixed' ? 'selected' : '' ?>>
                                    <?= __('discount_fixed') ?>
                                </option>
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-text mb-1.5"><?= __('discount_value') ?></label>
                            <input type="number" name="discount_value"
                                value="<?= e((string) ($order['discount_value'] ?? '0')) ?>" min="0" step="0.01"
                                class="w-full px-3 py-3 border border-border rounded-lg text-sm text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-text mb-1.5"><?= __('tax_rate') ?> (%)</label>
                            <input type="number" name="tax_rate" value="<?= e((string) ($order['tax_rate'] ?? '0')) ?>"
                                min="0" max="100" step="0.01"
                                class="w-full px-3 py-3 border border-border rounded-lg text-sm text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-text mb-1.5"><?= __('payment_method') ?></label>
                            <select name="payment_method"
                                class="w-full px-3 py-3 border border-border rounded-lg text-sm text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light">
                                <option value="cash" <?= ($order['payment_method'] ?? 'cash') === 'cash' ? 'selected' : '' ?>><?= __('cash') ?></option>
                                <option value="transfer" <?= ($order['payment_method'] ?? '') === 'transfer' ? 'selected' : '' ?>><?= __('transfer') ?></option>
                                <option value="qris" <?= ($order['payment_method'] ?? '') === 'qris' ? 'selected' : '' ?>>
                                    <?= __('qris') ?>
                                </option>
                                <option value="other" <?= ($order['payment_method'] ?? '') === 'other' ? 'selected' : '' ?>><?= __('other') ?></option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-text mb-1.5"><?= __('down_payment') ?></label>
                            <input type="number" name="down_payment"
                                value="<?= e((string) ($order['down_payment'] ?? '0')) ?>" min="0" step="0.01"
                                class="w-full px-3 py-3 border border-border rounded-lg text-sm text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-text mb-1.5"><?= __('dp_due_date') ?></label>
                        <input type="date" name="down_payment_due"
                            value="<?= !empty($order['down_payment_due']) ? e($order['down_payment_due']) : '' ?>"
                            class="w-full px-3 py-3 border border-border rounded-lg text-sm text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light">
                    </div>
                    <hr class="border-white/5 my-2">
                    <div>
                        <label class="block text-sm font-medium text-text mb-1.5"><?= __('notes') ?></label>
                        <textarea name="notes"
                            class="w-full px-3 py-3 border border-border rounded-lg text-sm text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light min-h-[60px] resize-vertical"><?= e($order['notes'] ?? '') ?></textarea>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 p-6 border-t border-white/10 shrink-0">
                    <button type="button" onclick="closeEditOrderModal()"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body bg-white/6 text-text border-border hover:bg-white/10 hover:text-text"><?= __('cancel') ?></button>
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-primary text-white border-primary hover:bg-primary-hover hover:border-primary-hover hover:shadow-[0_0_15px_var(--color-gold-glow)] hover:text-white"><?= __('save_changes') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleOccasionEdit(sel) {
        var custom = document.getElementById('edit-occasion-custom');
        if (!custom) return;
        if (sel.value === '__other__') {
            custom.style.display = '';
            custom.name = 'occasion';
            sel.name = '';
            custom.focus();
        } else {
            custom.style.display = 'none';
            custom.name = '';
            custom.value = '';
            sel.name = 'occasion';
        }
    }

    function showEditOrderModal() {
        var el = document.getElementById('editOrderModal');
        if (!el) return;
        el.classList.remove('hidden');
        var card = el.firstElementChild.firstElementChild;
        void card.offsetHeight;
        card.style.transform = 'scale(1) translateY(0)';
        card.style.opacity = '1';
        document.body.style.overflow = 'hidden';
        // Init occasion toggle
        var occSel = document.getElementById('edit-occasion-select');
        if (occSel) toggleOccasionEdit(occSel);
    }

    function closeEditOrderModal() {
        var el = document.getElementById('editOrderModal');
        if (!el) return;
        var card = el.firstElementChild.firstElementChild;
        if (card) { card.style.transform = 'scale(0.95) translateY(10px)'; card.style.opacity = '0'; }
        setTimeout(function () { el.classList.add('hidden'); }, 150);
        document.body.style.overflow = '';
    }

    (function () {
        var form = document.getElementById('editOrderModal-form');
        if (!form) return;

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (form.dataset.submitting) return;
            form.dataset.submitting = '1';

            var btn = form.querySelector('button[type="submit"]');
            var origText = btn ? btn.textContent : '';
            if (btn) { btn.disabled = true; btn.innerHTML = '<span class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin inline-block"></span> <?= __('saving') ?>'; }

            var errorsEl = document.getElementById('editOrderModal-errors');

            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(function (r) { return r.json(); })
                .then(function (resp) {
                    if (resp.success) {
                        closeEditOrderModal();
                        if (window.AppModules && window.AppModules.toast) {
                            window.AppModules.toast.show(resp.message || '<?= __('order_updated') ?>', 'success');
                        }
                        window.location.reload();
                    } else {
                        if (errorsEl) {
                            errorsEl.textContent = '';
                            if (resp.message) {
                                var p = document.createElement('p');
                                p.className = 'mb-2 font-medium';
                                p.textContent = resp.message;
                                errorsEl.appendChild(p);
                            }
                            if (resp.errors) {
                                var ul = document.createElement('ul');
                                ul.className = 'list-disc pl-4 space-y-0.5';
                                for (var key in resp.errors) {
                                    if (resp.errors.hasOwnProperty(key)) {
                                        var li = document.createElement('li');
                                        li.textContent = resp.errors[key];
                                        ul.appendChild(li);
                                    }
                                }
                                errorsEl.appendChild(ul);
                            }
                            errorsEl.classList.remove('hidden');
                        }
                        if (btn) { btn.disabled = false; btn.textContent = origText; }
                    }
                })
                .catch(function (err) {
                    if (errorsEl) {
                        errorsEl.textContent = '';
                        var p = document.createElement('p');
                        p.textContent = '<?= __('network_error') ?>';
                        errorsEl.appendChild(p);
                        errorsEl.classList.remove('hidden');
                    }
                    if (btn) { btn.disabled = false; btn.textContent = origText; }
                })
                .finally(function () {
                    delete form.dataset.submitting;
                });
        });

        // Close via overlay click
        el = document.getElementById('editOrderModal');
        if (el) {
            el.addEventListener('click', function (e) {
                if (e.target === el) closeEditOrderModal();
            });
        }
    })();

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            var el = document.getElementById('editOrderModal');
            if (el && !el.classList.contains('hidden')) closeEditOrderModal();
        }
    });

    // Edit modal menu items management
    (function () {
        var container = document.getElementById('edit-menu-items-container');
        var addBtn = document.getElementById('add-edit-menu-item');
        if (!container || !addBtn) return;

        var menuData = <?= json_encode(array_map(function ($m) {
            return ['id' => $m['id'], 'name' => $m['name'], 'price' => $m['price'], 'minimum_portions' => (int) $m['minimum_portions']];
        }, $menus ?? []), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

        function buildSelect(idx) {
            var s = '<select name="items[' + idx + '][menu_id]" required class="w-full px-3 py-3 border border-border rounded-lg text-sm text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light">';
            s += '<option value="">-- <?= e(__('select_menu')) ?> --</option>';
            for (var i = 0; i < menuData.length; i++) {
                s += '<option value="' + menuData[i].id + '">' + menuData[i].name.replace(/&/g, '&amp;').replace(/</g, '&lt;') + ' (Rp ' + Number(menuData[i].price).toLocaleString('id-ID') + ')</option>';
            }
            s += '</select>';
            return s;
        }

        function updateEditIndices() {
            var rows = container.querySelectorAll('.edit-menu-item-row');
            rows.forEach(function (row, i) {
                row.dataset.index = i;
                var selects = row.querySelectorAll('select, input');
                selects.forEach(function (el) {
                    el.name = el.name.replace(/\[\d+\]/, '[' + i + ']');
                });
                var removeBtn = row.querySelector('.remove-edit-menu-item');
                if (removeBtn) removeBtn.dataset.index = i;
                // Hide remove btn if only 1 row
                var allRows = container.querySelectorAll('.edit-menu-item-row');
                allRows.forEach(function (r) {
                    var btn = r.querySelector('.remove-edit-menu-item');
                    if (btn) btn.classList.toggle('hidden', allRows.length <= 1);
                });
            });
        }

        function getEditMenuById(id) {
            for (var i = 0; i < menuData.length; i++) {
                if (String(menuData[i].id) === String(id)) return menuData[i];
            }
            return null;
        }

        // Set correct min for existing rows on page load
        var existingRows = container.querySelectorAll('.edit-menu-item-row');
        existingRows.forEach(function (row) {
            var select = row.querySelector('select[name*="[menu_id]"]');
            var qtyInput = row.querySelector('input[name*="[quantity]"]');
            if (select && qtyInput) {
                var menu = getEditMenuById(select.value);
                if (menu && menu.minimum_portions > 1) {
                    qtyInput.min = menu.minimum_portions;
                    if (parseInt(qtyInput.value) < menu.minimum_portions) {
                        qtyInput.value = menu.minimum_portions;
                    }
                }
            }
        });

        // Update min quantity when menu selection changes
        container.addEventListener('change', function (e) {
            var select = e.target.closest('select[name*="[menu_id]"]');
            if (!select) return;
            var row = select.closest('.edit-menu-item-row');
            if (!row) return;
            var qtyInput = row.querySelector('input[name*="[quantity]"]');
            if (!qtyInput) return;
            var menu = getEditMenuById(select.value);
            if (menu && menu.minimum_portions > 1) {
                qtyInput.min = menu.minimum_portions;
                if (parseInt(qtyInput.value) < menu.minimum_portions) {
                    qtyInput.value = menu.minimum_portions;
                }
            } else {
                qtyInput.min = 1;
            }
        });

        addBtn.addEventListener('click', function () {
            var rows = container.querySelectorAll('.edit-menu-item-row');
            var idx = rows.length;
            var div = document.createElement('div');
            div.className = 'edit-menu-item-row flex items-start gap-2';
            div.dataset.index = idx;
            div.innerHTML =
                '<div class="flex-1"><label class="block text-sm font-medium text-text mb-1.5"><?= __('menu') ?> <span class="text-danger">*</span></label>' +
                buildSelect(idx) +
                '</div>' +
                '<div class="w-28 shrink-0"><label class="block text-sm font-medium text-text mb-1.5"><?= __('qty') ?> <span class="text-danger">*</span></label>' +
                '<input type="number" name="items[' + idx + '][quantity]" value="1" min="1" required class="w-full px-3 py-3 border border-border rounded-lg text-sm text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light">' +
                '</div>' +
                '<button type="button" class="remove-edit-menu-item mt-6 w-9 h-9 flex items-center justify-center rounded-lg text-muted hover:text-danger hover:bg-danger/10 transition-all duration-150 cursor-pointer border-0 bg-transparent shrink-0" data-index="' + idx + '" title="<?= __('remove') ?>">' +
                '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg></button>';
            container.appendChild(div);
            updateEditIndices();
        });

        container.addEventListener('click', function (e) {
            var btn = e.target.closest('.remove-edit-menu-item');
            if (!btn) return;
            var row = btn.closest('.edit-menu-item-row');
            if (row) {
                row.remove();
                updateEditIndices();
            }
        });
    })();
</script>