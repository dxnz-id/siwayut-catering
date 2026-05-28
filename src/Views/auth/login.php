<!-- File: src/Views/auth/login.php -->
<div
    class="bg-[#18181b] border border-white/10 rounded-[24px] shadow-[0_25px_50px_-12px_rgba(0,0,0,0.5)] w-full max-w-[420px] p-10">
    <div class="text-center mb-8">
        <h1
            class="text-2xl font-extrabold text-white mb-1 font-display bg-gradient-to-r from-white to-gold bg-clip-text text-transparent">
            <?= \App\Core\View::e(APP_NAME) ?></h1>
        <p class="text-sm text-muted">Sign in to your account</p>
    </div>

    <?php if (!empty($error)): ?>
        <div
            class="flex items-center gap-2 px-4 py-3 rounded-lg text-sm mb-6 bg-error-bg text-[#fca5a5] border border-error-border">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="2"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
            <?= \App\Core\View::e($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/login">
        <?= \App\Core\Csrf::field() ?>

        <div class="mb-5">
            <label for="email" class="block text-sm font-medium text-text mb-1.5">Email Address</label>
            <input type="email" id="email" name="email"
                class="w-full px-3 py-2 border border-border rounded-lg text-sm leading-relaxed text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light"
                value="<?= \App\Core\View::e(old('email')) ?>" placeholder="admin@example.com" required autofocus>
        </div>

        <div class="mb-5">
            <label for="password" class="block text-sm font-medium text-text mb-1.5">Password</label>
            <input type="password" id="password" name="password"
                class="w-full px-3 py-2 border border-border rounded-lg text-sm leading-relaxed text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light"
                placeholder="••••••••" required>
        </div>

        <?php if (\App\Core\Turnstile::enabled()): ?>
            <div class="mt-6 flex justify-center">
                <?= \App\Core\Turnstile::widget() ?>
            </div>
        <?php endif; ?>

        <div class="flex items-center gap-3 mt-6">
            <button type="submit" id="submit-btn" data-turnstile-submit="1" <?= \App\Core\Turnstile::enabled() ? 'disabled' : '' ?>
                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-primary text-white border-primary hover:bg-primary-hover hover:border-primary-hover hover:shadow-[0_0_15px_var(--color-gold-glow)] hover:text-white w-full">
                Sign In
            </button>
        </div>
    </form>
</div>