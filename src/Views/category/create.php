<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold font-display text-text"><?= htmlspecialchars($title ?? 'Add Category') ?></h1>
    <a href="/categories" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-white/6 text-text border-border hover:bg-white/10 hover:text-text">&larr; Back to Categories</a>
</div>

<div class="bg-[#18181b] border border-border rounded-xl overflow-hidden max-w-[600px]">
    <div class="p-6">
        <form action="/categories" method="POST">
            <?= \App\Core\Csrf::field() ?>
            
            <?php component('form/input', [
                'name' => 'name',
                'label' => 'Category Name',
                'required' => true
            ]); ?>

            <div class="flex items-center gap-3 mt-6">
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-primary text-white border-primary hover:bg-primary-hover hover:border-primary-hover hover:shadow-[0_0_15px_var(--color-gold-glow)] hover:text-white">Save Category</button>
                <a href="/categories" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-white/6 text-text border-border hover:bg-white/10 hover:text-text">Cancel</a>
            </div>
        </form>
    </div>
</div>
