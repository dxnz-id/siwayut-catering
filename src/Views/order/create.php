<div class="content-header">
    <h1 class="content-title"><?= htmlspecialchars($title ?? 'Create Order') ?></h1>
    <a href="/orders" class="btn btn-secondary">&larr; Back to Orders</a>
</div>

<div class="card" style="max-width: 800px;">
    <div class="card-body">
        <form action="/orders" method="POST">
            <?= \App\Core\Csrf::field() ?>
            
            <h4 style="margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--color-border);">Customer Details</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
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

            <h4 style="margin-top: 1.5rem; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--color-border);">Order Details</h4>
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem;">
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

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
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

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Process Order</button>
                <a href="/orders" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
