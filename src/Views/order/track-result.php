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
    <style>
        :root {
            --bg-dark: #09090b;
            --card-bg: rgba(255, 255, 255, 0.03);
            --card-border: rgba(255, 255, 255, 0.08);
            --accent-gold: #e58e26;
            --accent-gold-glow: rgba(229, 142, 38, 0.3);
            --status-pending: #f59e0b;
            --status-processing: #4f46e5;
            --status-delivering: #4f46e5;
            --status-completed: #10b981;
            --status-cancelled: #ef4444;
            --text-light: #f4f4f5;
            --text-muted: #a1a1aa;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: radial-gradient(circle at 15% 25%, rgba(229, 142, 38, 0.12) 0%, transparent 45%),
                radial-gradient(circle at 85% 75%, rgba(234, 32, 39, 0.08) 0%, transparent 45%),
                var(--bg-dark);
            color: var(--text-light);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            line-height: 1.6;
        }

        h1,
        h2,
        h3,
        .logo-text {
            font-family: 'Outfit', sans-serif;
        }

        .wrapper {
            max-width: 640px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        header {
            background: rgba(9, 9, 11, 0.6);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--card-border);
            padding: 1rem 0;
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            color: var(--text-light);
        }

        .logo-icon {
            font-size: 1.5rem;
            filter: drop-shadow(0 0 8px var(--accent-gold-glow));
        }

        .logo-text {
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #fff 0%, var(--accent-gold) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .btn-outline {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--card-border);
            color: var(--text-light);
            padding: 0.5rem 1.25rem;
            border-radius: 9999px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }

        .btn-outline:hover {
            background: var(--accent-gold);
            border-color: var(--accent-gold);
            box-shadow: 0 0 15px var(--accent-gold-glow);
        }

        .result-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            backdrop-filter: blur(16px) saturate(120%);
            border-radius: 20px;
            padding: 2rem;
            margin-top: 2.5rem;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--card-border);
            margin-bottom: 1.5rem;
        }

        .order-id {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .order-id small {
            font-size: 0.85rem;
            font-weight: 400;
            color: var(--text-muted);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 1rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-pending {
            background: rgba(245, 158, 11, 0.15);
            color: var(--status-pending);
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .badge-processing {
            background: rgba(79, 70, 229, 0.15);
            color: var(--status-processing);
            border: 1px solid rgba(79, 70, 229, 0.3);
        }

        .badge-delivering {
            background: rgba(79, 70, 229, 0.15);
            color: var(--status-delivering);
            border: 1px solid rgba(79, 70, 229, 0.3);
        }

        .badge-completed {
            background: rgba(16, 185, 129, 0.15);
            color: var(--status-completed);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .badge-cancelled {
            background: rgba(239, 68, 68, 0.15);
            color: var(--status-cancelled);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .badge-unpaid {
            background: rgba(245, 158, 11, 0.15);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .badge-paid {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .badge-refunded {
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .status-row {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 140px 1fr;
            gap: 1rem 0.75rem;
            font-size: 0.9rem;
        }

        .detail-label {
            color: var(--text-muted);
            font-weight: 500;
        }

        .detail-value {
            color: var(--text-light);
        }

        .detail-value.price {
            font-weight: 700;
            color: var(--accent-gold);
            font-size: 1.1rem;
        }

        .divider {
            border: none;
            border-top: 1px solid var(--card-border);
            margin: 1.5rem 0;
        }

        .timeline {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .timeline-step {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
        }

        .timeline-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-top: 4px;
            flex-shrink: 0;
        }

        .timeline-dot.active {
            background: var(--accent-gold);
            box-shadow: 0 0 8px var(--accent-gold-glow);
        }

        .timeline-dot.inactive {
            background: rgba(255, 255, 255, 0.1);
        }

        .timeline-content {}

        .timeline-content .step-label {
            font-weight: 600;
            font-size: 0.9rem;
        }

        .timeline-content .step-desc {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .action-link {
            display: block;
            text-align: center;
            margin-top: 1.5rem;
            color: var(--accent-gold);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .action-link:hover {
            text-shadow: 0 0 8px var(--accent-gold-glow);
        }

        .footer-text {
            text-align: center;
            color: var(--text-muted);
            font-size: 0.8rem;
            margin-top: 2rem;
            padding-bottom: 2rem;
        }

        .footer-text a {
            color: var(--accent-gold);
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .result-card {
                padding: 1.25rem;
            }

            .order-header {
                flex-direction: column;
                gap: 1rem;
            }

            .detail-grid {
                grid-template-columns: 1fr;
                gap: 0.25rem;
            }

            .detail-label {
                font-size: 0.8rem;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="wrapper nav-container">
            <a href="/" class="logo">
                <span class="logo-icon">🍲</span>
                <span class="logo-text">Siwayut Catering</span>
            </a>
            <a href="/track-order" class="btn-outline">&larr; Search Again</a>
        </div>
    </header>
    <main class="wrapper">
        <div class="result-card">
            <div class="order-header">
                <div>
                    <div class="order-id">
                        Order #<?= (int) $order['id'] ?>
                        <small>created <?= date('d M Y, H:i', strtotime($order['created_at'])) ?></small>
                    </div>
                </div>
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <span class="badge badge-<?= \App\Core\View::e($order['status']) ?>">
                        <?php
                        $statusLabels = [
                            'pending' => 'Pending',
                            'processing' => 'Processing',
                            'delivering' => 'Delivering',
                            'completed' => 'Completed',
                            'cancelled' => 'Cancelled',
                        ];
                        echo \App\Core\View::e($statusLabels[$order['status']] ?? $order['status']);
                        ?>
                    </span>
                    <span class="badge badge-<?= \App\Core\View::e($order['payment_status']) ?>">
                        <?php
                        $paymentLabels = [
                            'unpaid' => 'Unpaid',
                            'paid' => 'Paid',
                            'refunded' => 'Refunded',
                        ];
                        echo \App\Core\View::e($paymentLabels[$order['payment_status']] ?? $order['payment_status']);
                        ?>
                    </span>
                </div>
            </div>

            <div class="detail-grid">
                <div class="detail-label">Customer Name</div>
                <div class="detail-value"><?= \App\Core\View::e($customer['name'] ?? '-') ?></div>

                <div class="detail-label">Phone No.</div>
                <div class="detail-value"><?= \App\Core\View::e($customer['phone'] ?? '-') ?></div>

                <div class="detail-label">Menu</div>
                <div class="detail-value"><?= \App\Core\View::e($menu['name'] ?? '-') ?></div>

                <div class="detail-label">Event</div>
                <div class="detail-value"><?= \App\Core\View::e($event['name'] ?? '-') ?></div>

                <div class="detail-label">Portions</div>
                <div class="detail-value"><?= (int) $order['quantity'] ?> portions</div>

                <div class="detail-label">Total Price</div>
                <div class="detail-value price">Rp <?= number_format((float) $order['total_price'], 0, ',', '.') ?>
                </div>

                <div class="detail-label">Event Date</div>
                <div class="detail-value"><?= date('d F Y, H:i', strtotime($order['event_date'])) ?></div>

                <div class="detail-label">Address</div>
                <div class="detail-value"><?= nl2br(\App\Core\View::e($order['delivery_address'])) ?></div>

                <?php if ($order['notes']): ?>
                    <div class="detail-label">Notes</div>
                    <div class="detail-value"><?= nl2br(\App\Core\View::e($order['notes'])) ?></div>
                <?php endif; ?>
            </div>

            <hr class="divider">


            <div
                style="font-weight: 600; font-size: 0.9rem; margin-bottom: 1rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">
                Order Status
            </div>
            <div class="timeline">
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
                    <div class="timeline-step">
                        <div class="timeline-dot <?= $isActive ? 'active' : 'inactive' ?>"></div>
                        <div class="timeline-conten
     t                      ">

                            <div class="step-label"
                                style="<?= $isActive ? 'color: var(--text-light);' : 'color: var(--text-muted);' ?>">
                                <?= \App\Core\View::e($step['label']) ?></div>
                            <div class="step-desc"><?= \App\Core\View::e($step['desc']) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if ($orderStatus === 'cancelled'): ?>
                    <div class="timeline-step">

                        <div class="timeline-dot active"
                            style="background: var(--status-cancelled); box-shadow: 0 0 8px rgba(239,68,68,0.3);"></div>
                        <div class="timeline-content">
                            <div class="step-label" style="color: var(--status-cancelled);">Cancelled</div>
                            <div class="step-desc">Order has been cancelled</div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <a href="/track-order" class="action-link">&larr; Track another order</a>
        </div>

        <div class="footer-text">
            <a href="/">Siwayut Catering</a> — Exquisite Taste For Your Most Sacred Moments
        </div>
        </m ain>
</body>

</html>