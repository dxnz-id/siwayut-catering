<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold font-display text-text">Add Catering Menu</h1>
    <a href="/menus" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-white/6 text-text border-border hover:bg-white/10 hover:text-text">Back</a>
</div>

<div class="bg-[#18181b] border border-border rounded-xl overflow-hidden">
    <div class="p-6">
        <form action="/menus" method="POST" enctype="multipart/form-data">
            <?= \App\Core\Csrf::field() ?>

            <?php component('form/input', [
                'name' => 'name',
                'label' => 'Menu Name',
                'required' => true
            ]); ?>

            <?php component('form/textarea', [
                'name' => 'description',
                'label' => 'Description',
                'rows' => 4
            ]); ?>
            <button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 px-3 py-1.5 text-[0.8125rem] bg-white/6 text-text border-border hover:bg-white/10 hover:text-text -mt-3 mb-5" onclick="generateDescription(this)">Generate with AI</button>

            <div class="grid grid-cols-2 gap-4">
                <?php component('form/select', [
                    'name' => 'category_id',
                    'label' => 'Category',
                    'options' => array_column($categories, 'name', 'id'),
                    'value' => old('category_id'),
                    'placeholder' => '-- Select Category --',
                    'required' => true
                ]); ?>

                <?php component('form/select', [
                    'name' => 'event_id',
                    'label' => 'Event',
                    'options' => array_column($events ?? [], 'name', 'id'),
                    'value' => old('event_id'),
                    'placeholder' => '-- Select Event --',
                    'required' => true
                ]); ?>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <?php component('form/select', [
                    'name' => 'status',
                    'label' => 'Status',
                    'options' => ['active' => 'Active', 'inactive' => 'Inactive'],
                    'required' => true
                ]); ?>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <?php component('form/input', [
                    'name' => 'price',
                    'label' => 'Price (Rp)',
                    'type' => 'number',
                    'required' => true
                ]); ?>

                <?php component('form/input', [
                    'name' => 'minimum_portions',
                    'label' => 'Minimum Portions',
                    'type' => 'number',
                    'value' => old('minimum_portions', '1'),
                    'min' => '1',
                    'required' => true
                ]); ?>
            </div>

            <?php component('form/input', [
                'name' => 'image',
                'label' => 'Menu Image (Optional)',
                'type' => 'file',
                'accept' => 'image/jpeg,image/png,image/webp',
                'max_size' => 5 * 1024 * 1024,
            ]); ?>

            <div class="flex items-center gap-3 mt-6">
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-primary text-white border-primary hover:bg-primary-hover hover:border-primary-hover hover:shadow-[0_0_15px_var(--color-gold-glow)] hover:text-white">Save Menu</button>
            </div>
        </form>
    </div>
</div>
