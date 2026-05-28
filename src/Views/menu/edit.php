<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold font-display text-text">Edit Catering Menu</h1>
    <a href="/menus" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-white/6 text-text border-border hover:bg-white/10 hover:text-text">Back</a>
</div>

<div class="bg-[#18181b] border border-border rounded-xl overflow-hidden">
    <div class="p-6">
        <form action="/menus/<?= e($menu['id']) ?>" method="POST" enctype="multipart/form-data">
            <?= \App\Core\Csrf::field() ?>

            <?php component('form/input', [
                'name' => 'name',
                'label' => 'Menu Name',
                'value' => old('name', $menu['name']),
                'required' => true
            ]); ?>

            <?php component('form/textarea', [
                'name' => 'description',
                'label' => 'Description',
                'value' => old('description', $menu['description']),
                'rows' => 4
            ]); ?>
            <button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 px-3 py-1.5 text-[0.8125rem] bg-white/6 text-text border-border hover:bg-white/10 hover:text-text -mt-3 mb-5" onclick="generateDescription(this)">Generate with AI</button>

            <div class="grid grid-cols-2 gap-4">
                <?php 
                component('form/select', [
                    'name' => 'category_id',
                    'label' => 'Category',
                    'options' => array_column($categories, 'name', 'id'),
                    'selected' => old('category_id', $menu['category_id']),
                    'placeholder' => '-- Select Category --',
                    'required' => true
                ]); 
                ?>

                <?php component('form/select', [
                    'name' => 'event_id',
                    'label' => 'Event',
                    'options' => array_column($events ?? [], 'name', 'id'),
                    'selected' => old('event_id', $menu['event_id'] ?? ''),
                    'placeholder' => '-- Select Event --',
                    'required' => true
                ]); ?>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <?php component('form/select', [
                    'name' => 'status',
                    'label' => 'Status',
                    'options' => ['active' => 'Active', 'inactive' => 'Inactive'],
                    'selected' => old('status', $menu['status']),
                    'required' => true
                ]); ?>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <?php component('form/input', [
                    'name' => 'price',
                    'label' => 'Price (Rp)',
                    'type' => 'number',
                    'value' => old('price', floor((float)$menu['price'])),
                    'required' => true
                ]); ?>

                <?php component('form/input', [
                    'name' => 'minimum_portions',
                    'label' => 'Minimum Portions',
                    'type' => 'number',
                    'value' => old('minimum_portions', $menu['minimum_portions']),
                    'min' => '1',
                    'required' => true
                ]); ?>
            </div>

            <?php if ($menu['image']): ?>
                <div class="mb-5">
                    <label class="block text-sm font-medium text-text mb-1.5">Current Image</label>
                    <div class="flex items-center gap-3 mb-1">
                        <?php component('progressive-image', ['src' => $menu['image'], 'alt' => 'Current Image', 'style' => 'width:80px;height:80px;object-fit:cover;border-radius:var(--radius);border:1px solid var(--color-border)']); ?>
                        <small class="text-muted text-xs">Drop a new file below to replace, or leave empty to keep current.</small>
                    </div>
                </div>
            <?php endif; ?>

            <?php component('form/input', [
                'name' => 'image',
                'label' => $menu['image'] ? 'Replace Image (Optional)' : 'Menu Image (Optional)',
                'type' => 'file',
                'accept' => 'image/jpeg,image/png,image/webp',
                'max_size' => 5 * 1024 * 1024,
            ]); ?>

            <div class="flex items-center gap-3 mt-6">
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-primary text-white border-primary hover:bg-primary-hover hover:border-primary-hover hover:shadow-[0_0_15px_var(--color-gold-glow)] hover:text-white">Save Changes</button>
            </div>
        </form>
    </div>
</div>
