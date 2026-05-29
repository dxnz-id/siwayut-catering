<div class="mb-4">
    <form method="GET" class="<?= ($showFilter ?? true) ? 'flex items-center gap-3 relative' : 'relative' ?>">
        <input type="hidden" name="page" value="1">
        <div class="relative flex-1">
            <input type="text" name="search" value="<?= e($search ?? '') ?>" placeholder="<?= e($searchPlaceholder ?? 'Search...') ?>" class="w-full px-4 py-2.5 pl-10 border border-border rounded-lg text-sm text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted pointer-events-none" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
        </div>
        <?php if ($showFilter ?? true): ?>
        <button type="button" onclick="this.nextElementSibling.classList.toggle('hidden')" class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-border bg-black/40 text-muted hover:text-text hover:border-primary transition-all duration-150 shrink-0" title="Filters">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75"/></svg>
        </button>
        <div class="hidden p-4 bg-[#18181b] border border-border rounded-xl absolute mt-2 right-0 top-full z-10 min-w-[<?= e($filterMinWidth ?? '280px') ?>] shadow-lg">
            <div class="flex items-end gap-3 flex-wrap">
                <?php foreach ($filters ?? [] as $filter): ?>
                <div>
                    <label class="block text-xs font-medium text-muted mb-1"><?= e($filter['label']) ?></label>
                    <select name="<?= e($filter['name']) ?>" class="px-3 py-2 border border-border rounded-lg text-sm text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light min-w-[140px]">
                        <?php foreach ($filter['options'] as $val => $label): ?>
                        <option value="<?= e((string)$val) ?>" <?= ($filter['selected'] ?? '') == $val ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endforeach; ?>
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-primary text-white border-primary hover:bg-primary-hover hover:border-primary-hover hover:shadow-[0_0_15px_var(--color-gold-glow)] hover:text-white">Apply</button>
            </div>
        </div>
        <?php endif; ?>
    </form>
</div>
