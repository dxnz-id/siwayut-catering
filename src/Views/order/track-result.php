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

        </div>
    </header>

    <?php
    $statusLabels = [
        'pending' => __('pending'),
        'processing' => __('processing'),
        'delivering' => __('delivering'),
        'completed' => __('completed'),
        'cancelled' => __('cancelled'),
    ];
    $paymentLabels = [
        'unpaid' => __('unpaid'),
        'paid' => __('paid'),
        'refunded' => __('refunded'),
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
                        <?= __('detail_order') ?>
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
                <div class="text-muted font-medium max-md:text-[0.8rem]"><?= __('order_number_label') ?></div>
                <div class="text-text"><?= \App\Core\View::e($order['order_number']) ?></div>

                <div class="text-muted font-medium max-md:text-[0.8rem]"><?= __('customer_name_label') ?></div>
                <div class="text-text"><?= \App\Core\View::e($customer['name'] ?? '-') ?></div>

                <div class="text-muted font-medium max-md:text-[0.8rem]"><?= __('order_date') ?></div>
                <div class="text-text"><?= date('d M Y, H:i', strtotime($order['created_at'])) ?></div>

                <div class="text-muted font-medium max-md:text-[0.8rem]"><?= __('phone_no') ?></div>
                <div class="text-text"><?= \App\Core\View::e($customer['phone'] ?? '-') ?></div>

                <div class="text-muted font-medium max-md:text-[0.8rem]"><?= __('menu_items') ?></div>
                <div class="text-text">
                    <table class="w-full text-[0.85rem]">
                        <thead>
                            <tr class="text-muted border-b border-border">
                                <th class="text-left pb-1.5 font-medium"><?= __('menu') ?></th>
                                <th class="text-right pb-1.5 font-medium"><?= __('qty') ?></th>
                                <th class="text-right pb-1.5 font-medium"><?= __('subtotal') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                            <tr class="border-b border-border/50">
                                <td class="py-1.5"><?= \App\Core\View::e($item['menu_name']) ?></td>
                                <td class="py-1.5 text-right"><?= (int) $item['quantity'] ?></td>
                                <td class="py-1.5 text-right font-medium">Rp <?= number_format((float) $item['subtotal'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="text-muted font-medium max-md:text-[0.8rem]"><?= __('event') ?></div>
                <div class="text-text"><?= \App\Core\View::e($event['name'] ?? '-') ?></div>

                <div class="text-muted font-medium max-md:text-[0.8rem]"><?= __('total_price') ?></div>
                <div class="font-display text-xl font-bold text-gold">Rp <?= number_format((float) $order['total_price'], 0, ',', '.') ?></div>

                <div class="text-muted font-medium max-md:text-[0.8rem]"><?= __('event_date') ?></div>
                <div class="text-text"><?= date('d F Y, H:i', strtotime($order['event_date'])) ?></div>

                <div class="text-muted font-medium max-md:text-[0.8rem]"><?= __('address') ?></div>
                <div class="text-text"><?= nl2br(\App\Core\View::e($order['delivery_address'])) ?></div>

                <?php if ($order['notes']): ?>
                    <div class="text-muted font-medium max-md:text-[0.8rem]"><?= __('notes') ?></div>
                    <div class="text-text"><?= nl2br(\App\Core\View::e($order['notes'])) ?></div>
                <?php endif; ?>
            </div>

            <hr class="border-0 border-t border-border my-6">

            <div class="font-semibold text-[0.9rem] mb-4 text-muted uppercase tracking-wide">
                <?= __('order_status') ?>
            </div>
            <div class="flex flex-col gap-4">
                <?php
                $statuses = [
                    'pending' => ['label' => __('order_received'), 'desc' => __('order_received_desc')],
                    'processing' => ['label' => __('processing'), 'desc' => __('processing_desc')],
                    'delivering' => ['label' => __('delivering'), 'desc' => __('delivering_desc')],
                    'completed' => ['label' => __('completed'), 'desc' => __('completed_desc')],
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
                            <div class="font-semibold text-[0.9rem] text-[#ef4444]"><?= __('cancelled_label') ?></div>
                            <div class="text-[0.8rem] text-muted"><?= __('cancelled_desc') ?></div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <a href="javascript:void(0)" onclick="history.back();return false" class="block text-center mt-6 text-gold no-underline text-[0.9rem] font-medium transition-all duration-300 hover:[text-shadow:0_0_8px_var(--color-gold-glow)]"><?= __('back') ?></a>
        </div>

    </main>
    <?php component('footer') ?>
</body>

</html>
