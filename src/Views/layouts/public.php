<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    if (\App\Core\Turnstile::enabled()) {
        $headExtra = '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>';
    }
    require __DIR__ . '/../partials/head.php' ?>
</head>

<body class="bg-bg text-text min-h-screen flex flex-col leading-relaxed font-body overflow-x-hidden bg-fixed bg-[radial-gradient(circle_at_15%_25%,rgba(229,142,38,0.12)_0%,transparent_45%),radial-gradient(circle_at_85%_75%,rgba(234,32,39,0.08)_0%,transparent_45%)]">

    <header class="sticky top-0 z-[100] bg-bg/60 backdrop-blur-[12px] border-b border-border py-4">
        <div class="max-w-[1200px] mx-auto px-6 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 no-underline text-text">
                <span class="text-[1.8rem] drop-shadow-[0_0_8px_var(--accent-gold-glow)]">🍲</span>
                <span class="font-display text-2xl font-bold tracking-tight bg-gradient-to-r from-white to-gold bg-clip-text text-transparent">Siwayut Catering</span>
            </a>
            <div class="flex items-center gap-3">
                <?php component('lang-switcher') ?>
                <?= $navExtra ?? '' ?>
            </div>
        </div>
    </header>

    <main class="flex-1">
        <?= $content ?? '' ?>
    </main>

    <?php component('footer') ?>
    <?php component('toast') ?>

    <?php if (\App\Core\Turnstile::enabled()): ?>
        <script src="/assets/js/modules/turnstile.js"></script>
    <?php endif; ?>
    <script src="/assets/js/modules/toast.js"></script>
    <script src="/assets/js/modules/progressive-image.js"></script>
    <?= $scriptsExtra ?? '' ?>
    <script src="/assets/js/app.js"></script>
</body>

</html>