

    <main class="max-w-[540px] mx-auto px-6">
        <div class="bg-card-bg border border-border backdrop-blur-[16px] rounded-xl p-10 px-8 max-md:p-6 max-md:px-5 mt-16">
            <h1 class="text-center text-[1.75rem] max-md:text-[1.4rem] font-bold mb-2 font-display"><?= __('track_order_title') ?></h1>
            <p class="text-center text-muted text-sm mb-8"><?= __('track_order_desc') ?></p>

            <form action="/track-order" method="POST">
                <?= \App\Core\Csrf::field() ?>
                <div class="mb-5">
                    <label for="order_number" class="block text-sm font-medium mb-1 text-muted"><?= __('order_number') ?></label>
                    <input type="text" id="order_number" name="order_number"
                        class="w-full px-4 py-3 bg-white/5 border border-border rounded-xl font-body leading-relaxed text-text text-[0.95rem] outline-none transition-all duration-300 placeholder:text-white/20 focus:border-gold focus:ring-[3px] focus:ring-gold/20"
                        placeholder="<?= __('order_number_placeholder') ?>"
                        value="<?= \App\Core\View::e(old('order_number')) ?>" required>
                </div>
                <div class="mb-5">
                    <label for="phone" class="block text-sm font-medium mb-1 text-muted"><?= __('phone_when_ordering') ?></label>
                    <input type="tel" id="phone" name="phone"
                        class="w-full px-4 py-3 bg-white/5 border border-border rounded-xl font-body leading-relaxed text-text text-[0.95rem] outline-none transition-all duration-300 placeholder:text-white/20 focus:border-gold focus:ring-[3px] focus:ring-gold/20"
                        placeholder="<?= __('phone_placeholder') ?>"
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
                    <?= __('search_order_btn') ?>
                </button>
            </form>
        </div>
    </main>

