<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold font-display text-text"><?= htmlspecialchars($title ?? 'Update Order Status') ?></h1>
    <a href="/orders" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-white/6 text-text border-border hover:bg-white/10 hover:text-text">&larr; Back to Orders</a>
</div>

<div class="grid grid-cols-2 gap-6">
    <div class="bg-[#18181b] border border-border rounded-xl overflow-hidden">
        <div class="p-6">
            <h4 class="mb-4 pb-2 border-b border-border font-display font-semibold text-text">Order Details <?= $order['id'] ?></h4>
            <div class="grid grid-cols-[150px_1fr] gap-2 text-sm">
                <div class="font-medium">Customer Name</div>
                <div><?= htmlspecialchars($customer['name'] ?? '-') ?></div>
                <div class="font-medium">Phone</div>
                <div><?= htmlspecialchars($customer['phone'] ?? '-') ?></div>
                <div class="font-medium">Menu</div>
                <div><?= htmlspecialchars($menu['name'] ?? '-') ?></div>
                <div class="font-medium">Quantity</div>
                <div><?= $order['quantity'] ?> portions</div>
                <div class="font-medium">Total Price</div>
                <div class="text-success font-semibold">Rp <?= number_format((float)$order['total_price'], 0, ',', '.') ?></div>
                <div class="font-medium">Payment Status</div>
                <div>
                    <?php
                    $paymentBadgeColors = [
                        'unpaid' => 'var(--color-warning)',
                        'paid' => 'var(--color-success)',
                        'refunded' => 'var(--color-danger)',
                    ];
                    $pColor = $paymentBadgeColors[$order['payment_status']] ?? 'var(--color-text-muted)';
                    $pLabels = ['unpaid' => 'Unpaid', 'paid' => 'Paid', 'refunded' => 'Refunded'];
                    ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium uppercase" style="background: <?= $pColor ?>; color: white;">
                        <?= htmlspecialchars($pLabels[$order['payment_status']] ?? $order['payment_status']) ?>
                    </span>
                </div>
                <div class="font-medium">Event Date</div>
                <div><?= date('d F Y, H:i', strtotime($order['event_date'])) ?></div>
                <div class="font-medium">Delivery Address</div>
                <div><?= nl2br(htmlspecialchars($order['delivery_address'])) ?></div>
                <div class="font-medium">Notes</div>
                <div><?= htmlspecialchars($order['notes'] ?: '-') ?></div>
            </div>
        </div>
    </div>
    
    <div class="bg-[#18181b] border border-border rounded-xl overflow-hidden">
        <div class="p-6">
            <h4 class="mb-4 pb-2 border-b border-border font-display font-semibold text-text">Update Status</h4>
            <form action="/orders/<?= $order['id'] ?>" method="POST">
                <?= \App\Core\Csrf::field() ?>
                
                <?php component('form/select', [
                    'name' => 'status',
                    'label' => 'Order Status',
                    'options' => [
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'delivering' => 'Delivering',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled'
                    ],
                    'selected' => old('status', $order['status']),
                    'required' => true
                ]); ?>

                <?php component('form/select', [
                    'name' => 'payment_status',
                    'label' => 'Payment Status',
                    'options' => [
                        'unpaid' => 'Unpaid',
                        'paid' => 'Paid',
                        'refunded' => 'Refunded',
                    ],
                    'selected' => old('payment_status', $order['payment_status'] ?? 'unpaid'),
                    'required' => true
                ]); ?>

                <div class="flex items-center gap-3 mt-6">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-primary text-white border-primary hover:bg-primary-hover hover:border-primary-hover hover:shadow-[0_0_15px_var(--color-gold-glow)] hover:text-white">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
