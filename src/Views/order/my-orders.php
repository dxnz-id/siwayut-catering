<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \App\Core\View::e($title) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/app.css?v=2">
</head>
<body class="bg-[#09090b] text-[#f4f4f5] font-body min-h-screen">

    <!-- Navbar -->
    <header class="sticky top-0 z-[100] bg-bg/60 backdrop-blur-[12px] border-b border-border py-4">
        <div class="max-w-[1200px] mx-auto px-6 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 no-underline text-text">
                <span class="text-[1.8rem] drop-shadow-[0_0_8px_var(--accent-gold-glow)]">🍲</span>
                <span class="font-display text-2xl font-bold tracking-tight bg-gradient-to-r from-white to-gold bg-clip-text text-transparent">Siwayut Catering</span>
            </a>
            <div class="flex items-center gap-3">
                <?php component('lang-switcher') ?>
                <a href="javascript:void(0)" onclick="history.back();return false" class="inline-flex items-center gap-2 px-5 py-2 rounded-full text-sm font-medium no-underline bg-white/5 border border-border text-text backdrop-blur-[8px] hover:bg-gold hover:border-gold hover:shadow-[0_0_15px_var(--color-gold-glow)] transition-all duration-300"><?= __('back') ?></a>
                <form method="POST" action="/logout" class="m-0 p-0 inline">
                    <?= \App\Core\Csrf::field() ?>
                    <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded-full text-sm font-medium no-underline bg-transparent border border-transparent text-muted hover:text-danger hover:border-danger/30 hover:bg-danger/10 transition-all duration-300 cursor-pointer"><?= __('logout') ?></button>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-[900px] mx-auto px-6 py-10">

        <h1 class="text-2xl font-bold font-display text-text mb-8"><?= __('my_orders') ?></h1>

        <?php if (empty($orders)): ?>
        <div class="bg-card-bg border border-dashed border-border rounded-xl py-16 text-center">
            <div class="text-5xl mb-4 opacity-40">📋</div>
            <p class="text-muted mb-6"><?= __('no_orders_yet') ?></p>
            <a href="/order-form" class="inline-flex items-center gap-2 px-6 py-3 rounded-full text-sm font-semibold no-underline bg-gold border border-gold text-white shadow-[0_0_12px_var(--color-gold-glow)] hover:-translate-y-0.5 transition-all duration-300"><?= __('order_now') ?></a>
        </div>
        <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($orders as $ord): ?>
            <a href="/track-order/<?= e($ord['order_number']) ?>"
                class="flex items-center justify-between gap-4 px-5 py-4 rounded-xl bg-white/[0.02] border border-white/5 no-underline text-inherit hover:bg-white/[0.04] hover:border-white/10 transition-all duration-200 group">
                <div class="min-w-0">
                    <div class="text-sm font-medium text-text"><?= e($ord['order_number']) ?></div>
                    <div class="text-xs text-muted mt-0.5"><?= date('d M Y', strtotime($ord['created_at'])) ?></div>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <span class="text-sm font-semibold text-success">Rp <?= number_format((float)$ord['total_price'], 0, ',', '.') ?></span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.65rem] font-semibold uppercase tracking-wider"
                        style="background:<?php
                            $sc = ['pending'=>'rgba(245,158,11,0.12)','processing'=>'rgba(79,70,229,0.12)','delivering'=>'rgba(79,70,229,0.12)','completed'=>'rgba(16,185,129,0.12)','cancelled'=>'rgba(239,68,68,0.12)'];
                            echo $sc[$ord['status']] ?? 'rgba(161,161,170,0.12)';
                        ?>;color:<?php
                            $st = ['pending'=>'#f59e0b','processing'=>'#818cf8','delivering'=>'#818cf8','completed'=>'#10b981','cancelled'=>'#ef4444'];
                            echo $st[$ord['status']] ?? '#a1a1aa';
                        ?>">
                        <?= __($ord['status']) ?>
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-muted/30 group-hover:text-gold/50 transition-colors duration-200"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </main>

    <?php component('footer') ?>

    <script src="/assets/js/app.js"></script>
</body>
</html>
