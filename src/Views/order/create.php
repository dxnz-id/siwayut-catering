<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold font-display text-text"><?= htmlspecialchars($title ?? 'Create Order') ?></h1>
    <a href="/orders" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-white/6 text-text border-border hover:bg-white/10 hover:text-text">&larr; Back to Orders</a>
</div>

<div class="bg-[#18181b] border border-border rounded-xl overflow-hidden max-w-[800px]">
    <div class="p-6">
        <form action="/orders" method="POST">
            <?= \App\Core\Csrf::field() ?>
            
            <h4 class="mb-4 pb-2 border-b border-border font-display font-semibold text-text">Customer Details</h4>
            <div class="grid grid-cols-2 gap-4">
                <?php component('form/input', [
                    'name' => 'phone',
                    'label' => 'Phone Number (Member ID)',
                    'placeholder' => '08123456789',
                    'required' => true,
                    'help_text' => 'If registered, name & address will be updated.'
                ]); ?>
                
                <?php component('form/input', [
                    'name' => 'customer_name',
                    'label' => 'Customer Name',
                    'required' => true
                ]); ?>
            </div>

            <?php component('form/input', [
                'name' => 'delivery_address',
                'label' => 'Delivery Address',
                'value' => old('delivery_address'),
                'required' => true
            ]); ?>

            <?php component('form/select', [
                'name' => 'event_id',
                'label' => 'Event',
                'options' => array_column($events ?? [], 'name', 'id'),
                'value' => old('event_id'),
                'required' => true
            ]); ?>

            <h4 class="mt-6 mb-4 pb-2 border-b border-border font-display font-semibold text-text">Order Details</h4>
            <div class="grid grid-cols-[2fr_1fr] gap-4">
                <?php $menuOptions = [];
                foreach ($menus as $m) {
                    $menuOptions[$m['id']] = $m['name'] . ' (Rp ' . number_format((float)$m['price'], 0, ',', '.') . ')';
                }
                component('form/select', [
                    'name' => 'menu_id',
                    'label' => 'Select Menu',
                    'options' => $menuOptions,
                    'placeholder' => '-- Choose Menu --',
                    'required' => true
                ]); 
                ?>

                <?php component('form/input', [
                    'name' => 'quantity',
                    'label' => 'Quantity (Portions)',
                    'type' => 'number',
                    'value' => old('quantity', '1'),
                    'min' => '1',
                    'required' => true
                ]); ?>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <?php component('form/input', [
                    'name' => 'event_date',
                    'label' => 'Event Date & Time',
                    'type' => 'datetime-local',
                    'required' => true
                ]); ?>

                <?php component('form/input', [
                    'name' => 'notes',
                    'label' => 'Additional Notes',
                    'placeholder' => 'e.g. Less spicy'
                ]); ?>
            </div>

            <div class="flex items-center gap-3 mt-6">
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-primary text-white border-primary hover:bg-primary-hover hover:border-primary-hover hover:shadow-[0_0_15px_var(--color-gold-glow)] hover:text-white">Process Order</button>
                <a href="/orders" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-white/6 text-text border-border hover:bg-white/10 hover:text-text">Cancel</a>
            </div>
        </form>
    </div>
</div>
