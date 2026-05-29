<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold font-display text-text"><?= e($headerTitle) ?></h1>
    <?php if (!empty($createUrl)): ?>
    <a href="<?= e($createUrl) ?>" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-primary text-white border-primary hover:bg-primary-hover hover:border-primary-hover hover:shadow-[0_0_15px_var(--color-gold-glow)] hover:text-white">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        <?= e($createLabel ?? 'Add') ?></a>
    <?php endif; ?>
</div>
