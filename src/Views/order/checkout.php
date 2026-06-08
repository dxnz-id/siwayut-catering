<div class="max-w-[1200px] mx-auto px-6 pb-32">
    <h1 class="text-[1.75rem] max-md:text-[1.4rem] font-bold font-display mt-10 mb-8"><?= __('checkout_title') ?></h1>
    <p class="text-sm text-muted -mt-6 mb-8"><?= __('checkout_desc') ?></p>

    <div class="flex gap-8 flex-col lg:flex-row items-start">
        <!-- Left: Form -->
        <div class="w-full lg:w-[68%]">
            <form action="/checkout" method="POST" novalidate target="_blank">
                <?= \App\Core\Csrf::field() ?>

                <div class="bg-card-bg border border-border backdrop-blur-[16px] rounded-xl p-6 space-y-5">
                    <h2 class="text-base font-bold font-display text-text pb-2 border-b border-border"><?= __('customer_details') ?></h2>

                    <div>
                        <label for="name" class="block text-sm font-medium text-muted mb-1"><?= __('full_name') ?> <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name"
                               class="w-full px-4 py-3 bg-white/5 border border-border rounded-xl text-text text-[0.95rem] outline-none transition-all duration-300 placeholder:text-white/20 focus:border-gold focus:ring-[3px] focus:ring-gold/20"
                               placeholder="<?= __('enter_name') ?>" value="<?= e(old('name')) ?>" required>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-muted mb-1"><?= __('phone_label') ?> <span class="text-danger">*</span></label>
                        <input type="tel" id="phone" name="phone"
                               class="w-full px-4 py-3 bg-white/5 border border-border rounded-xl text-text text-[0.95rem] outline-none transition-all duration-300 placeholder:text-white/20 focus:border-gold focus:ring-[3px] focus:ring-gold/20"
                               placeholder="<?= e(__('phone_placeholder')) ?>" value="<?= e(old('phone')) ?>" required minlength="10" maxlength="20">
                        <p class="text-[0.7rem] text-muted mt-1"><?= __('phone_help') ?></p>
                    </div>

                    <div>
                        <label for="event_date" class="block text-sm font-medium text-muted mb-1"><?= __('event_date') ?> <span class="text-danger">*</span></label>
                        <input type="date" id="event_date" name="event_date"
                               class="w-full px-4 py-3 bg-white/5 border border-border rounded-xl text-text text-[0.95rem] outline-none transition-all duration-300 focus:border-gold focus:ring-[3px] focus:ring-gold/20"
                               value="<?= e(old('event_date')) ?>" min="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div>
                        <label for="event_time" class="block text-sm font-medium text-muted mb-1"><?= __('event_time') ?> <span class="text-muted">(<?= __('optional') ?>)</span></label>
                        <input type="time" id="event_time" name="event_time"
                               class="w-full px-4 py-3 bg-white/5 border border-border rounded-xl text-text text-[0.95rem] outline-none transition-all duration-300 focus:border-gold focus:ring-[3px] focus:ring-gold/20"
                               value="<?= e(old('event_time')) ?>">
                        <p class="text-[0.7rem] text-muted mt-1"><?= __('event_time_optional') ?></p>
                    </div>

                    <div>
                        <label for="occasion_select" class="block text-sm font-medium text-muted mb-1"><?= __('occasion') ?> <span class="text-danger">*</span></label>
                        <select id="occasion_select" name="occasion"
                                class="w-full px-4 py-3 bg-white/5 text-text border border-border rounded-xl text-[0.95rem] outline-none transition-all duration-300 focus:border-gold focus:ring-[3px] focus:ring-gold/20"
                                onchange="toggleOccasion(this)">
                            <option value=""><?= __('occasion_placeholder') ?></option>
                            <option value="birthday" <?= old('occasion') === 'birthday' ? 'selected' : '' ?>><?= __('occasion_birthday') ?></option>
                            <option value="wedding" <?= old('occasion') === 'wedding' ? 'selected' : '' ?>><?= __('occasion_wedding') ?></option>
                            <option value="corporate" <?= old('occasion') === 'corporate' ? 'selected' : '' ?>><?= __('occasion_corporate') ?></option>
                            <option value="family" <?= old('occasion') === 'family' ? 'selected' : '' ?>><?= __('occasion_family') ?></option>
                            <option value="arisan" <?= old('occasion') === 'arisan' ? 'selected' : '' ?>><?= __('occasion_arisan') ?></option>
                            <option value="khitanan" <?= old('occasion') === 'khitanan' ? 'selected' : '' ?>><?= __('occasion_khitanan') ?></option>
                            <option value="__other__" <?= old('occasion') === '__other__' ? 'selected' : '' ?>><?= __('occasion_other') ?></option>
                        </select>
                        <input type="text" id="occasion_custom" name="occasion_custom" value="<?= e(old('occasion_custom')) ?>"
                               placeholder="<?= __('occasion_custom_placeholder') ?>"
                               class="w-full px-4 py-3 bg-white/5 border border-border rounded-xl text-text text-[0.95rem] outline-none transition-all duration-300 focus:border-gold focus:ring-[3px] focus:ring-gold/20 mt-2"
                               style="display:none">
                    </div>

                    <div>
                        <label for="address" class="block text-sm font-medium text-muted mb-1"><?= __('delivery_address') ?> <span class="text-danger">*</span></label>
                        <textarea id="address" name="address" required
                                  class="w-full px-4 py-3 bg-white/5 border border-border rounded-xl text-text text-[0.95rem] outline-none transition-all duration-300 placeholder:text-white/20 focus:border-gold focus:ring-[3px] focus:ring-gold/20 min-h-[100px] resize-vertical"
                                  placeholder="<?= __('enter_address') ?>"><?= e(old('address')) ?></textarea>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-muted mb-1"><?= __('notes') ?> <span class="text-muted">(<?= __('optional') ?>)</span></label>
                        <textarea id="notes" name="notes"
                                  class="w-full px-4 py-3 bg-white/5 border border-border rounded-xl text-text text-[0.95rem] outline-none transition-all duration-300 placeholder:text-white/20 focus:border-gold focus:ring-[3px] focus:ring-gold/20 min-h-[80px] resize-vertical"
                                  placeholder="<?= __('notes_placeholder') ?>"><?= e(old('notes')) ?></textarea>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-6">
                    <p class="text-xs text-muted"><?= __('whatsapp_redirect_note') ?></p>
                    <button type="submit"
                            class="w-full sm:w-auto px-8 py-3 rounded-xl text-sm font-semibold bg-gold border border-gold text-white shadow-[0_0_12px_var(--color-gold-glow)] hover:bg-primary-hover hover:border-primary-hover hover:-translate-y-0.5 hover:shadow-[0_0_20px_var(--color-gold-glow)] transition-all duration-300 flex items-center justify-center gap-2 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                        </svg>
                        <?= __('send_via_whatsapp') ?>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Order Summary (desktop) -->
        <div class="w-full lg:w-[32%] shrink-0 lg:sticky lg:top-24 self-start hidden lg:block">
            <div class="bg-card-bg border border-border backdrop-blur-[16px] rounded-xl p-6">
                <h3 class="text-sm font-semibold text-muted uppercase tracking-widest mb-5"><?= __('order_summary') ?></h3>

                <div class="space-y-4">
                    <?php foreach ($items as $item): ?>
                    <div class="flex items-start gap-3">
                        <div class="shrink-0 w-10 h-10 rounded-lg overflow-hidden bg-gradient-to-br from-gold/20 to-accent-red/10 flex items-center justify-center text-white/15 text-xs">
                            <?php if ($item['image']): ?>
                                <?php component('progressive-image', ['src' => $item['image'], 'alt' => $item['name'], 'class' => 'w-full h-full']) ?>
                            <?php else: ?>
                                <span>🍱</span>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-text truncate"><?= e($item['name']) ?></p>
                            <p class="text-xs text-muted"><?= (int) $item['quantity'] ?> × Rp<?= number_format((float) $item['price'], 0, ',', '.') ?></p>
                        </div>
                        <span class="text-sm font-semibold text-gold whitespace-nowrap">Rp<?= number_format((float) $item['subtotal'], 0, ',', '.') ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="border-t border-white/5 mt-5 pt-4 space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-muted"><?= __('subtotal') ?></span>
                        <span class="text-text font-semibold">Rp<?= number_format((float) $total, 0, ',', '.') ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-muted"><?= __('total_items') ?></span>
                        <span class="text-text font-semibold"><?= $cartCount ?></span>
                    </div>
                </div>

                <div class="border-t border-white/5 mt-4 pt-4">
                    <div class="flex items-center justify-between">
                        <span class="text-text font-bold font-display"><?= __('total') ?></span>
                        <span class="text-lg font-bold text-gold font-display">Rp<?= number_format((float) $total, 0, ',', '.') ?></span>
                    </div>
                </div>

                <div class="mt-4 px-4 py-3 rounded-lg bg-white/5 border border-border/50">
                    <p class="text-xs text-muted leading-relaxed">
                        <?= __('whatsapp_redirect_note') ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleOccasion(sel) {
    var custom = document.getElementById('occasion_custom');
    if (!custom) return;
    if (sel.value === '__other__') {
        custom.style.display = '';
        custom.name = 'occasion';
        sel.name = '';
        custom.focus();
    } else if (sel.value !== '') {
        custom.style.display = 'none';
        custom.name = '';
        custom.value = '';
        sel.name = 'occasion';
    } else {
        custom.style.display = 'none';
        custom.name = '';
        custom.value = '';
        sel.name = 'occasion';
    }
}
(function() {
    var sel = document.getElementById('occasion_select');
    if (sel) toggleOccasion(sel);
})();
</script>

