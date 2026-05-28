<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \App\Core\View::e($title ?? 'Order Detail — Siwayut Catering') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/app.css?v=3">
</head>

<body class="bg-bg text-text min-h-screen leading-relaxed font-body overflow-x-hidden bg-fixed bg-[radial-gradient(circle_at_15%_25%,rgba(229,142,38,0.12)_0%,transparent_45%),radial-gradient(circle_at_85%_75%,rgba(234,32,39,0.08)_0%,transparent_45%)]">

    <header class="sticky top-0 z-[100] bg-bg/60 backdrop-blur-[12px] border-b border-border py-4">
        <div class="max-w-[1200px] mx-auto px-6 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 no-underline text-text">
                <span class="text-[1.8rem] drop-shadow-[0_0_8px_var(--accent-gold-glow)]">🍲</span>
                <span class="font-display text-2xl font-bold tracking-tight bg-gradient-to-r from-white to-gold bg-clip-text text-transparent">Siwayut Catering</span>
            </a>
            <a href="/track-order" class="inline-flex items-center gap-2 px-5 py-2 rounded-full text-sm font-medium no-underline bg-white/5 border border-border text-text backdrop-blur-[8px] hover:bg-gold hover:border-gold hover:shadow-[0_0_15px_var(--color-gold-glow)] transition-all duration-300">&larr; Search Again</a>
        </div>
    </header>

    <?php
    $statusLabels = [
        'pending' => 'Pending',
        'processing' => 'Processing',
        'delivering' => 'Delivering',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];
    $paymentLabels = [
        'unpaid' => 'Unpaid',
        'paid' => 'Paid',
        'refunded' => 'Refunded',
    ];

    $statusStyles = [
        'pending' => 'background:rgba(245,158,11,0.15);color:#f59e0b;border-color:rgba(245,158,11,0.3)',
        'processing' => 'background:rgba(79,70,229,0.15);color:#4f46e5;border-color:rgba(79,70,229,0.3)',
        'delivering' => 'background:rgba(79,70,229,0.15);color:#4f46e5;border-color:rgba(79,70,229,0.3)',
        'completed' => 'background:rgba(16,185,129,0.15);color:#10b981;border-color:rgba(16,185,129,0.3)',
        'cancelled' => 'background:rgba(239,68,68,0.15);color:#ef4444;border-color:rgba(239,68,68,0.3)',
        'unpaid' => 'background:rgba(245,158,11,0.15);color:#f59e0b;border-color:rgba(245,158,11,0.3)',
        'paid' => 'background:rgba(16,185,129,0.15);color:#10b981;border-color:rgba(16,185,129,0.3)',
        'refunded' => 'background:rgba(239,68,68,0.15);color:#ef4444;border-color:rgba(239,68,68,0.3)',
    ];
    ?>

    <main class="max-w-[640px] mx-auto px-6">
        <div class="bg-card-bg border border-border backdrop-blur-[16px] rounded-xl p-8 max-md:p-5 mt-10">
            <div class="flex max-md:flex-col max-md:gap-4 items-start justify-between pb-6 border-b border-border mb-6">
                <div>
                    <div class="text-2xl font-bold tracking-tight">
                        Detail Order
                    </div>
                </div>
                <div class="flex gap-2 flex-wrap">
                    <span class="inline-flex items-center px-4 py-[0.35rem] rounded-full text-[0.8rem] font-semibold uppercase tracking-wide"
                        style="<?= \App\Core\View::e($statusStyles[$order['status']] ?? '') ?>">
                        <?= \App\Core\View::e($statusLabels[$order['status']] ?? $order['status']) ?>
                    </span>
                    <span class="inline-flex items-center px-4 py-[0.35rem] rounded-full text-[0.8rem] font-semibold uppercase tracking-wide"
                        style="<?= \App\Core\View::e($statusStyles[$order['payment_status']] ?? '') ?>">
                        <?= \App\Core\View::e($paymentLabels[$order['payment_status']] ?? $order['payment_status']) ?>
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-[140px_1fr] max-md:grid-cols-1 gap-y-4 gap-x-3 text-[0.9rem]">
                <div class="text-muted font-medium max-md:text-[0.8rem]">Order ID</div>
                <div class="text-text"><?= (int) $order['id'] ?></div>

                <div class="text-muted font-medium max-md:text-[0.8rem]">Customer Name</div>
                <div class="text-text"><?= \App\Core\View::e($customer['name'] ?? '-') ?></div>

                <div class="text-muted font-medium max-md:text-[0.8rem]">Order Date</div>
                <div class="text-text"><?= date('d M Y, H:i', strtotime($order['created_at'])) ?></div>

                <div class="text-muted font-medium max-md:text-[0.8rem]">Phone No.</div>
                <div class="text-text"><?= \App\Core\View::e($customer['phone'] ?? '-') ?></div>

                <div class="text-muted font-medium max-md:text-[0.8rem]">Menu</div>
                <div class="text-text"><?= \App\Core\View::e($menu['name'] ?? '-') ?></div>

                <div class="text-muted font-medium max-md:text-[0.8rem]">Event</div>
                <div class="text-text"><?= \App\Core\View::e($event['name'] ?? '-') ?></div>

                <div class="text-muted font-medium max-md:text-[0.8rem]">Portions</div>
                <div class="text-text"><?= (int) $order['quantity'] ?> portions</div>

                <div class="text-muted font-medium max-md:text-[0.8rem]">Total Price</div>
                <div class="font-display text-xl font-bold text-gold">Rp <?= number_format((float) $order['total_price'], 0, ',', '.') ?></div>

                <div class="text-muted font-medium max-md:text-[0.8rem]">Event Date</div>
                <div class="text-text"><?= date('d F Y, H:i', strtotime($order['event_date'])) ?></div>

                <div class="text-muted font-medium max-md:text-[0.8rem]">Address</div>
                <div class="text-text"><?= nl2br(\App\Core\View::e($order['delivery_address'])) ?></div>

                <?php if ($order['notes']): ?>
                    <div class="text-muted font-medium max-md:text-[0.8rem]">Notes</div>
                    <div class="text-text"><?= nl2br(\App\Core\View::e($order['notes'])) ?></div>
                <?php endif; ?>
            </div>

            <hr class="border-0 border-t border-border my-6">

            <div class="font-semibold text-[0.9rem] mb-4 text-muted uppercase tracking-wide">
                Order Status
            </div>
            <div class="flex flex-col gap-4">
                <?php
                $statuses = [
                    'pending' => ['label' => 'Order Received', 'desc' => 'Your order is being reviewed'],
                    'processing' => ['label' => 'Processing', 'desc' => 'We are preparing your order'],
                    'delivering' => ['label' => 'Delivering', 'desc' => 'Your order is on its way'],
                    'completed' => ['label' => 'Completed', 'desc' => 'Your order has arrived'],
                ];
                $orderStatus = $order['status'];
                $reached = $orderStatus === 'completed';
                foreach ($statuses as $key => $step):
                    $isActive = in_array($orderStatus, ['pending', 'processing', 'delivering', 'completed']) && (
                        ($key === 'pending') ||
                        ($key === 'processing' && in_array($orderStatus, ['processing', 'delivering', 'completed'])) ||
                        ($key === 'delivering' && in_array($orderStatus, ['delivering', 'completed'])) ||
                        ($key === 'completed' && $orderStatus === 'completed')
                    );

                    if ($orderStatus === 'cancelled') {
                        $isActive = $key === 'pending';
                    }
                    ?>
                    <div class="flex gap-4 items-start">
                        <div class="w-3 h-3 rounded-full mt-1 shrink-0 <?= $isActive ? 'bg-gold shadow-[0_0_8px_var(--color-gold-glow)]' : 'bg-white/10' ?>"></div>
                        <div>
                            <div class="font-semibold text-[0.9rem] <?= $isActive ? 'text-text' : 'text-muted' ?>"><?= \App\Core\View::e($step['label']) ?></div>
                            <div class="text-[0.8rem] text-muted"><?= \App\Core\View::e($step['desc']) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if ($orderStatus === 'cancelled'): ?>
                    <div class="flex gap-4 items-start">
                        <div class="w-3 h-3 rounded-full mt-1 shrink-0 bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.3)]"></div>
                        <div>
                            <div class="font-semibold text-[0.9rem] text-[#ef4444]">Cancelled</div>
                            <div class="text-[0.8rem] text-muted">Order has been cancelled</div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <a href="/track-order" class="block text-center mt-6 text-gold no-underline text-[0.9rem] font-medium transition-all duration-300 hover:[text-shadow:0_0_8px_var(--color-gold-glow)]">&larr; Track another order</a>
        </div>

        <div class="text-center text-muted text-xs mt-8 pb-8">
            <a href="/" class="text-gold no-underline hover:text-gold">Siwayut Catering</a> — Exquisite Taste For Your Most Sacred Moments
        </div>
    </main>
</body>

</html>
