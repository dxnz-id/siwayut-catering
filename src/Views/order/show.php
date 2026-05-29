<div class="max-w-[1040px] mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-10 flex-wrap gap-3">
        <a href="/orders"
            class="inline-flex items-center gap-2 px-0 text-sm text-muted no-underline hover:text-gold transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            Back to Orders
        </a>
        <div class="flex items-center gap-3">
            <button type="button" id="toggle-edit-status"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-full text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-white/5 border-border text-text backdrop-blur-[8px] hover:bg-gold hover:border-gold hover:shadow-[0_0_15px_var(--color-gold-glow)] hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/></svg>
                Update Status
            </button>
        </div>
    </div>

    <!-- Hero: Gradient panel + floating info -->
    <div class="relative mb-10">
        <div class="h-[200px] max-md:h-[160px] rounded-2xl bg-gradient-to-br from-gold/15 via-gold/5 to-accent-red/5 flex items-center justify-center overflow-hidden">
            <div class="absolute inset-0 opacity-[0.06]">
                <svg class="w-full h-full" viewBox="0 0 1040 200" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 120 Q 260 40, 520 100 T 1040 80 L 1040 200 L 0 200 Z" fill="currentColor"/></svg>
            </div>
            <div class="text-center relative z-[1]">
                <div class="text-xs text-muted/50 uppercase tracking-widest font-medium mb-1">Order</div>
                <div class="font-display text-4xl md:text-5xl font-bold text-white/10 tracking-tight select-none"><?= htmlspecialchars($order['order_number']) ?></div>
            </div>
        </div>

        <!-- Floating info panel -->
        <div class="absolute -bottom-8 left-6 right-6 max-md:static max-md:mt-6 max-md:px-0">
            <div class="bg-[#18181b]/90 backdrop-blur-[20px] border border-white/10 rounded-xl px-6 py-5 flex items-center justify-between flex-wrap gap-4 shadow-xl">
                <div class="flex items-center gap-4 flex-wrap">
                    <div>
                        <div class="inline-flex items-center gap-2.5 mb-2">
                            <span class="order-status-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-[0.7rem] font-semibold uppercase tracking-widest"
                                style="background:<?php
                                    $sc = ['pending'=>'rgba(245,158,11,0.12)','processing'=>'rgba(79,70,229,0.12)','delivering'=>'rgba(79,70,229,0.12)','completed'=>'rgba(16,185,129,0.12)','cancelled'=>'rgba(239,68,68,0.12)'];
                                    echo $sc[$order['status']] ?? 'rgba(161,161,170,0.12)';
                                ?>;color:<?php
                                    $st = ['pending'=>'#f59e0b','processing'=>'#818cf8','delivering'=>'#818cf8','completed'=>'#10b981','cancelled'=>'#ef4444'];
                                    echo $st[$order['status']] ?? '#a1a1aa';
                                ?>"
                                data-status="<?= e($order['status']) ?>">
                                <?= e(ucfirst($order['status'])) ?>
                            </span>
                            <span class="payment-status-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-[0.7rem] font-semibold uppercase tracking-widest"
                                style="background:<?php
                                    $pc = ['unpaid'=>'rgba(245,158,11,0.12)','paid'=>'rgba(16,185,129,0.12)','refunded'=>'rgba(239,68,68,0.12)'];
                                    echo $pc[$order['payment_status']] ?? 'rgba(161,161,170,0.12)';
                                ?>;color:<?php
                                    $pt = ['unpaid'=>'#f59e0b','paid'=>'#10b981','refunded'=>'#ef4444'];
                                    echo $pt[$order['payment_status']] ?? '#a1a1aa';
                                ?>"
                                data-payment="<?= e($order['payment_status']) ?>">
                                <?= e(ucfirst($order['payment_status'])) ?>
                            </span>
                            <span class="text-[0.7rem] text-muted uppercase tracking-widest">Order</span>
                        </div>
                        <h1 class="text-2xl md:text-3xl font-bold font-display text-text leading-tight">Order <span class="text-gold"><?= htmlspecialchars($order['order_number']) ?></span></h1>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <div class="text-xs text-muted uppercase tracking-wider font-medium mb-1">Total</div>
                    <div class="font-display text-2xl md:text-3xl font-bold text-gold" id="order-total">Rp <?= number_format((float)$order['total_price'], 0, ',', '.') ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Spacer for floating panel (desktop) -->
    <div class="h-8 max-md:hidden"></div>

    <!-- Order stats badges -->
    <div class="flex flex-wrap items-center gap-2.5 mb-10">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/[0.04] border border-white/5 text-xs text-muted font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-gold"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
            <?= e($customer['name'] ?? 'Unknown Customer') ?>
        </span>
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/[0.04] border border-white/5 text-xs text-muted font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-gold"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
            <?= date('d M Y', strtotime($order['event_date'])) ?>
        </span>
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/[0.04] border border-white/5 text-xs text-muted font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-gold"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            Ordered <?= date('d M Y', strtotime($order['created_at'])) ?>
        </span>
    </div>

    <!-- Status update form (hidden by default) -->
    <form id="status-form" action="/orders/<?= (int)$order['id'] ?>" method="POST" class="hidden mb-8">
        <?= csrf_field() ?>
        <div class="bg-white/[0.03] border border-white/5 rounded-xl p-5 flex items-end gap-4 flex-wrap">
            <div class="flex-1 min-w-[160px]">
                <label class="block text-xs text-muted font-medium mb-1.5">Status</label>
                <select name="status" class="w-full px-3 py-2.5 border border-white/10 rounded-lg text-sm text-text bg-black/40 font-body focus:outline-none focus:border-gold/50 focus:ring-[3px] focus:ring-gold/10 transition-all duration-200">
                    <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                    <option value="delivering" <?= $order['status'] === 'delivering' ? 'selected' : '' ?>>Delivering</option>
                    <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>
            <div class="flex-1 min-w-[160px]">
                <label class="block text-xs text-muted font-medium mb-1.5">Payment</label>
                <select name="payment_status" class="w-full px-3 py-2.5 border border-white/10 rounded-lg text-sm text-text bg-black/40 font-body focus:outline-none focus:border-gold/50 focus:ring-[3px] focus:ring-gold/10 transition-all duration-200">
                    <option value="unpaid" <?= $order['payment_status'] === 'unpaid' ? 'selected' : '' ?>>Unpaid</option>
                    <option value="paid" <?= $order['payment_status'] === 'paid' ? 'selected' : '' ?>>Paid</option>
                    <option value="refunded" <?= $order['payment_status'] === 'refunded' ? 'selected' : '' ?>>Refunded</option>
                </select>
            </div>
            <button type="submit"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-gold border-gold text-white shadow-[0_0_12px_var(--color-gold-glow)] hover:shadow-[0_0_20px_var(--color-gold-glow)]">
                Save Changes
            </button>
        </div>
    </form>

    <!-- Two-column: Timeline + Details -->
    <div class="grid grid-cols-1 md:grid-cols-[1fr_1.6fr] gap-8 mb-10">
        <!-- Left: Status Timeline -->
        <div>
            <h2 class="text-sm font-semibold text-muted uppercase tracking-widest mb-5 flex items-center gap-3">
                <span class="w-6 h-px bg-gold/50"></span>
                Order Progress
            </h2>

            <div class="flex flex-col gap-5" id="status-timeline">
                <?php
                $steps = [
                    'pending' => ['label' => 'Order Received', 'desc' => 'Being reviewed'],
                    'processing' => ['label' => 'Processing', 'desc' => 'Preparing your order'],
                    'delivering' => ['label' => 'Delivering', 'desc' => 'On its way'],
                    'completed' => ['label' => 'Completed', 'desc' => 'Order has arrived'],
                ];
                $orderStatus = $order['status'];
                foreach ($steps as $key => $step):
                    $isActive = in_array($orderStatus, ['pending', 'processing', 'delivering', 'completed']) && (
                        ($key === 'pending') ||
                        ($key === 'processing' && in_array($orderStatus, ['processing', 'delivering', 'completed'])) ||
                        ($key === 'delivering' && in_array($orderStatus, ['delivering', 'completed'])) ||
                        ($key === 'completed' && $orderStatus === 'completed')
                    );
                    if ($orderStatus === 'cancelled') $isActive = $key === 'pending';
                ?>
                <div class="flex gap-4 items-start status-step" data-step="<?= e($key) ?>">
                    <div class="flex flex-col items-center">
                        <div class="w-3.5 h-3.5 rounded-full border-2 transition-all duration-300 status-dot <?= $isActive ? 'border-gold bg-gold shadow-[0_0_8px_var(--color-gold-glow)]' : 'border-white/20 bg-transparent' ?>"></div>
                        <?php if ($key !== 'completed'): ?>
                        <div class="w-px flex-1 min-h-[28px] <?= $isActive && $orderStatus !== $key ? 'bg-gradient-to-b from-gold/50 to-white/5' : 'bg-white/5' ?>"></div>
                        <?php endif; ?>
                    </div>
                    <div class="pb-1">
                        <div class="font-semibold text-sm transition-colors duration-300 <?= $isActive ? 'text-text' : 'text-muted/60' ?>"><?= e($step['label']) ?></div>
                        <div class="text-xs text-muted/50"><?= e($step['desc']) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if ($orderStatus === 'cancelled'): ?>
                <div class="flex gap-4 items-start" data-step="cancelled">
                    <div class="flex flex-col items-center">
                        <div class="w-3.5 h-3.5 rounded-full border-2 border-red-500 bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.3)]"></div>
                    </div>
                    <div>
                        <div class="font-semibold text-sm text-[#ef4444]">Cancelled</div>
                        <div class="text-xs text-muted/50">Order has been cancelled</div>
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
                    Customer
                </h2>
                <div class="bg-white/[0.03] border border-white/5 rounded-xl p-5">
                    <div class="grid grid-cols-[110px_1fr] max-md:grid-cols-1 gap-y-3 gap-x-3 text-sm">
                        <div class="text-muted">Name</div>
                        <div class="text-text font-medium"><?= e($customer['name'] ?? '-') ?></div>
                        <div class="text-muted">Phone</div>
                        <div class="text-text">
                            <?php if ($customer['phone'] ?? null): ?>
                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $customer['phone']) ?>" target="_blank" class="text-gold hover:text-gold no-underline inline-flex items-center gap-1.5">
                                <?= e($customer['phone']) ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                            </a>
                            <?php else: ?><span class="text-muted">-</span><?php endif; ?>
                        </div>
                        <div class="text-muted">Event Date</div>
                        <div class="text-text"><?= date('d F Y, H:i', strtotime($order['event_date'])) ?></div>
                        <div class="text-muted">Address</div>
                        <div class="text-text"><?= nl2br(e($order['delivery_address'])) ?></div>
                        <?php if ($order['notes']): ?>
                        <div class="text-muted">Notes</div>
                        <div class="text-text text-muted italic"><?= nl2br(e($order['notes'])) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Menu Items -->
            <div>
                <h2 class="text-sm font-semibold text-muted uppercase tracking-widest mb-5 flex items-center gap-3">
                    <span class="w-6 h-px bg-gold/50"></span>
                    Menu Items
                </h2>
                <div class="bg-white/[0.03] border border-white/5 rounded-xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr>
                                    <th class="text-left text-xs font-semibold uppercase tracking-wider text-muted/60 px-5 py-3 border-b border-white/5 bg-white/[0.02]">Menu</th>
                                    <th class="text-right text-xs font-semibold uppercase tracking-wider text-muted/60 px-5 py-3 border-b border-white/5 bg-white/[0.02]">Qty</th>
                                    <th class="text-right text-xs font-semibold uppercase tracking-wider text-muted/60 px-5 py-3 border-b border-white/5 bg-white/[0.02]">Price</th>
                                    <th class="text-right text-xs font-semibold uppercase tracking-wider text-muted/60 px-5 py-3 border-b border-white/5 bg-white/[0.02]">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $total = 0; foreach ($items as $item): $total += (float)$item['subtotal']; ?>
                                <tr class="border-b border-white/[0.03] hover:bg-white/[0.02] transition-all duration-150">
                                    <td class="px-5 py-3.5 text-sm text-text"><?= e($item['menu_name']) ?></td>
                                    <td class="px-5 py-3.5 text-sm text-text text-right"><?= (int)$item['quantity'] ?>x</td>
                                    <td class="px-5 py-3.5 text-sm text-text text-right">Rp <?= number_format((float)$item['price_at_time'], 0, ',', '.') ?></td>
                                    <td class="px-5 py-3.5 text-sm text-text text-right font-medium">Rp <?= number_format((float)$item['subtotal'], 0, ',', '.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="px-5 py-3.5 text-sm font-semibold text-text text-right">Total</td>
                                    <td class="px-5 py-3.5 text-sm font-bold text-gold text-right" id="items-total">Rp <?= number_format($total, 0, ',', '.') ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    'use strict';

    var toggleBtn = document.getElementById('toggle-edit-status');
    var statusForm = document.getElementById('status-form');
    if (toggleBtn && statusForm) {
        toggleBtn.addEventListener('click', function() {
            statusForm.classList.toggle('hidden');
        });
    }

    if (statusForm) {
        statusForm.addEventListener('submit', function(e) {
            e.preventDefault();
            var form = e.target;
            var btn = form.querySelector('button[type="submit"]');
            var origText = btn.textContent;
            btn.disabled = true;
            btn.innerHTML = '<span class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin inline-block"></span> Saving...';

            fetch(form.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(form)
            })
            .then(function(r) { return r.json(); })
            .then(function(resp) {
                if (resp.success) {
                    window.location.reload();
                } else {
                    if (window.AppModules && window.AppModules.toast) {
                        window.AppModules.toast.show(resp.error || 'Update failed.', 'error');
                    }
                    btn.disabled = false;
                    btn.textContent = origText;
                }
            })
            .catch(function() {
                if (window.AppModules && window.AppModules.toast) {
                    window.AppModules.toast.show('Network error.', 'error');
                }
                btn.disabled = false;
                btn.textContent = origText;
            });
        });
    }

    var totalEl = document.getElementById('order-total');
    if (totalEl) {
        var target = parseFloat(totalEl.textContent.replace(/[^0-9]/g, ''));
        if (target > 0) {
            var duration = 600;
            var startTime = null;
            function animate(time) {
                if (!startTime) startTime = time;
                var elapsed = time - startTime;
                var progress = Math.min(elapsed / duration, 1);
                var eased = 1 - (1 - progress) * (1 - progress);
                var current = Math.floor(eased * target);
                totalEl.textContent = 'Rp ' + current.toLocaleString('id-ID');
                if (progress < 1) requestAnimationFrame(animate);
            }
            totalEl.textContent = 'Rp 0';
            requestAnimationFrame(animate);
        }
    }
})();
</script>
