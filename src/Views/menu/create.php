<div class="content-header">
    <h1 class="content-title">Add Catering Menu</h1>
    <a href="/menus" class="btn btn-secondary">Back</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="/menus" method="POST" enctype="multipart/form-data">
            <?= \App\Core\Csrf::field() ?>

            <?php component('form/input', [
                'name' => 'name',
                'label' => 'Menu Name *',
                'required' => true
            ]); ?>

            <?php component('form/textarea', [
                'name' => 'description',
                'label' => 'Description',
                'rows' => 4
            ]); ?>
            <button type="button" class="btn btn-sm btn-secondary" style="margin-top: -0.75rem; margin-bottom: 1.25rem;" onclick="generateDescription(this)">Generate with AI</button>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <?php component('form/select', [
                    'name' => 'category_id',
                    'label' => 'Category *',
                    'options' => array_column($categories, 'name', 'id'),
                    'value' => old('category_id'),
                    'placeholder' => '-- Select Category --',
                    'required' => true
                ]); ?>

                <?php component('form/select', [
                    'name' => 'event_id',
                    'label' => 'Event *',
                    'options' => array_column($events ?? [], 'name', 'id'),
                    'value' => old('event_id'),
                    'placeholder' => '-- Select Event --',
                    'required' => true
                ]); ?>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <?php component('form/select', [
                    'name' => 'status',
                    'label' => 'Status *',
                    'options' => ['active' => 'Active', 'inactive' => 'Inactive'],
                    'required' => true
                ]); ?>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <?php component('form/input', [
                    'name' => 'price',
                    'label' => 'Price (Rp) *',
                    'type' => 'number',
                    'required' => true
                ]); ?>

                <?php component('form/input', [
                    'name' => 'minimum_portions',
                    'label' => 'Minimum Portions *',
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

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Menu</button>
            </div>
        </form>
    </div>
</div>
