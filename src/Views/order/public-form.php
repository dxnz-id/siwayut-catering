<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \App\Core\View::e($title ?? 'Order Catering — Siwayut Catering') ?></title>
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

    <main class="max-w-[600px] mx-auto px-6">
        <div class="bg-card-bg border border-border backdrop-blur-[16px] rounded-xl p-10 px-8 max-md:p-6 max-md:px-5 mt-10">
            <h1 class="text-center text-[1.75rem] max-md:text-[1.4rem] font-bold mb-2 font-display">Order Catering</h1>
            <p class="text-center text-muted text-sm mb-8">Fill out the form below, then your order will be sent to us via WhatsApp.</p>

            <form action="/order-form" method="POST">
                <?= \App\Core\Csrf::field() ?>

                <div class="mb-5">
                    <label for="name" class="block text-sm font-medium mb-1 text-muted">Full Name</label>
                    <input type="text" id="name" name="name"
                        class="w-full px-4 py-3 bg-white/5 border border-border rounded-xl text-text text-[0.95rem] outline-none transition-all duration-300 placeholder:text-white/20 focus:border-gold focus:ring-[3px] focus:ring-gold/20"
                        placeholder="Enter your name"
                        value="<?= \App\Core\View::e(old('name')) ?>" required>
                </div>

                <div class="mb-5">
                    <label for="menu" class="block text-sm font-medium mb-1 text-muted">Menu</label>
                    <select id="menu" name="menu"
                        class="w-full px-4 py-3 bg-[#1a1a1e] text-[#f4f4f5] border border-border rounded-xl text-[0.95rem] outline-none transition-all duration-300 focus:border-gold focus:ring-[3px] focus:ring-gold/20"
                        required>
                        <option value="">— Select Menu —</option>
                        <?php foreach ($menus as $m): ?>
                            <option value="<?= \App\Core\View::e($m['name']) ?>" <?= old('menu') === $m['name'] ? 'selected' : '' ?>>
                                <?= \App\Core\View::e($m['name']) ?> — Rp
                                <?= number_format((float) $m['price'], 0, ',', '.') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-5">
                    <label for="event_date" class="block text-sm font-medium mb-1 text-muted">Event Date</label>
                    <input type="datetime-local" id="event_date" name="event_date"
                        class="w-full px-4 py-3 bg-white/5 border border-border rounded-xl text-text text-[0.95rem] outline-none transition-all duration-300 placeholder:text-white/20 focus:border-gold focus:ring-[3px] focus:ring-gold/20"
                        value="<?= \App\Core\View::e(old('event_date')) ?>" required>
                </div>

                <div class="mb-5">
                    <label for="quantity" class="block text-sm font-medium mb-1 text-muted">Portions</label>
                    <input type="number" id="quantity" name="quantity"
                        class="w-full px-4 py-3 bg-white/5 border border-border rounded-xl text-text text-[0.95rem] outline-none transition-all duration-300 placeholder:text-white/20 focus:border-gold focus:ring-[3px] focus:ring-gold/20"
                        placeholder="E.g. 50" min="1"
                        value="<?= \App\Core\View::e(old('quantity')) ?>" required>
                </div>

                <div class="mb-5">
                    <label for="address" class="block text-sm font-medium mb-1 text-muted">Delivery Address</label>
                    <textarea id="address" name="address"
                        class="w-full px-4 py-3 bg-white/5 border border-border rounded-xl text-text text-[0.95rem] outline-none transition-all duration-300 placeholder:text-white/20 focus:border-gold focus:ring-[3px] focus:ring-gold/20 min-h-[100px] resize-vertical"
                        placeholder="Enter your complete delivery address"
                        required><?= \App\Core\View::e(old('address')) ?></textarea>
                </div>

                <div class="mb-5">
                    <label for="notes" class="block text-sm font-medium mb-1 text-muted">Notes <span class="text-muted">(optional)</span></label>
                    <textarea id="notes" name="notes"
                        class="w-full px-4 py-3 bg-white/5 border border-border rounded-xl text-text text-[0.95rem] outline-none transition-all duration-300 placeholder:text-white/20 focus:border-gold focus:ring-[3px] focus:ring-gold/20 min-h-[100px] resize-vertical"
                        placeholder="E.g. additional requests, delivery time, etc."><?= \App\Core\View::e(old('notes')) ?></textarea>
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                        fill="currentColor">
                        <path
                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                    </svg>
                    Send Order via WhatsApp
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
