<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \App\Core\View::e($title ?? 'Track Order — Siwayut Catering') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/app.css?v=3">
    <?php if (\App\Core\Turnstile::enabled()): ?>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <?php endif; ?>
</head>

<body class="bg-bg text-text min-h-screen leading-relaxed font-body overflow-x-hidden bg-fixed bg-[radial-gradient(circle_at_15%_25%,rgba(229,142,38,0.12)_0%,transparent_45%),radial-gradient(circle_at_85%_75%,rgba(234,32,39,0.08)_0%,transparent_45%)]">

    <header class="sticky top-0 z-[100] bg-bg/60 backdrop-blur-[12px] border-b border-border py-4">
        <div class="max-w-[1200px] mx-auto px-6 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 no-underline text-text">
                <span class="text-[1.8rem] drop-shadow-[0_0_8px_var(--accent-gold-glow)]">🍲</span>
                <span class="font-display text-2xl font-bold tracking-tight bg-gradient-to-r from-white to-gold bg-clip-text text-transparent">Siwayut Catering</span>
            </a>
            <a href="/" class="inline-flex items-center gap-2 px-5 py-2 rounded-full text-sm font-medium no-underline bg-white/5 border border-border text-text backdrop-blur-[8px] hover:bg-gold hover:border-gold hover:shadow-[0_0_15px_var(--color-gold-glow)] transition-all duration-300">&larr; Home</a>
        </div>
    </header>

    <main class="max-w-[540px] mx-auto px-6">
        <div class="bg-card-bg border border-border backdrop-blur-[16px] rounded-xl p-10 px-8 max-md:p-6 max-md:px-5 mt-16">
            <h1 class="text-center text-[1.75rem] max-md:text-[1.4rem] font-bold mb-2 font-display">Track Order</h1>
            <p class="text-center text-muted text-sm mb-8">Enter your order number and phone number to check the latest status.</p>

            <form action="/track-order" method="POST">
                <?= \App\Core\Csrf::field() ?>
                <div class="mb-5">
                    <label for="order_number" class="block text-sm font-medium mb-1 text-muted">Order Number</label>
                    <input type="text" id="order_number" name="order_number"
                        class="w-full px-4 py-3 bg-white/5 border border-border rounded-xl font-body leading-relaxed text-text text-[0.95rem] outline-none transition-all duration-300 placeholder:text-white/20 focus:border-gold focus:ring-[3px] focus:ring-gold/20"
                        placeholder="E.g. ORD-20231124-0001"
                        value="<?= \App\Core\View::e(old('order_number')) ?>" required>
                </div>
                <div class="mb-5">
                    <label for="phone" class="block text-sm font-medium mb-1 text-muted">Phone Number (when ordering)</label>
                    <input type="tel" id="phone" name="phone"
                        class="w-full px-4 py-3 bg-white/5 border border-border rounded-xl font-body leading-relaxed text-text text-[0.95rem] outline-none transition-all duration-300 placeholder:text-white/20 focus:border-gold focus:ring-[3px] focus:ring-gold/20"
                        placeholder="E.g. 08123456789"
                        value="<?= \App\Core\View::e(old('phone')) ?>" required>
                </div>
                <?php if (\App\Core\Turnstile::enabled()): ?>
                    <div class="mt-6 flex justify-center">
                        <?= \App\Core\Turnstile::widget() ?>
                    </div>
                <?php endif; ?>

                <button type="submit" id="submit-btn"
                    data-turnstile-submit="1"
                    <?= \App\Core\Turnstile::enabled() ? 'disabled' : '' ?>
                    class="w-full py-[0.85rem] bg-gold border border-gold rounded-xl text-white text-base font-semibold cursor-pointer transition-all duration-300 flex items-center justify-center gap-2 shadow-[0_0_15px_var(--color-gold-glow)] hover:bg-primary-hover hover:border-primary-hover hover:-translate-y-0.5 hover:shadow-[0_0_25px_var(--color-gold-glow)] font-body">
                    Search Order
                </button>
            </form>
        </div>
        <div class="text-center text-muted text-xs mt-8 pb-8">
            <a href="/" class="text-gold no-underline hover:text-gold">Siwayut Catering</a> — Exquisite Taste For Your Most Sacred Moments
        </div>
    </main>
    <?php
    $flashError = \App\Core\Session::getFlash('error');
    $pageFlashes = [];
    if ($flashError) $pageFlashes[] = ['type' => 'error', 'message' => $flashError];
    ?>
    <script src="/assets/js/modules/turnstile.js"></script>
    <script src="/assets/js/modules/toast.js"></script>
    <script src="/assets/js/modules/file-upload.js"></script>
    <script src="/assets/js/modules/progressive-image.js"></script>
    <script src="/assets/js/modules/load-more-menu.js"></script>
    <script src="/assets/js/modules/ai-description.js"></script>
    <?php component('toast', ['flashes' => $pageFlashes]) ?>
    <script src="/assets/js/app.js"></script>
</body>

</html>
